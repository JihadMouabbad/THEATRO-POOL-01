<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Tournament;
use App\Services\BracketGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * TournamentController handles tournament management.
 */
class TournamentController extends Controller
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
     * Display a listing of tournaments.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $query = Tournament::query();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Search by name or location
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $tournaments = $query->orderBy('start_date', 'desc')
            ->withCount('players')
            ->paginate(15)
            ->withQueryString();

        return view('tournaments.index', compact('tournaments'));
    }

    /**
     * Show the form for creating a new tournament.
     *
     * @return View
     */
    public function create(): View
    {
        $allowedPlayerCounts = Tournament::ALLOWED_PLAYER_COUNTS;
        return view('tournaments.create', compact('allowedPlayerCounts'));
    }

    /**
     * Store a newly created tournament.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'location' => ['required', 'string', 'max:255'],
            'max_players' => ['required', 'integer', Rule::in(Tournament::ALLOWED_PLAYER_COUNTS)],
        ]);

        $tournament = Tournament::create($validated);

        return redirect()->route('tournaments.show', $tournament)
            ->with('success', 'Tournament created successfully. Start registering players!');
    }

    /**
     * Display the tournament details with bracket visualization.
     *
     * @param Tournament $tournament
     * @return View
     */
    public function show(Tournament $tournament): View
    {
        $tournament->load(['players', 'matches.player1', 'matches.player2', 'matches.winner']);

        // Organize matches by round for bracket display
        $matchesByRound = [];
        if ($tournament->total_rounds > 0) {
            for ($round = 1; $round <= $tournament->total_rounds; $round++) {
                $matchesByRound[$round] = $tournament->matches
                    ->where('round', $round)
                    ->sortBy('match_number')
                    ->values();
            }
        }

        // Get available players for registration
        $availablePlayers = [];
        if ($tournament->canRegisterPlayer()) {
            $registeredPlayerIds = $tournament->players->pluck('id')->toArray();
            $availablePlayers = Player::whereNotIn('id', $registeredPlayerIds)
                ->orderBy('name')
                ->get();
        }

        return view('tournaments.show', compact('tournament', 'matchesByRound', 'availablePlayers'));
    }

    /**
     * Show the form for editing the tournament.
     *
     * @param Tournament $tournament
     * @return View
     */
    public function edit(Tournament $tournament): View
    {
        if (!$tournament->isUpcoming()) {
            return redirect()->route('tournaments.show', $tournament)
                ->with('error', 'Cannot edit a tournament that has already started.');
        }

        $allowedPlayerCounts = Tournament::ALLOWED_PLAYER_COUNTS;
        return view('tournaments.edit', compact('tournament', 'allowedPlayerCounts'));
    }

    /**
     * Update the tournament.
     *
     * @param Request $request
     * @param Tournament $tournament
     * @return RedirectResponse
     */
    public function update(Request $request, Tournament $tournament): RedirectResponse
    {
        if (!$tournament->isUpcoming()) {
            return redirect()->route('tournaments.show', $tournament)
                ->with('error', 'Cannot edit a tournament that has already started.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'location' => ['required', 'string', 'max:255'],
            'max_players' => [
                'required', 
                'integer', 
                Rule::in(Tournament::ALLOWED_PLAYER_COUNTS),
                'gte:' . $tournament->players()->count(),
            ],
        ]);

        $tournament->update($validated);

        return redirect()->route('tournaments.show', $tournament)
            ->with('success', 'Tournament updated successfully.');
    }

    /**
     * Remove the tournament.
     *
     * @param Tournament $tournament
     * @return RedirectResponse
     */
    public function destroy(Tournament $tournament): RedirectResponse
    {
        if ($tournament->isOngoing()) {
            return back()->with('error', 'Cannot delete an ongoing tournament.');
        }

        $tournament->delete();

        return redirect()->route('tournaments.index')
            ->with('success', 'Tournament deleted successfully.');
    }

    /**
     * Register a player to the tournament.
     *
     * @param Request $request
     * @param Tournament $tournament
     * @return RedirectResponse
     */
    public function registerPlayer(Request $request, Tournament $tournament): RedirectResponse
    {
        if (!$tournament->canRegisterPlayer()) {
            return back()->with('error', 'Cannot register more players to this tournament.');
        }

        $validated = $request->validate([
            'player_id' => ['required', 'exists:players,id'],
        ]);

        // Check if player is already registered
        if ($tournament->players()->where('player_id', $validated['player_id'])->exists()) {
            return back()->with('error', 'Player is already registered in this tournament.');
        }

        // Get the next available seed
        $nextSeed = $tournament->players()->count() + 1;

        $tournament->players()->attach($validated['player_id'], ['seed' => $nextSeed]);

        return back()->with('success', 'Player registered successfully.');
    }

    /**
     * Unregister a player from the tournament.
     *
     * @param Tournament $tournament
     * @param Player $player
     * @return RedirectResponse
     */
    public function unregisterPlayer(Tournament $tournament, Player $player): RedirectResponse
    {
        if (!$tournament->isUpcoming()) {
            return back()->with('error', 'Cannot unregister players from an active tournament.');
        }

        $tournament->players()->detach($player->id);

        // Re-seed remaining players in a single transaction
        DB::transaction(function () use ($tournament) {
            $players = $tournament->players()->get();
            foreach ($players as $index => $p) {
                $tournament->players()->updateExistingPivot($p->id, ['seed' => $index + 1]);
            }
        });

        return back()->with('success', 'Player unregistered successfully.');
    }

    /**
     * Generate the tournament bracket and start the tournament.
     *
     * @param Tournament $tournament
     * @return RedirectResponse
     */
    public function generateBracket(Tournament $tournament): RedirectResponse
    {
        if (!$tournament->isUpcoming()) {
            return back()->with('error', 'Tournament has already started.');
        }

        if (!$tournament->isFull()) {
            return back()->with('error', 'Tournament must have exactly ' . $tournament->max_players . ' players.');
        }

        try {
            $this->bracketGenerator->generate($tournament);
            return redirect()->route('tournaments.show', $tournament)
                ->with('success', 'Bracket generated! Tournament is now live.');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Allow a player to join a tournament themselves.
     *
     * @param Tournament $tournament
     * @return RedirectResponse
     */
    public function joinTournament(Tournament $tournament): RedirectResponse
    {
        $user = auth()->user();
        
        if (!$user->hasPlayer()) {
            return back()->with('error', 'You need to create a player profile first. Go to your profile to create one.');
        }
        
        $player = $user->player;
        
        if (!$tournament->canRegisterPlayer()) {
            return back()->with('error', 'Cannot join this tournament. It may be full or already started.');
        }
        
        // Check if player is already registered
        if ($tournament->players()->where('player_id', $player->id)->exists()) {
            return back()->with('error', 'You are already registered in this tournament.');
        }
        
        // Get the next available seed
        $nextSeed = $tournament->players()->count() + 1;
        
        $tournament->players()->attach($player->id, ['seed' => $nextSeed]);
        
        return back()->with('success', 'You have successfully joined the tournament!');
    }

    /**
     * Allow a player to leave a tournament.
     *
     * @param Tournament $tournament
     * @return RedirectResponse
     */
    public function leaveTournament(Tournament $tournament): RedirectResponse
    {
        $user = auth()->user();
        
        if (!$user->hasPlayer()) {
            return back()->with('error', 'You do not have a player profile.');
        }
        
        $player = $user->player;
        
        if (!$tournament->isUpcoming()) {
            return back()->with('error', 'Cannot leave a tournament that has already started.');
        }
        
        // Check if player is registered
        if (!$tournament->players()->where('player_id', $player->id)->exists()) {
            return back()->with('error', 'You are not registered in this tournament.');
        }
        
        $tournament->players()->detach($player->id);
        
        // Re-seed remaining players in a single transaction
        DB::transaction(function () use ($tournament) {
            $players = $tournament->players()->get();
            foreach ($players as $index => $p) {
                $tournament->players()->updateExistingPivot($p->id, ['seed' => $index + 1]);
            }
        });
        
        return back()->with('success', 'You have left the tournament.');
    }
}
