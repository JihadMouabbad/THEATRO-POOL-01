<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\PoolMatch;
use App\Models\Tournament;
use Illuminate\View\View;

/**
 * HomeController handles the public home/welcome page.
 */
class HomeController extends Controller
{
    /**
     * Show the welcome/home page with statistics and featured content.
     *
     * @return View
     */
    public function index(): View
    {
        // Get statistics for the hero section
        $stats = [
            'tournaments' => Tournament::count(),
            'players' => Player::count(),
            'matches' => PoolMatch::where('status', PoolMatch::STATUS_COMPLETED)->count(),
        ];

        // Get active/live tournaments
        $activeTournaments = Tournament::where('status', Tournament::STATUS_ONGOING)
            ->orderBy('start_date')
            ->take(3)
            ->get();

        // Get upcoming tournaments
        $upcomingTournaments = Tournament::where('status', Tournament::STATUS_UPCOMING)
            ->orderBy('start_date')
            ->take(3)
            ->get();

        // Get top players for Hall of Fame (players with matches only)
        $topPlayers = Player::where('total_matches', '>', 0)
            ->orderByDesc('wins')
            ->take(5)
            ->get();

        return view('welcome', compact(
            'stats',
            'activeTournaments',
            'upcomingTournaments',
            'topPlayers'
        ));
    }
}
