<?php

namespace App\Http\Controllers;

use App\Models\PoolMatch;
use App\Models\Tournament;
use Illuminate\View\View;

/**
 * ActivityController shows recent activity in the application.
 */
class ActivityController extends Controller
{
    /**
     * Show the recent activity feed.
     *
     * @return View
     */
    public function index(): View
    {
        // Get recent completed matches
        $recentMatches = PoolMatch::with(['player1', 'player2', 'winner', 'tournament'])
            ->whereNotNull('winner_id')
            ->orderBy('updated_at', 'desc')
            ->limit(20)
            ->get();

        // Get recent tournament status changes
        $recentTournaments = Tournament::orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        // Get today's matches
        $todayMatches = PoolMatch::with(['player1', 'player2', 'winner', 'tournament'])
            ->whereDate('updated_at', today())
            ->whereNotNull('winner_id')
            ->orderBy('updated_at', 'desc')
            ->get();

        // Get live tournaments
        $liveTournaments = Tournament::where('status', 'ongoing')
            ->withCount('players')
            ->get();

        return view('activity.index', compact(
            'recentMatches',
            'recentTournaments',
            'todayMatches',
            'liveTournaments'
        ));
    }
}
