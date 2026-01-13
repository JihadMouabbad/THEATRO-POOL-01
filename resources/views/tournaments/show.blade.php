@extends('layouts.app')

@section('title', $tournament->name)

@section('content')
<div class="mb-6">
    <a href="{{ route('tournaments.index') }}" class="inline-flex items-center gap-2 text-pool-green hover:text-pool-felt transition font-medium">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to Tournaments
    </a>
</div>

<!-- Tournament Header -->
<div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
    <div class="h-2 
        {{ $tournament->status === 'upcoming' ? 'bg-gradient-to-r from-blue-400 to-indigo-500' : '' }}
        {{ $tournament->status === 'ongoing' ? 'bg-gradient-to-r from-green-400 to-emerald-500' : '' }}
        {{ $tournament->status === 'finished' ? 'bg-gradient-to-r from-gray-400 to-gray-500' : '' }}
    "></div>
    <div class="p-6 md:p-8">
        <div class="flex flex-col md:flex-row md:items-start md:justify-between">
            <div class="flex-1">
                <div class="flex flex-wrap items-center gap-3 mb-4">
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-800">{{ $tournament->name }}</h1>
                    <span class="px-4 py-1.5 text-sm font-bold rounded-full
                        {{ $tournament->status === 'upcoming' ? 'bg-blue-100 text-blue-700' : '' }}
                        {{ $tournament->status === 'ongoing' ? 'bg-green-100 text-green-700 animate-pulse' : '' }}
                        {{ $tournament->status === 'finished' ? 'bg-gray-100 text-gray-700' : '' }}
                    ">
                        {{ $tournament->status === 'ongoing' ? '🔴 LIVE' : strtoupper($tournament->status) }}
                    </span>
                </div>
                <div class="space-y-2 text-gray-600">
                    <p class="flex items-center gap-2">
                        <span class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">📍</span>
                        {{ $tournament->location }}
                    </p>
                    <p class="flex items-center gap-2">
                        <span class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">📅</span>
                        {{ $tournament->start_date->format('F d, Y') }}
                    </p>
                </div>
                @if($tournament->description)
                <p class="text-gray-500 mt-4 bg-gray-50 p-4 rounded-xl italic">{{ $tournament->description }}</p>
                @endif
            </div>
            @auth
                @if(Auth::user()->isAdmin() && $tournament->isUpcoming())
                <div class="mt-4 md:mt-0 flex gap-2">
                    <a href="{{ route('tournaments.edit', $tournament) }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all duration-300 font-medium flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>
                </div>
                @endif
            @endauth
        </div>

        <!-- Tournament Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8">
            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-5 text-center group hover:shadow-md transition-shadow">
                <div class="text-3xl font-black text-pool-green">{{ $tournament->max_players }}</div>
                <div class="text-sm text-gray-500 font-medium mt-1">Max Players</div>
            </div>
            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-5 text-center group hover:shadow-md transition-shadow">
                <div class="text-3xl font-black text-pool-green">{{ $tournament->players->count() }}</div>
                <div class="text-sm text-gray-500 font-medium mt-1">Registered</div>
                <div class="mt-2">
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-gradient-to-r from-pool-green to-pool-felt h-2 rounded-full progress-bar" 
                             style="width: {{ ($tournament->players->count() / $tournament->max_players) * 100 }}%"></div>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-5 text-center group hover:shadow-md transition-shadow">
                <div class="text-3xl font-black text-pool-green">{{ $tournament->total_rounds ?: (int) log($tournament->max_players, 2) }}</div>
                <div class="text-sm text-gray-500 font-medium mt-1">Rounds</div>
            </div>
            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-5 text-center group hover:shadow-md transition-shadow">
                <div class="text-3xl font-black text-pool-green">{{ $tournament->matches->where('status', 'completed')->count() }}</div>
                <div class="text-sm text-gray-500 font-medium mt-1">Matches Played</div>
            </div>
        </div>

        @if($tournament->isFinished())
            @php $champion = $tournament->getChampion(); @endphp
            @if($champion)
            <div class="mt-8 bg-gradient-to-r from-yellow-50 via-amber-50 to-yellow-50 border-2 border-yellow-300 rounded-2xl p-8 text-center relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent transform -skew-x-12"></div>
                <div class="relative z-10">
                    <span class="text-6xl trophy-shine inline-block">🏆</span>
                    <h3 class="text-2xl font-bold text-yellow-800 mt-4">Tournament Champion</h3>
                    <a href="{{ route('players.show', $champion) }}" class="text-3xl font-black text-pool-green hover:text-pool-felt transition inline-block mt-2">
                        {{ $champion->display_name }}
                    </a>
                </div>
            </div>
            @endif
        @endif
    </div>
