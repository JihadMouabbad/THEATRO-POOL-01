<?php

namespace App\Http\Controllers;

use App\Models\PoolMatch;
use App\Models\Tournament;
use App\Models\Player;
use App\Services\BracketGenerator;
use App\Services\MatchManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * MatchController handles match result updates.
 */
class MatchController extends Controller
{
    /**
     * @var BracketGenerator
     */
    protected BracketGenerator $bracketGenerator;

    /**
     * @var MatchManager
     */
    protected MatchManager $matchManager;

    /**
     * Create a new controller instance.
     *
     * @param BracketGenerator $bracketGenerator
     * @param MatchManager $matchManager
     */
    public function __construct(BracketGenerator $bracketGenerator, MatchManager $matchManager)
    {
        $this->bracketGenerator = $bracketGenerator;
        $this->matchManager = $matchManager;
    }

    /**
     * Show the match result form.
     *
     * @param PoolMatch $match
     * @return View|RedirectResponse
     */
    public function edit(PoolMatch $match): View|RedirectResponse
    {
        $match->load(['tournament', 'player1', 'player2', 'winner']);

        if (!$match->hasBothPlayers()) {
            return redirect()->route('tournaments.show', $match->tournament)
                ->with('error', 'Both players must be set before updating match result.');
        }

        return view('matches.edit', compact('match'));
    }

