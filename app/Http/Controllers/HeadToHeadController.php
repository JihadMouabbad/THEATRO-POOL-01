<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\PoolMatch;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * HeadToHeadController handles player comparison features.
 */
class HeadToHeadController extends Controller
{
    /**
     * Show the head-to-head comparison form.
     *
     * @return View
     */
    public function index(): View
    {
        $players = Player::orderBy('name')->get();
        return view('head-to-head.index', compact('players'));
    }

    /**
     * Compare two players head-to-head.
     *
     * @param Request $request
     * @return View
     */
    public function compare(Request $request): View
    {
        $validated = $request->validate([
            'player1_id' => ['required', 'exists:players,id', 'different:player2_id'],
            'player2_id' => ['required', 'exists:players,id'],
        ]);

        $player1 = Player::findOrFail($validated['player1_id']);
        $player2 = Player::findOrFail($validated['player2_id']);
        $players = Player::orderBy('name')->get();

        // Get all matches between these two players
        $matches = PoolMatch::where(function ($query) use ($player1, $player2) {
            $query->where('player1_id', $player1->id)
                  ->where('player2_id', $player2->id);
        })->orWhere(function ($query) use ($player1, $player2) {
            $query->where('player1_id', $player2->id)
                  ->where('player2_id', $player1->id);
        })
        ->whereNotNull('winner_id')
        ->with(['tournament', 'winner'])
        ->orderBy('updated_at', 'desc')
        ->get();

        // Calculate head-to-head stats
        $player1Wins = $matches->where('winner_id', $player1->id)->count();
        $player2Wins = $matches->where('winner_id', $player2->id)->count();
        $totalMatches = $matches->count();

        // Calculate total scores using helper method
        [$player1TotalScore, $player2TotalScore] = $this->calculateTotalScores($matches, $player1->id);

        $stats = [
            'player1_wins' => $player1Wins,
            'player2_wins' => $player2Wins,
            'total_matches' => $totalMatches,
            'player1_total_score' => $player1TotalScore,
            'player2_total_score' => $player2TotalScore,
        ];

        return view('head-to-head.index', compact('players', 'player1', 'player2', 'matches', 'stats'));
    }

    /**
     * Calculate total scores for player1 across all matches.
     *
     * @param \Illuminate\Support\Collection $matches
     * @param int $player1Id
     * @return array{0: int, 1: int} [player1TotalScore, player2TotalScore]
     */
    private function calculateTotalScores($matches, int $player1Id): array
    {
        $player1TotalScore = 0;
        $player2TotalScore = 0;

        foreach ($matches as $match) {
            $isPlayer1First = $match->player1_id === $player1Id;
            $player1TotalScore += $isPlayer1First ? ($match->player1_score ?? 0) : ($match->player2_score ?? 0);
            $player2TotalScore += $isPlayer1First ? ($match->player2_score ?? 0) : ($match->player1_score ?? 0);
        }

        return [$player1TotalScore, $player2TotalScore];
    }
}