</div>

@if($tournament->isUpcoming())
<!-- Registration Section (Only for Upcoming Tournaments) -->

<!-- Player Self-Join Section -->
@auth
    @if(!Auth::user()->isAdmin())
        @php
            $userPlayer = Auth::user()->player;
            $isRegistered = $userPlayer && $tournament->players->contains('id', $userPlayer->id);
        @endphp
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-4">
                <h2 class="text-lg font-bold text-white flex items-center gap-2">
                    🎱 Join This Tournament
                </h2>
            </div>
            <div class="p-6">
                @if(!$userPlayer)
                    <div class="text-center py-4">
                        <span class="text-4xl">👤</span>
                        <p class="text-gray-600 mt-3 font-medium">You need a player profile to join tournaments</p>
                        <a href="{{ route('profile.show') }}" class="mt-4 inline-flex items-center gap-2 px-6 py-3 bg-pool-green text-white font-semibold rounded-xl hover:bg-pool-felt transition">
                            Create Player Profile
                        </a>
                    </div>
                @elseif($isRegistered)
                    <div class="text-center py-4">
                        <span class="text-4xl">✅</span>
                        <p class="text-green-600 font-bold text-lg mt-3">You're registered!</p>
                        <p class="text-gray-500 text-sm mt-1">Good luck in the tournament!</p>
                        <form action="{{ route('tournaments.leave', $tournament) }}" method="POST" class="mt-4">
                            @csrf
                            <button type="submit" class="px-6 py-2 bg-red-100 text-red-700 font-semibold rounded-xl hover:bg-red-200 transition" onclick="return confirm('Are you sure you want to leave this tournament?');">
                                Leave Tournament
                            </button>
                        </form>
                    </div>
                @elseif($tournament->canRegisterPlayer())
                    <div class="text-center py-4">
                        <span class="text-4xl">🏆</span>
                        <p class="text-gray-600 mt-3 font-medium">Join this tournament and compete!</p>
                        <form action="{{ route('tournaments.join', $tournament) }}" method="POST" class="mt-4">
                            @csrf
                            <button type="submit" class="px-8 py-3 bg-gradient-to-r from-pool-green to-pool-felt text-white font-bold rounded-xl hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                                🎱 Join Tournament
                            </button>
                        </form>
                    </div>
                @else
                    <div class="text-center py-4">
                        <span class="text-4xl">📋</span>
                        <p class="text-gray-600 font-medium mt-3">This tournament is full</p>
                        <p class="text-gray-500 text-sm mt-1">Check back for upcoming tournaments!</p>
                    </div>
                @endif
            </div>
        </div>
    @endif
@else
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-4">
            <h2 class="text-lg font-bold text-white flex items-center gap-2">
                🎱 Want to Join?
            </h2>
        </div>
        <div class="p-6 text-center">
            <span class="text-4xl">🔐</span>
            <p class="text-gray-600 mt-3 font-medium">Login or register to join this tournament</p>
            <div class="mt-4 flex justify-center gap-4">
                <a href="{{ route('login') }}" class="px-6 py-2 bg-pool-green text-white font-semibold rounded-xl hover:bg-pool-felt transition">
                    Login
                </a>
                <a href="{{ route('register') }}" class="px-6 py-2 border-2 border-pool-green text-pool-green font-semibold rounded-xl hover:bg-pool-green hover:text-white transition">
                    Register
                </a>
            </div>
        </div>
    </div>
