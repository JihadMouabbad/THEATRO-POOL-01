<?php

namespace App\Http\Controllers;

use App\Models\Player;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * ProfileController handles user profile and player profile management.
 */
class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     *
     * @return View
     */
    public function show(): View
    {
        $user = Auth::user();
        $player = $user->player;
        
        // Get recent matches if player exists
        $recentMatches = collect();
        $upcomingTournaments = collect();
        
        if ($player) {
            // Get matches as player1 and player2 separately, then merge
            $matchesAsPlayer1 = $player->matchesAsPlayer1()
                ->with(['tournament', 'player2', 'winner'])
                ->where('status', 'completed')
                ->get();
                
            $matchesAsPlayer2 = $player->matchesAsPlayer2()
                ->with(['tournament', 'player1', 'winner'])
                ->where('status', 'completed')
                ->get();
            
            $recentMatches = $matchesAsPlayer1->merge($matchesAsPlayer2)
                ->sortByDesc('completed_at')
                ->take(10)
                ->values();
                
            $upcomingTournaments = $player->tournaments()
                ->whereIn('status', ['upcoming', 'ongoing'])
                ->orderBy('start_date')
                ->get();
        }
        
        return view('profile.show', compact('user', 'player', 'recentMatches', 'upcomingTournaments'));
    }

    /**
     * Show the form for editing the profile.
     *
     * @return View
     */
    public function edit(): View
    {
        $user = Auth::user();
        $player = $user->player;
        
        return view('profile.edit', compact('user', 'player'));
    }

    /**
     * Update the user's profile.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();
        
        // Validate user fields
        $userValidated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);
        
        // Update user
        $user->update($userValidated);
        
        // If user has a player profile, update that too
        if ($user->player) {
            $playerValidated = $request->validate([
                'nickname' => ['nullable', 'string', 'max:255'],
                'phone' => ['nullable', 'string', 'max:20'],
            ]);
            
            // Sync name and email with player profile
            $user->player->update([
                'name' => $userValidated['name'],
                'email' => $userValidated['email'],
                'nickname' => $playerValidated['nickname'] ?? $user->player->nickname,
                'phone' => $playerValidated['phone'] ?? $user->player->phone,
            ]);
        }
        
        return redirect()->route('profile.show')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Create a player profile for the current user.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function createPlayer(Request $request): RedirectResponse
    {
        $user = Auth::user();
        
        if ($user->hasPlayer()) {
            return redirect()->route('profile.show')
                ->with('error', 'You already have a player profile.');
        }
        
        $validated = $request->validate([
            'nickname' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);
        
        // Create player profile
        $player = Player::create([
            'name' => $user->name,
            'email' => $user->email,
            'nickname' => $validated['nickname'] ?? null,
            'phone' => $validated['phone'] ?? null,
        ]);
        
        // Link player to user
        $user->update(['player_id' => $player->id]);
        
        return redirect()->route('profile.show')
            ->with('success', 'Player profile created! You can now join tournaments.');
    }
}
