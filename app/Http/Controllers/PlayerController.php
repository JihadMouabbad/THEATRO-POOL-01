<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Tournament;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * PlayerController handles CRUD operations for players.
 */
class PlayerController extends Controller
{
    /**
     * Display a listing of players.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $query = Player::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nickname', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortField = $request->input('sort', 'name');
        $sortDirection = $request->input('direction', 'asc');
        
        $allowedSorts = ['name', 'wins', 'losses', 'total_matches', 'created_at'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection === 'desc' ? 'desc' : 'asc');
        }

        $players = $query->paginate(15)->withQueryString();

        return view('players.index', compact('players'));
    }

    /**
     * Show the form for creating a new player.
     *
     * @return View
     */
    public function create(): View
    {
        return view('players.create');
    }

    /**
     * Store a newly created player in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nickname' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:players'],
            'phone' => ['nullable', 'string', 'max:20'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        Player::create($validated);

        return redirect()->route('players.index')
            ->with('success', 'Player created successfully.');
    }

    /**
     * Display the specified player.
     *
     * @param Player $player
     * @return View
     */
    public function show(Player $player): View
    {
        // Load player's tournament history and recent matches
        $player->load(['tournaments' => function ($query) {
            $query->orderBy('start_date', 'desc')->take(10);
        }]);

        // Get recent matches for this player
        $recentMatches = $player->matchesAsPlayer1()
            ->with(['tournament', 'player2', 'winner'])
            ->union(
                $player->matchesAsPlayer2()
                    ->with(['tournament', 'player1', 'winner'])
            )
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->take(20)
            ->get();

        return view('players.show', compact('player', 'recentMatches'));
    }

    /**
     * Show the form for editing the specified player.
     *
     * @param Player $player
     * @return View
     */
    public function edit(Player $player): View
    {
        return view('players.edit', compact('player'));
    }

    /**
     * Update the specified player in storage.
     *
     * @param Request $request
     * @param Player $player
     * @return RedirectResponse
     */
    public function update(Request $request, Player $player): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nickname' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:players,email,' . $player->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $player->update($validated);

        return redirect()->route('players.show', $player)
            ->with('success', 'Player updated successfully.');
    }

    /**
     * Remove the specified player from storage.
     *
     * @param Player $player
     * @return RedirectResponse
     */
    public function destroy(Player $player): RedirectResponse
    {
        // Check if player is in any active tournaments
        $activeTournaments = $player->tournaments()
            ->whereIn('status', [Tournament::STATUS_UPCOMING, Tournament::STATUS_ONGOING])
            ->count();

        if ($activeTournaments > 0) {
            return back()->with('error', 'Cannot delete player who is registered in active tournaments.');
        }

        $player->delete();

        return redirect()->route('players.index')
            ->with('success', 'Player deleted successfully.');
    }
}
