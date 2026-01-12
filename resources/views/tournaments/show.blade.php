@extends('layouts.app')

@section('title', $tournament->name)

@section('content')
<div class="mb-6">
    <a href="{{ route('tournaments.index') }}" class="text-pool-green hover:underline">&larr; Back to Tournaments</a>
</div>

<!-- Tournament Header -->
<div class="bg-white rounded-lg shadow-md p-6 mb-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <h1 class="text-3xl font-bold text-pool-green">{{ $tournament->name }}</h1>
                <span class="px-3 py-1 text-sm rounded-full
                    {{ $tournament->status === 'upcoming' ? 'bg-blue-100 text-blue-800' : '' }}
                    {{ $tournament->status === 'ongoing' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $tournament->status === 'finished' ? 'bg-gray-100 text-gray-800' : '' }}
                ">
                    {{ ucfirst($tournament->status) }}
                </span>
            </div>
            <p class="text-gray-600">📍 {{ $tournament->location }}</p>
            <p class="text-gray-600">📅 {{ $tournament->start_date->format('F d, Y') }}</p>
            @if($tournament->description)
            <p class="text-gray-500 mt-2">{{ $tournament->description }}</p>
            @endif
        </div>
        @auth
            @if(Auth::user()->isAdmin() && $tournament->isUpcoming())
            <div class="mt-4 md:mt-0 flex gap-2">
                <a href="{{ route('tournaments.edit', $tournament) }}" class="px-4 py-2 border-2 border-pool-green text-pool-green rounded-lg hover:bg-gray-50 transition">
                    Edit
                </a>
            </div>
            @endif
        @endauth
    </div>

    <!-- Tournament Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
        <div class="bg-gray-50 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-pool-green">{{ $tournament->max_players }}</div>
            <div class="text-sm text-gray-500">Max Players</div>
        </div>
        <div class="bg-gray-50 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-pool-green">{{ $tournament->players->count() }}</div>
            <div class="text-sm text-gray-500">Registered</div>
        </div>
        <div class="bg-gray-50 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-pool-green">{{ $tournament->total_rounds ?: (int) log($tournament->max_players, 2) }}</div>
            <div class="text-sm text-gray-500">Rounds</div>
        </div>
        <div class="bg-gray-50 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-pool-green">{{ $tournament->matches->where('status', 'completed')->count() }}</div>
            <div class="text-sm text-gray-500">Matches Played</div>
        </div>
    </div>

    @if($tournament->isFinished())
        @php $champion = $tournament->getChampion(); @endphp
        @if($champion)
        <div class="mt-6 bg-yellow-50 border border-yellow-300 rounded-lg p-4 text-center">
            <span class="text-4xl">🏆</span>
            <h3 class="text-xl font-bold text-yellow-800 mt-2">Champion</h3>
            <a href="{{ route('players.show', $champion) }}" class="text-2xl font-bold text-pool-green hover:underline">
                {{ $champion->display_name }}
            </a>
        </div>
        @endif
    @endif
</div>

