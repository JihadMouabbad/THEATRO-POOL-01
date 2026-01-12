<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\PoolMatch;
use App\Models\Tournament;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

/**
 * StatisticsController handles the display of global statistics.
 */
class StatisticsController extends Controller
{
    /**
     * Display the global statistics page.
     *
     * @return View
     */
    public function index(): View
    {
        // Overall statistics
        $overallStats = [
            'total_players' => Player::count(),
            'active_players' => Player::where('total_matches', '>', 0)->count(),
            'total_tournaments' => Tournament::count(),
            'completed_tournaments' => Tournament::where('status', Tournament::STATUS_FINISHED)->count(),
            'ongoing_tournaments' => Tournament::where('status', Tournament::STATUS_ONGOING)->count(),
            'upcoming_tournaments' => Tournament::where('status', Tournament::STATUS_UPCOMING)->count(),
            'total_matches' => PoolMatch::where('status', PoolMatch::STATUS_COMPLETED)->count(),
        ];

        // Top scorers (by total wins)
        $topScorers = Player::where('total_matches', '>', 0)
            ->orderByDesc('wins')
            ->take(10)
            ->get();

        // Highest win rates (minimum 3 matches)
        $highestWinRates = Player::where('total_matches', '>=', 3)
            ->get()
            ->sortByDesc(function ($player) {
                return $player->win_rate;
            })
            ->take(10)
            ->values();

        // Most tournament wins - get champions from finished tournaments
        $finishedTournaments = Tournament::where('status', Tournament::STATUS_FINISHED)
            ->with(['matches' => function ($query) {
                $query->whereNotNull('winner_id');
            }])
            ->get();
        
        $championCounts = [];
        foreach ($finishedTournaments as $tournament) {
            $champion = $tournament->getChampion();
            if ($champion) {
                if (!isset($championCounts[$champion->id])) {
                    $championCounts[$champion->id] = [
                        'player' => $champion,
                        'count' => 0
                    ];
                }
                $championCounts[$champion->id]['count']++;
            }
        }
        
        $mostChampionships = collect($championCounts)
            ->sortByDesc('count')
            ->take(10)
            ->map(function ($item) {
                $player = $item['player'];
                $player->championships = $item['count'];
                return $player;
            })
            ->values();

        // Recent matches
        $recentMatches = PoolMatch::with(['tournament', 'player1', 'player2', 'winner'])
            ->where('status', PoolMatch::STATUS_COMPLETED)
            ->orderByDesc('completed_at')
            ->take(10)
            ->get();

        // Most popular tournament format
        $popularFormats = Tournament::selectRaw('max_players, COUNT(*) as count')
            ->groupBy('max_players')
            ->orderByDesc('count')
            ->get();

        return view('statistics.index', compact(
            'overallStats',
            'topScorers',
            'highestWinRates',
            'mostChampionships',
            'recentMatches',
            'popularFormats'
        ));
    }
}
