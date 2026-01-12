@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-pool-green">Dashboard</h1>
    <p class="text-gray-600">Welcome back, {{ Auth::user()->name }}!</p>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total Players</p>
                <p class="text-2xl font-bold text-pool-green">{{ $stats['total_players'] }}</p>
            </div>
            <span class="text-3xl">👥</span>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total Tournaments</p>
                <p class="text-2xl font-bold text-pool-green">{{ $stats['total_tournaments'] }}</p>
            </div>
            <span class="text-3xl">🏆</span>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Active Tournaments</p>
                <p class="text-2xl font-bold text-pool-green">{{ $stats['active_tournaments'] }}</p>
            </div>
            <span class="text-3xl">🎯</span>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Matches Completed</p>
                <p class="text-2xl font-bold text-pool-green">{{ $stats['total_matches'] }}</p>
            </div>
            <span class="text-3xl">🎱</span>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Active Tournaments -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-pool-green">Active Tournaments</h2>
            @if(Auth::user()->isAdmin())
            <a href="{{ route('tournaments.create') }}" class="text-sm text-pool-green hover:underline">+ Create New</a>
            @endif
        </div>
        @if($activeTournaments->count() > 0)
            <div class="space-y-3">
                @foreach($activeTournaments as $tournament)
                <a href="{{ route('tournaments.show', $tournament) }}" class="block p-4 border border-gray-200 rounded-lg hover:border-pool-green transition">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-semibold">{{ $tournament->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $tournament->location }}</p>
                        </div>
                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Ongoing</span>
                    </div>
                </a>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-center py-4">No active tournaments</p>
        @endif
    </div>

    <!-- Upcoming Tournaments -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-pool-green mb-4">Upcoming Tournaments</h2>
        @if($upcomingTournaments->count() > 0)
            <div class="space-y-3">
                @foreach($upcomingTournaments as $tournament)
                <a href="{{ route('tournaments.show', $tournament) }}" class="block p-4 border border-gray-200 rounded-lg hover:border-pool-green transition">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-semibold">{{ $tournament->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $tournament->start_date->format('M d, Y') }} • {{ $tournament->location }}</p>
                        </div>
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">Upcoming</span>
                    </div>
                </a>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-center py-4">No upcoming tournaments</p>
        @endif
    </div>

    <!-- Top Players -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-pool-green">Top Players</h2>
            <a href="{{ route('players.index') }}" class="text-sm text-pool-green hover:underline">View All</a>
        </div>
        @if($topPlayers->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-sm text-gray-500 border-b">
                            <th class="pb-2">#</th>
                            <th class="pb-2">Player</th>
                            <th class="pb-2 text-center">W</th>
                            <th class="pb-2 text-center">L</th>
                            <th class="pb-2 text-center">Win%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topPlayers as $index => $player)
                        <tr class="border-b border-gray-100">
                            <td class="py-2 text-gray-500">{{ $index + 1 }}</td>
                            <td class="py-2">
                                <a href="{{ route('players.show', $player) }}" class="hover:text-pool-green">
                                    {{ $player->display_name }}
                                </a>
                            </td>
                            <td class="py-2 text-center text-green-600 font-medium">{{ $player->wins }}</td>
                            <td class="py-2 text-center text-red-600 font-medium">{{ $player->losses }}</td>
                            <td class="py-2 text-center font-medium">{{ $player->win_rate }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 text-center py-4">No players registered yet</p>
        @endif
    </div>

    <!-- Recent Matches -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-pool-green mb-4">Recent Matches</h2>
        @if($recentMatches->count() > 0)
            <div class="space-y-3">
                @foreach($recentMatches as $match)
                <div class="p-3 border border-gray-200 rounded-lg">
                    <div class="flex justify-between items-center">
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <span class="{{ $match->winner_id === $match->player1_id ? 'font-bold text-green-600' : '' }}">
                                    {{ $match->player1?->display_name ?? 'TBD' }}
                                </span>
                                <span class="text-gray-400">vs</span>
                                <span class="{{ $match->winner_id === $match->player2_id ? 'font-bold text-green-600' : '' }}">
                                    {{ $match->player2?->display_name ?? 'TBD' }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $match->tournament->name }} • {{ $match->tournament->getRoundName($match->round) }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="font-mono font-bold">{{ $match->player1_score }} - {{ $match->player2_score }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-center py-4">No matches played yet</p>
        @endif
    </div>
</div>

@if(Auth::user()->isAdmin())
<!-- Quick Actions for Admin -->
<div class="mt-8 bg-white rounded-lg shadow-md p-6">
    <h2 class="text-xl font-semibold text-pool-green mb-4">Quick Actions</h2>
    <div class="flex flex-wrap gap-4">
        <a href="{{ route('tournaments.create') }}" class="px-6 py-3 bg-pool-green text-white rounded-lg hover:bg-pool-felt transition">
            🏆 Create Tournament
        </a>
        <a href="{{ route('players.create') }}" class="px-6 py-3 bg-pool-green text-white rounded-lg hover:bg-pool-felt transition">
            👤 Add Player
        </a>
        <a href="{{ route('players.index') }}" class="px-6 py-3 border-2 border-pool-green text-pool-green rounded-lg hover:bg-gray-50 transition">
            📋 Manage Players
        </a>
        <a href="{{ route('tournaments.index') }}" class="px-6 py-3 border-2 border-pool-green text-pool-green rounded-lg hover:bg-gray-50 transition">
            📋 Manage Tournaments
        </a>
    </div>
</div>
@endif
@endsection