    /**
     * Update the match result.
     *
     * @param Request $request
     * @param PoolMatch $match
     * @return RedirectResponse
     */
    public function update(Request $request, PoolMatch $match): RedirectResponse
    {
        if ($match->isCompleted()) {
            return redirect()->route('tournaments.show', $match->tournament)
                ->with('error', 'This match has already been completed.');
        }

        if (!$match->hasBothPlayers()) {
            return redirect()->route('tournaments.show', $match->tournament)
                ->with('error', 'Both players must be set before updating match result.');
        }

        $validated = $request->validate([
            'player1_score' => ['required', 'integer', 'min:0', 'max:100'],
            'player2_score' => ['required', 'integer', 'min:0', 'max:100', 'different:player1_score'],
        ], [
            'player2_score.different' => 'The match cannot end in a tie. One player must win.',
        ]);

        try {
            $this->bracketGenerator->processMatchResult(
                $match,
                $validated['player1_score'],
                $validated['player2_score']
            );

            return redirect()->route('tournaments.show', $match->tournament)
                ->with('success', 'Match result updated successfully.');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the match details.
     *
     * @param PoolMatch $match
     * @return View
     */
    public function show(PoolMatch $match): View
    {
        $match->load(['tournament', 'player1', 'player2', 'winner', 'nextMatch']);

        return view('matches.show', compact('match'));
    }

    /**
     * Admin override for match result.
     *
     * @param Request $request
     * @param PoolMatch $match
     * @return RedirectResponse
     */
    public function override(Request $request, PoolMatch $match): RedirectResponse
    {
        $validated = $request->validate([
            'winner_id' => 'required|in:' . $match->player1_id . ',' . $match->player2_id,
            'player1_score' => 'required|integer|min:0|max:255',
            'player2_score' => 'required|integer|min:0|max:255',
        ]);

        try {
            $this->matchManager->overrideResult(
                $match,
                $validated['winner_id'],
                $validated['player1_score'],
                $validated['player2_score']
            );

            return redirect()->route('tournaments.show', $match->tournament)
                ->with('success', 'Match result overridden successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to override result: ' . $e->getMessage());
        }
    }

    /**
     * Schedule a match.
     *
     * @param Request $request
     * @param PoolMatch $match
     * @return RedirectResponse
     */
    public function schedule(Request $request, PoolMatch $match): RedirectResponse
    {
        $validated = $request->validate([
            'scheduled_at' => 'required|date_format:Y-m-d H:i',
            'table_number' => 'nullable|integer|min:1|max:99',
            'referee_id' => 'nullable|exists:players,id',
        ]);

        try {
            $this->matchManager->scheduleMatch(
                $match,
                \Carbon\Carbon::createFromFormat('Y-m-d H:i', $validated['scheduled_at']),
                $validated['table_number'],
                $validated['referee_id']
            );

            return back()->with('success', 'Match scheduled successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to schedule match: ' . $e->getMessage());
        }
    }

    /**
     * Start a match (change to live mode).
     *
     * @param PoolMatch $match
     * @return RedirectResponse
     */
    public function start(PoolMatch $match): RedirectResponse
    {
        try {
            $this->matchManager->startMatch($match);

            return back()->with('success', 'Match started. Now in live mode.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to start match: ' . $e->getMessage());
        }
    }

    /**
     * Display live match mode (full screen, auto-refresh).
     *
     * @param PoolMatch $match
     * @return View
     */
    public function liveMode(PoolMatch $match): View
    {
        $match->load('player1', 'player2', 'winner', 'referee');

        return view('matches.live-mode', compact('match'));
    }

    /**
     * Get match data as JSON for live updates.
     *
     * @param PoolMatch $match
     * @return \Illuminate\Http\JsonResponse
     */
    public function getData(PoolMatch $match)
    {
        $match->load('player1', 'player2', 'winner', 'referee');

        return response()->json([
            'id' => $match->id,
            'round' => $match->round,
            'match_number' => $match->match_number,
            'player1' => [
                'id' => $match->player1?->id,
                'name' => $match->player1?->display_name,
                'score' => $match->player1_score,
            ],
            'player2' => [
                'id' => $match->player2?->id,
                'name' => $match->player2?->display_name,
                'score' => $match->player2_score,
            ],
            'winner_id' => $match->winner_id,
            'status' => $match->status,
            'scheduled_at' => $match->scheduled_at,
            'table_number' => $match->table_number,
            'referee' => $match->referee?->display_name,
            'match_format' => $match->match_format,
            'frames_to_win' => $match->frames_to_win,
            'is_admin_override' => $match->is_admin_override,
        ]);
    }

    /**
     * Get all matches for a tournament as JSON.
     *
     * @param Tournament $tournament
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllMatches(Tournament $tournament)
    {
        $matches = $tournament->matches()
            ->with('player1', 'player2', 'winner', 'referee')
            ->orderBy('round')
            ->orderBy('match_number')
            ->get()
            ->map(function ($match) {
                return [
                    'id' => $match->id,
                    'round' => $match->round,
                    'match_number' => $match->match_number,
                    'player1_name' => $match->player1?->display_name ?? 'TBD',
                    'player2_name' => $match->player2?->display_name ?? 'TBD',
                    'player1_score' => $match->player1_score,
                    'player2_score' => $match->player2_score,
                    'winner_id' => $match->winner_id,
                    'status' => $match->status,
                    'table_number' => $match->table_number,
                ];
            });

        return response()->json($matches);
    }

    /**
     * Get pending matches for a tournament.
     *
     * @param Tournament $tournament
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPendingMatches(Tournament $tournament)
    {
        $matches = $this->matchManager->getPendingMatches($tournament)
            ->map(function ($match) {
                return [
                    'id' => $match->id,
                    'round' => $match->round,
                    'match_number' => $match->match_number,
                    'player1_name' => $match->player1->display_name,
                    'player2_name' => $match->player2->display_name,
                    'status' => $match->status,
                ];
            });

        return response()->json($matches);
    }

    /**
     * Get player match history in a tournament.
     *
     * @param Tournament $tournament
     * @param Player $player
     * @return \Illuminate\Http\JsonResponse
     */
    public function playerHistory(Tournament $tournament, Player $player)
    {
        $stats = $this->matchManager->getPlayerStats($tournament, $player);
        $matches = $tournament->matches()
            ->where(function ($query) use ($player) {
                $query->where('player1_id', $player->id)
                    ->orWhere('player2_id', $player->id);
            })
            ->where('status', 'completed')
            ->with('player1', 'player2', 'winner')
            ->orderBy('completed_at', 'desc')
            ->get()
            ->map(function ($match) use ($player) {
                return [
                    'round' => $match->round,
                    'opponent' => $match->player1_id === $player->id
                        ? $match->player2->display_name
                        : $match->player1->display_name,
                    'result' => $match->winner_id === $player->id ? 'Win' : 'Loss',
                    'score' => $match->player1_id === $player->id
                        ? "{$match->player1_score}-{$match->player2_score}"
                        : "{$match->player2_score}-{$match->player1_score}",
                    'date' => $match->completed_at,
                ];
            });

        return response()->json([
            'stats' => $stats,
            'matches' => $matches,
        ]);
    }
}