@if($tournament->isUpcoming())
<!-- Registration Section (Only for Upcoming Tournaments) -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Registered Players -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-pool-green">Registered Players</h2>
            <span class="text-sm text-gray-500">{{ $tournament->players->count() }}/{{ $tournament->max_players }}</span>
        </div>
        
        @if($tournament->players->count() > 0)
        <div class="space-y-2">
            @foreach($tournament->players as $index => $player)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div class="flex items-center gap-3">
                    <span class="w-6 h-6 bg-pool-green text-white rounded-full flex items-center justify-center text-xs font-bold">
                        {{ $index + 1 }}
                    </span>
                    <a href="{{ route('players.show', $player) }}" class="font-medium hover:text-pool-green">
                        {{ $player->display_name }}
                    </a>
                </div>
                @auth
                    @if(Auth::user()->isAdmin())
                    <form action="{{ route('tournaments.unregisterPlayer', [$tournament, $player]) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm" onclick="return confirm('Remove this player?');">
                            Remove
                        </button>
                    </form>
                    @endif
                @endauth
            </div>
            @endforeach
        </div>
        @else
        <p class="text-gray-500 text-center py-4">No players registered yet</p>
        @endif
    </div>

    <!-- Add Players -->
    @auth
        @if(Auth::user()->isAdmin())
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-pool-green mb-4">Add Player</h2>
            
            @if($tournament->canRegisterPlayer())
                @if($availablePlayers->count() > 0)
                <form action="{{ route('tournaments.registerPlayer', $tournament) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <select name="player_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pool-green">
                            <option value="">Select a player</option>
                            @foreach($availablePlayers as $player)
                            <option value="{{ $player->id }}">{{ $player->display_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="w-full py-2 bg-pool-green text-white rounded-lg hover:bg-pool-felt transition">
                        Register Player
                    </button>
                </form>
                @else
                <p class="text-gray-500 text-center py-4">No available players to register</p>
                <a href="{{ route('players.create') }}" class="block text-center text-pool-green hover:underline mt-2">
                    + Add new player
                </a>
                @endif
            @else
                <p class="text-green-600 text-center py-4 font-medium">Tournament is full!</p>
            @endif
        </div>
        @endif
    @endauth
</div>

<!-- Start Tournament Button -->
@auth
    @if(Auth::user()->isAdmin() && $tournament->isFull())
    <div class="bg-green-50 border border-green-300 rounded-lg p-6 mb-8 text-center">
        <h3 class="text-lg font-semibold text-green-800 mb-2">🎉 Ready to Start!</h3>
        <p class="text-green-700 mb-4">All {{ $tournament->max_players }} players are registered. Generate the bracket and start the tournament!</p>
        <form action="{{ route('tournaments.generateBracket', $tournament) }}" method="POST">
            @csrf
            <button type="submit" class="px-8 py-3 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700 transition">
                🏆 Generate Bracket & Start Tournament
            </button>
        </form>
    </div>
    @endif
@endauth

@elseif($tournament->isOngoing() || $tournament->isFinished())
<!-- Bracket Section -->
<div class="bg-white rounded-lg shadow-md p-6 mb-8">
    <h2 class="text-xl font-semibold text-pool-green mb-6">Tournament Bracket</h2>
    
    @if(count($matchesByRound) > 0)
    <div class="overflow-x-auto">
        <div class="flex gap-8 min-w-max pb-4">
            @foreach($matchesByRound as $round => $matches)
            <div class="flex-shrink-0" style="width: 250px;">
                <h3 class="text-center font-semibold text-gray-700 mb-4 bg-gray-100 py-2 rounded">
                    {{ $tournament->getRoundName($round) }}
                </h3>
                <div class="space-y-4">
                    @foreach($matches as $match)
                    <div class="border rounded-lg overflow-hidden {{ $match->isCompleted() ? 'border-gray-300' : 'border-pool-green' }}">
                        <!-- Player 1 -->
                        <div class="flex items-center justify-between p-3 border-b {{ $match->winner_id === $match->player1_id ? 'bg-green-50' : 'bg-white' }}">
                            <div class="flex items-center gap-2 flex-1 min-w-0">
                                @if($match->winner_id === $match->player1_id)
                                <span class="text-green-600">🏆</span>
                                @endif
                                <span class="truncate {{ $match->winner_id === $match->player1_id ? 'font-bold text-green-700' : '' }}">
                                    {{ $match->player1?->display_name ?? 'TBD' }}
                                </span>
                            </div>
                            <span class="font-mono font-bold ml-2">
                                {{ $match->player1_score ?? '-' }}
                            </span>
                        </div>
                        <!-- Player 2 -->
                        <div class="flex items-center justify-between p-3 {{ $match->winner_id === $match->player2_id ? 'bg-green-50' : 'bg-white' }}">
                            <div class="flex items-center gap-2 flex-1 min-w-0">
                                @if($match->winner_id === $match->player2_id)
                                <span class="text-green-600">🏆</span>
                                @endif
                                <span class="truncate {{ $match->winner_id === $match->player2_id ? 'font-bold text-green-700' : '' }}">
                                    {{ $match->player2?->display_name ?? 'TBD' }}
                                </span>
                            </div>
                            <span class="font-mono font-bold ml-2">
                                {{ $match->player2_score ?? '-' }}
                            </span>
                        </div>
                        <!-- Action -->
                        @auth
                            @if(Auth::user()->isAdmin() && $match->hasBothPlayers() && !$match->isCompleted())
                            <div class="bg-gray-50 p-2 border-t">
                                <a href="{{ route('matches.edit', $match) }}" class="block text-center text-sm text-pool-green hover:underline">
                                    Enter Result
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
    <p class="text-gray-500 text-center py-4">Bracket not yet generated</p>
    @endif
</div>

<!-- Registered Players List (Ongoing/Finished) -->
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-xl font-semibold text-pool-green mb-4">Participants</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-3">
        @foreach($tournament->players as $player)
        <a href="{{ route('players.show', $player) }}" class="p-3 bg-gray-50 rounded-lg text-center hover:bg-gray-100 transition">
            <div class="w-10 h-10 bg-pool-green text-white rounded-full flex items-center justify-center mx-auto mb-1 text-sm font-bold">
                {{ substr($player->name, 0, 1) }}
            </div>
            <span class="text-sm truncate block">{{ $player->display_name }}</span>
        </a>
        @endforeach
    </div>
</div>
@endif
@endsection