@endauth

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Registered Players -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-pool-green to-pool-felt px-6 py-4 flex justify-between items-center">
            <h2 class="text-lg font-bold text-white flex items-center gap-2">
                👥 Registered Players
            </h2>
            <span class="px-3 py-1 bg-white/20 text-white text-sm font-bold rounded-full">{{ $tournament->players->count() }}/{{ $tournament->max_players }}</span>
        </div>
        <div class="p-6">
            @if($tournament->players->count() > 0)
            <div class="space-y-2">
                @foreach($tournament->players as $index => $player)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition group">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 bg-gradient-to-br from-pool-green to-pool-felt text-white rounded-full flex items-center justify-center text-sm font-bold shadow">
                            {{ $index + 1 }}
                        </span>
                        <a href="{{ route('players.show', $player) }}" class="font-medium text-gray-800 hover:text-pool-green transition">
                            {{ $player->display_name }}
                        </a>
                        <span class="text-xs text-gray-400">{{ $player->wins }}W - {{ $player->losses }}L</span>
                    </div>
                    @auth
                        @if(Auth::user()->isAdmin())
                        <form action="{{ route('tournaments.unregisterPlayer', [$tournament, $player]) }}" method="POST" class="inline opacity-0 group-hover:opacity-100 transition">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-medium transition" onclick="return confirm('Remove this player?');">
                                ✕ Remove
                            </button>
                        </form>
                        @endif
                    @endauth
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8">
                <span class="text-5xl">👥</span>
                <p class="text-gray-500 mt-3">No players registered yet</p>
                <p class="text-sm text-gray-400">Be the first to join!</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Add Players -->
    @auth
        @if(Auth::user()->isAdmin())
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-4">
                <h2 class="text-lg font-bold text-white flex items-center gap-2">
                    ➕ Add Player
                </h2>
            </div>
            <div class="p-6">
                @if($tournament->canRegisterPlayer())
                    @if($availablePlayers->count() > 0)
                    <form action="{{ route('tournaments.registerPlayer', $tournament) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Select Player</label>
                            <select name="player_id" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-pool-green focus:border-pool-green transition text-gray-700">
                                <option value="">Choose a player...</option>
                                @foreach($availablePlayers as $player)
                                <option value="{{ $player->id }}">{{ $player->display_name }} ({{ $player->wins }}W - {{ $player->losses }}L)</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="w-full py-3 bg-gradient-to-r from-pool-green to-pool-felt text-white font-bold rounded-xl hover:shadow-lg transition-all duration-300 flex items-center justify-center gap-2">
                            <span>➕</span> Register Player
                        </button>
                    </form>
                    @else
                    <div class="text-center py-8">
                        <span class="text-5xl">🎱</span>
                        <p class="text-gray-500 mt-3">No available players</p>
                        <a href="{{ route('players.create') }}" class="mt-4 inline-flex items-center gap-2 text-pool-green hover:text-pool-felt font-medium transition">
                            <span>➕</span> Add new player
                        </a>
                    </div>
                    @endif
                @else
                    <div class="text-center py-8">
                        <span class="text-5xl">✅</span>
                        <p class="text-green-600 font-bold text-lg mt-3">Tournament is full!</p>
                        <p class="text-gray-500 text-sm mt-1">Ready to generate bracket</p>
                    </div>
                @endif
            </div>
        </div>
        @endif
    @endauth
</div>

<!-- Start Tournament Button -->
@auth
    @if(Auth::user()->isAdmin() && $tournament->isFull())
    <div class="bg-gradient-to-r from-green-50 via-emerald-50 to-green-50 border-2 border-green-300 rounded-2xl p-8 mb-8 text-center relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-green-400 to-transparent transform -skew-x-12 animate-pulse"></div>
        </div>
        <div class="relative z-10">
            <span class="text-5xl inline-block animate-bounce-slow">🎉</span>
            <h3 class="text-2xl font-bold text-green-800 mt-4">Ready to Start!</h3>
            <p class="text-green-700 mt-2 max-w-md mx-auto">All {{ $tournament->max_players }} players are registered. Generate the bracket and let the games begin!</p>
            <form action="{{ route('tournaments.generateBracket', $tournament) }}" method="POST" class="mt-6">
                @csrf
                <button type="submit" class="px-8 py-4 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-bold rounded-xl hover:shadow-xl transition-all duration-300 transform hover:scale-105 inline-flex items-center gap-2">
                    <span class="text-2xl">🏆</span>
                    Generate Bracket & Start Tournament
                </button>
            </form>
        </div>
    </div>
    @endif
@endauth

