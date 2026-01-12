<?php

namespace App\Http\Controllers;

use App\Models\PoolMatch;
use App\Models\Player;
use App\Models\Tournament;
use Illuminate\View\View;

/**
 * DashboardController handles the admin dashboard display.
 */
class DashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     *
     * @return View
     */
    public function index(): View
    {
        // Get active/ongoing tournaments
        $activeTournaments = Tournament::where('status', Tournament::STATUS_ONGOING)
            ->orderBy('start_date')
            ->take(5)
            ->get();

        // Get upcoming tournaments
        $upcomingTournaments = Tournament::where('status', Tournament::STATUS_UPCOMING)
            ->orderBy('start_date')
            ->take(5)
            ->get();

        // Get recent completed tournaments
        $recentlyCompleted = Tournament::where('status', Tournament::STATUS_FINISHED)
            ->orderBy('end_date', 'desc')
            ->take(5)
            ->get();

        // Get top players by wins
        $topPlayers = Player::orderBy('wins', 'desc')
            ->take(10)
            ->get();

        // Get recent matches
        $recentMatches = PoolMatch::with(['tournament', 'player1', 'player2', 'winner'])
            ->where('status', PoolMatch::STATUS_COMPLETED)
            ->orderBy('completed_at', 'desc')
            ->take(10)
            ->get();

        // Statistics
        $stats = [
            'total_players' => Player::count(),
            'total_tournaments' => Tournament::count(),
            'active_tournaments' => $activeTournaments->count(),
            'total_matches' => PoolMatch::where('status', PoolMatch::STATUS_COMPLETED)->count(),
        ];

        return view('dashboard', compact(
            'activeTournaments',
            'upcomingTournaments',
            'recentlyCompleted',
            'topPlayers',
            'recentMatches',
            'stats'
        ));
    }
}
