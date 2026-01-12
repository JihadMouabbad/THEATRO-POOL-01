@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<!-- Dashboard Header -->
<div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between">
    <div>
        <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
            <span class="w-10 h-10 bg-gradient-to-br from-pool-green to-pool-felt rounded-lg flex items-center justify-center text-white">📊</span>
            Dashboard
        </h1>
        <p class="text-gray-500 mt-1">Welcome back, <span class="font-semibold text-pool-green">{{ Auth::user()->name }}</span>! Here's your overview.</p>
    </div>
    @if(Auth::user()->isAdmin())
    <div class="mt-4 md:mt-0 flex gap-2">
        <a href="{{ route('tournaments.create') }}" class="px-4 py-2 bg-gradient-to-r from-pool-green to-pool-felt text-white font-semibold rounded-lg hover:shadow-lg transition-all duration-300 flex items-center gap-2">
            <span>➕</span> New Tournament
        </a>
    </div>
    @endif
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
    <div class="bg-white rounded-2xl shadow-lg p-6 card-hover group">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">Total Players</p>
                <p class="text-3xl font-black text-pool-green stat-number mt-1">{{ $stats['total_players'] }}</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform shadow-lg">
                👥
            </div>
        </div>
        <div class="mt-4 flex items-center text-sm text-gray-500">
            <span class="text-green-500 font-semibold">+{{ \App\Models\Player::where('created_at', '>=', now()->subMonth())->count() }}</span>
            <span class="ml-1">this month</span>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-lg p-6 card-hover group">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">Total Tournaments</p>
                <p class="text-3xl font-black text-pool-green stat-number mt-1">{{ $stats['total_tournaments'] }}</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform shadow-lg">
                🏆
            </div>
        </div>
        <div class="mt-4 flex items-center text-sm text-gray-500">
            <span class="text-pool-green font-semibold">{{ \App\Models\Tournament::where('status', 'finished')->count() }}</span>
            <span class="ml-1">completed</span>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-lg p-6 card-hover group">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">Active Now</p>
                <p class="text-3xl font-black text-pool-green stat-number mt-1">{{ $stats['active_tournaments'] }}</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-green-400 to-emerald-600 rounded-xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform shadow-lg animate-pulse">
                🎯
            </div>
        </div>
        <div class="mt-4 flex items-center text-sm">
            @if($stats['active_tournaments'] > 0)
                <span class="flex items-center gap-1 text-green-600 font-semibold">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    Live
                </span>
            @else
                <span class="text-gray-400">No active tournaments</span>
            @endif
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-lg p-6 card-hover group">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">Matches Played</p>
                <p class="text-3xl font-black text-pool-green stat-number mt-1">{{ $stats['total_matches'] }}</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-purple-400 to-violet-600 rounded-xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform shadow-lg">
                🎱
            </div>
        </div>
        <div class="mt-4 flex items-center text-sm text-gray-500">
            <span class="text-purple-600 font-semibold">{{ \App\Models\PoolMatch::where('created_at', '>=', now()->subWeek())->where('status', 'completed')->count() }}</span>
            <span class="ml-1">this week</span>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Active Tournaments -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-4 flex justify-between items-center">
            <h2 class="text-lg font-bold text-white flex items-center gap-2">
                <span class="w-2 h-2 bg-white rounded-full animate-pulse"></span>
                Active Tournaments
            </h2>
            @if(Auth::user()->isAdmin())
            <a href="{{ route('tournaments.create') }}" class="text-white/80 hover:text-white text-sm font-medium transition">+ Create New</a>
            @endif
        </div>
        <div class="p-6">
            @if($activeTournaments->count() > 0)
                <div class="space-y-3">
                    @foreach($activeTournaments as $tournament)
                    <a href="{{ route('tournaments.show', $tournament) }}" class="block p-4 bg-gray-50 rounded-xl hover:bg-green-50 hover:shadow-md transition-all duration-300 group">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-bold text-gray-800 group-hover:text-pool-green transition-colors">{{ $tournament->name }}</h3>
                                <p class="text-sm text-gray-500 mt-1 flex items-center gap-1">📍 {{ $tournament->location }}</p>
                            </div>
                            <div class="flex flex-col items-end">
                                <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">LIVE</span>
                                <span class="text-xs text-gray-400 mt-1">{{ $tournament->matches->where('status', 'completed')->count() }}/{{ $tournament->matches->count() }} matches</span>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <span class="text-4xl">🏟️</span>
                    <p class="text-gray-500 mt-2">No active tournaments</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Upcoming Tournaments -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-4">
            <h2 class="text-lg font-bold text-white flex items-center gap-2">
                📅 Upcoming Tournaments
            </h2>
        </div>
        <div class="p-6">
            @if($upcomingTournaments->count() > 0)
                <div class="space-y-3">
                    @foreach($upcomingTournaments as $tournament)
                    <a href="{{ route('tournaments.show', $tournament) }}" class="block p-4 bg-gray-50 rounded-xl hover:bg-blue-50 hover:shadow-md transition-all duration-300 group">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-bold text-gray-800 group-hover:text-pool-green transition-colors">{{ $tournament->name }}</h3>
                                <p class="text-sm text-gray-500 mt-1">📅 {{ $tournament->start_date->format('M d, Y') }} • {{ $tournament->location }}</p>
                            </div>
                            <div class="text-right">
                                <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded-full">{{ $tournament->players->count() }}/{{ $tournament->max_players }}</span>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <span class="text-4xl">📅</span>
                    <p class="text-gray-500 mt-2">No upcoming tournaments</p>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Top Players Leaderboard -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-pool-green to-pool-felt px-6 py-4 flex justify-between items-center">
            <h2 class="text-lg font-bold text-white flex items-center gap-2">
                👑 Top Players
            </h2>
            <a href="{{ route('players.index') }}" class="text-white/80 hover:text-white text-sm font-medium transition">View All →</a>
        </div>
        <div class="p-6">
            @if($topPlayers->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100">
                                <th class="pb-3 pl-2">#</th>
                                <th class="pb-3">Player</th>
                                <th class="pb-3 text-center">W</th>
                                <th class="pb-3 text-center">L</th>
                                <th class="pb-3 text-center">Win%</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($topPlayers as $index => $player)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 pl-2">
                                    @if($index === 0)
                                        <span class="text-xl">🥇</span>
                                    @elseif($index === 1)
                                        <span class="text-xl">🥈</span>
                                    @elseif($index === 2)
                                        <span class="text-xl">🥉</span>
                                    @else
                                        <span class="text-gray-400 font-bold">{{ $index + 1 }}</span>
                                    @endif
                                </td>
                                <td class="py-3">
                                    <a href="{{ route('players.show', $player) }}" class="font-medium text-gray-800 hover:text-pool-green transition-colors">
                                        {{ $player->display_name }}
                                    </a>
                                </td>
                                <td class="py-3 text-center">
                                    <span class="px-2 py-1 bg-green-100 text-green-700 font-bold rounded text-sm">{{ $player->wins }}</span>
                                </td>
                                <td class="py-3 text-center">
                                    <span class="px-2 py-1 bg-red-100 text-red-700 font-bold rounded text-sm">{{ $player->losses }}</span>
                                </td>
                                <td class="py-3 text-center">
                                    <span class="font-bold text-pool-green">{{ $player->win_rate }}%</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <span class="text-4xl">👥</span>
                    <p class="text-gray-500 mt-2">No players with matches yet</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Matches -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-purple-500 to-violet-600 px-6 py-4">
            <h2 class="text-lg font-bold text-white flex items-center gap-2">
                🎱 Recent Matches
            </h2>
        </div>
        <div class="p-6">
            @if($recentMatches->count() > 0)
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @foreach($recentMatches as $match)
                    <div class="p-4 bg-gray-50 rounded-xl hover:bg-purple-50 transition-colors">
                        <div class="flex justify-between items-center">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 text-sm">
                                    <span class="{{ $match->winner_id === $match->player1_id ? 'font-bold text-green-600' : 'text-gray-600' }}">
                                        {{ $match->winner_id === $match->player1_id ? '🏆' : '' }} {{ $match->player1?->display_name ?? 'TBD' }}
                                    </span>
                                    <span class="text-gray-400">vs</span>
                                    <span class="{{ $match->winner_id === $match->player2_id ? 'font-bold text-green-600' : 'text-gray-600' }}">
                                        {{ $match->winner_id === $match->player2_id ? '🏆' : '' }} {{ $match->player2?->display_name ?? 'TBD' }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-400 mt-1">
                                    {{ $match->tournament->name }} • {{ $match->tournament->getRoundName($match->round) }}
                                </p>
                            </div>
                            <div class="text-right ml-4">
                                <span class="font-mono font-bold text-lg {{ $match->player1_score > $match->player2_score ? 'text-green-600' : 'text-gray-600' }}">{{ $match->player1_score }}</span>
                                <span class="text-gray-400 mx-1">-</span>
                                <span class="font-mono font-bold text-lg {{ $match->player2_score > $match->player1_score ? 'text-green-600' : 'text-gray-600' }}">{{ $match->player2_score }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <span class="text-4xl">🎱</span>
                    <p class="text-gray-500 mt-2">No matches played yet</p>
                </div>
            @endif
        </div>
    </div>
</div>

@if(Auth::user()->isAdmin())
<!-- Quick Actions for Admin -->
<div class="mt-8 bg-white rounded-2xl shadow-lg p-6">
    <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
        ⚡ Quick Actions
    </h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('tournaments.create') }}" class="p-4 bg-gradient-to-br from-pool-green to-pool-felt text-white rounded-xl hover:shadow-lg transition-all duration-300 text-center group">
            <span class="text-3xl block mb-2 group-hover:scale-110 transition-transform">🏆</span>
            <span class="font-semibold">Create Tournament</span>
        </a>
        <a href="{{ route('players.create') }}" class="p-4 bg-gradient-to-br from-blue-500 to-indigo-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 text-center group">
            <span class="text-3xl block mb-2 group-hover:scale-110 transition-transform">👤</span>
            <span class="font-semibold">Add Player</span>
        </a>
        <a href="{{ route('players.index') }}" class="p-4 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 hover:shadow-lg transition-all duration-300 text-center group">
            <span class="text-3xl block mb-2 group-hover:scale-110 transition-transform">📋</span>
            <span class="font-semibold">Manage Players</span>
        </a>
        <a href="{{ route('tournaments.index') }}" class="p-4 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 hover:shadow-lg transition-all duration-300 text-center group">
            <span class="text-3xl block mb-2 group-hover:scale-110 transition-transform">📊</span>
            <span class="font-semibold">All Tournaments</span>
        </a>
    </div>
</div>
@endif
@endsection