@elseif($tournament->isOngoing() || $tournament->isFinished())
<!-- Bracket Section -->
<div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8 print-full">
    <div class="bg-gradient-to-r from-pool-green to-pool-felt px-6 py-4 flex justify-between items-center no-print">
        <h2 class="text-lg font-bold text-white flex items-center gap-2">
            🏆 Tournament Bracket
        </h2>
        <button onclick="window.print()" class="px-4 py-2 bg-white/20 text-white rounded-lg hover:bg-white/30 transition text-sm font-medium flex items-center gap-2">
            🖨️ Print Bracket
        </button>
    </div>
    <div class="p-6">
        @if(count($matchesByRound) > 0)
        <div class="overflow-x-auto pb-4">
            <div class="flex gap-6 min-w-max">
                @foreach($matchesByRound as $round => $matches)
                <div class="flex-shrink-0" style="width: 280px;">
                    <h3 class="text-center font-bold text-gray-700 mb-4 py-3 px-4 bg-gradient-to-r from-gray-100 to-gray-50 rounded-xl border-2 border-gray-200">
                        {{ $tournament->getRoundName($round) }}
                        @if($round === $tournament->total_rounds)
                            <span class="ml-2">🏆</span>
                        @endif
                    </h3>
                    <div class="space-y-4">
                        @foreach($matches as $match)
                        <div class="bg-white border-2 rounded-xl overflow-hidden transition-all duration-300 
                            {{ $match->isCompleted() ? 'border-gray-200 shadow-sm' : 'border-pool-green shadow-lg animate-glow' }}">
                            <!-- Player 1 -->
                            <div class="flex items-center justify-between p-4 border-b 
                                {{ $match->winner_id === $match->player1_id ? 'bg-gradient-to-r from-green-50 to-emerald-50' : 'bg-white' }}">
                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                    @if($match->winner_id === $match->player1_id)
                                    <span class="text-lg">🏆</span>
                                    @elseif($match->player1)
                                    <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center text-xs font-bold text-gray-600">
                                        {{ substr($match->player1->name, 0, 1) }}
                                    </div>
                                    @else
                                    <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-xs text-gray-400">?</div>
                                    @endif
                                    <span class="truncate font-medium {{ $match->winner_id === $match->player1_id ? 'text-green-700 font-bold' : 'text-gray-700' }}">
                                        {{ $match->player1?->display_name ?? 'TBD' }}
                                    </span>
                                </div>
                                <span class="font-mono font-bold text-lg ml-3 w-8 text-center {{ $match->winner_id === $match->player1_id ? 'text-green-600' : 'text-gray-400' }}">
                                    {{ $match->player1_score ?? '-' }}
                                </span>
                            </div>
                            <!-- Player 2 -->
                            <div class="flex items-center justify-between p-4 
                                {{ $match->winner_id === $match->player2_id ? 'bg-gradient-to-r from-green-50 to-emerald-50' : 'bg-white' }}">
                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                    @if($match->winner_id === $match->player2_id)
                                    <span class="text-lg">🏆</span>
                                    @elseif($match->player2)
                                    <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center text-xs font-bold text-gray-600">
                                        {{ substr($match->player2->name, 0, 1) }}
                                    </div>
                                    @else
                                    <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-xs text-gray-400">?</div>
                                    @endif
                                    <span class="truncate font-medium {{ $match->winner_id === $match->player2_id ? 'text-green-700 font-bold' : 'text-gray-700' }}">
                                        {{ $match->player2?->display_name ?? 'TBD' }}
                                    </span>
                                </div>
                                <span class="font-mono font-bold text-lg ml-3 w-8 text-center {{ $match->winner_id === $match->player2_id ? 'text-green-600' : 'text-gray-400' }}">
                                    {{ $match->player2_score ?? '-' }}
                                </span>
                            </div>
                            <!-- Action -->
                            @auth
                                @if(Auth::user()->isAdmin() && $match->hasBothPlayers() && !$match->isCompleted())
                                <div class="bg-gradient-to-r from-pool-green to-pool-felt p-3 no-print">
                                    <a href="{{ route('matches.edit', $match) }}" class="block text-center text-white font-medium hover:text-pool-gold transition">
                                        🎱 Enter Result
                                    </a>
                                </div>
                                @endif
                            @endauth
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="text-center py-12">
            <span class="text-5xl">🎱</span>
            <p class="text-gray-500 mt-3">Bracket not yet generated</p>
        </div>
        @endif
    </div>
</div>

<!-- Registered Players List (Ongoing/Finished) -->
<div class="bg-white rounded-2xl shadow-lg overflow-hidden">
    <div class="bg-gradient-to-r from-gray-600 to-gray-700 px-6 py-4">
        <h2 class="text-lg font-bold text-white flex items-center gap-2">
            👥 Participants
        </h2>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3">
            @foreach($tournament->players as $player)
            <a href="{{ route('players.show', $player) }}" class="p-4 bg-gray-50 rounded-xl text-center hover:bg-pool-green hover:text-white transition-all duration-300 group">
                <div class="w-12 h-12 bg-gradient-to-br from-pool-green to-pool-felt text-white rounded-full flex items-center justify-center mx-auto mb-2 text-lg font-bold group-hover:from-white group-hover:to-gray-100 group-hover:text-pool-green transition-all">
                    {{ substr($player->name, 0, 1) }}
                </div>
                <span class="text-sm font-medium truncate block">{{ $player->display_name }}</span>
                <span class="text-xs text-gray-400 group-hover:text-white/70">{{ $player->wins }}W</span>
            </a>
            @endforeach
        </div>
    </div>
</div>
@endif
@endsection
