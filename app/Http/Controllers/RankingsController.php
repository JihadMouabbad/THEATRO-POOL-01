<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\PoolMatch;
use App\Models\Tournament;
use App\Services\EloRatingService;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * RankingsController handles player rankings and leaderboard.
 */
class RankingsController extends Controller
{
    protected EloRatingService $eloService;

    public function __construct(EloRatingService $eloService)
    {
        $this->eloService = $eloService;
    }

    /**
     * Display the player rankings/leaderboard page.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        // Get sorting preference
        $sortBy = $request->input('sort', 'rating');
        $validSorts = ['wins', 'win_rate', 'total_matches', 'name', 'rating'];

        if (!in_array($sortBy, $validSorts)) {
            $sortBy = 'rating';
        }

        // Build query for players with matches
        $query = Player::where('total_matches', '>', 0);

        // Apply sorting
        switch ($sortBy) {
            case 'wins':
                $query->orderByDesc('wins');
                break;
            case 'total_matches':
                $query->orderByDesc('total_matches');
                break;
            case 'name':
                $query->orderBy('name');
                break;
            case 'win_rate':
                $query->orderByRaw('(wins * 100.0 / NULLIF(total_matches, 0)) DESC');
                break;
            case 'rating':
            default:
                $query->orderByDesc('ranking_points');
                break;
        }

        $rankedPlayers = $query->take(50)->get();

        // Add tier information to each player
        $rankedPlayers->each(function ($player, $index) {
            $player->tier = $this->eloService->getRatingTier($player->ranking_points ?? 1000);
            $player->rank = $index + 1;
        });

        // Get recent champions - calculate dynamically
        $finishedTournaments = Tournament::where('status', Tournament::STATUS_FINISHED)
            ->orderByDesc('end_date')
            ->take(10)
            ->get();

        $recentChampions = collect();
        foreach ($finishedTournaments as $tournament) {
            $champion = $tournament->getChampion();
            if ($champion) {
                $tournament->champion = $champion;
                $recentChampions->push($tournament);
                if ($recentChampions->count() >= 5) {
                    break;
                }
            }
        }

        // Get statistics for the page
        $stats = [
            'total_players' => Player::count(),
            'players_with_matches' => Player::where('total_matches', '>', 0)->count(),
            'total_matches' => PoolMatch::where('status', PoolMatch::STATUS_COMPLETED)->count(),
            'total_tournaments' => Tournament::where('status', Tournament::STATUS_FINISHED)->count(),
        ];

        // Get most active players (by total matches)
        $mostActive = Player::where('total_matches', '>', 0)
            ->orderByDesc('total_matches')
            ->take(5)
            ->get();

        return view('rankings.index', compact(
            'rankedPlayers',
            'recentChampions',
            'stats',
            'mostActive',
            'sortBy'
        ));
    }
}
