<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * ArchiveController handles the display of finished tournaments.
 */
class ArchiveController extends Controller
{
    /**
     * Display the tournament archive.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $query = Tournament::where('status', Tournament::STATUS_FINISHED);

        // Filter by year
        if ($request->filled('year')) {
            $year = $request->input('year');
            $query->whereYear('end_date', $year);
        }

        // Search by name
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $tournaments = $query->orderByDesc('end_date')
            ->withCount('players')
            ->paginate(12)
            ->withQueryString();

        // Calculate champion for each tournament
        foreach ($tournaments as $tournament) {
            $tournament->champion = $tournament->getChampion();
        }

        // Get available years for filtering - handle both SQLite and MySQL
        $driver = config('database.default');
        if ($driver === 'sqlite') {
            $availableYears = Tournament::where('status', Tournament::STATUS_FINISHED)
                ->whereNotNull('end_date')
                ->selectRaw("strftime('%Y', end_date) as year")
                ->distinct()
                ->orderByDesc('year')
                ->pluck('year');
        } else {
            $availableYears = Tournament::where('status', Tournament::STATUS_FINISHED)
                ->whereNotNull('end_date')
                ->selectRaw("YEAR(end_date) as year")
                ->distinct()
                ->orderByDesc('year')
                ->pluck('year');
        }

        // Get archive statistics
        $stats = [
            'total_tournaments' => Tournament::where('status', Tournament::STATUS_FINISHED)->count(),
            'total_players_participated' => \DB::table('tournament_player')
                ->join('tournaments', 'tournament_player.tournament_id', '=', 'tournaments.id')
                ->where('tournaments.status', Tournament::STATUS_FINISHED)
                ->distinct()
                ->count('tournament_player.player_id'),
        ];

        return view('archive.index', compact(
            'tournaments',
            'availableYears',
            'stats'
        ));
    }
}
