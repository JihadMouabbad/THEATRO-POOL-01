<?php

namespace App\Http\Controllers;

use App\Models\PoolMatch;
use App\Services\BracketGenerator;
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
     * Create a new controller instance.
     *
     * @param BracketGenerator $bracketGenerator
     */
    public function __construct(BracketGenerator $bracketGenerator)
    {
        $this->bracketGenerator = $bracketGenerator;
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
}
