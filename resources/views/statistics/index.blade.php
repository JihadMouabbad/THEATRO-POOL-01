@extends('layouts.app')

@section('title', 'Statistics & Analytics')

@section('content')
<!-- Page Header -->
<div class="mb-8">
    <h1 class="text-3xl font-bold text-text-primary dark:text-white flex items-center gap-3">
        <span class="w-12 h-12 bg-gradient-to-br from-brand-light to-success rounded-xl flex items-center justify-center text-white shadow-lg">📊</span>
        Statistics & Analytics
    </h1>
    <p class="text-text-muted dark:text-gray-400 mt-2">Comprehensive statistics about Theatro Pool tournaments and players</p>
</div>

<!-- Main Statistics Cards -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-gradient-to-br from-brand to-brand-light rounded-2xl shadow-lg p-6 text-white card-hover">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-white/80 text-sm font-medium">Total Players</p>
                <p class="text-4xl font-black mt-1">{{ $overallStats['total_players'] }}</p>
            </div>
            <span class="text-4xl opacity-30">👥</span>
        </div>
        <p class="text-white/70 text-sm mt-3">{{ $overallStats['active_players'] }} active players</p>
    </div>
    <div class="bg-gradient-to-br from-success to-success-light rounded-2xl shadow-lg p-6 text-white card-hover">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-white/80 text-sm font-medium">Tournaments</p>
                <p class="text-4xl font-black mt-1">{{ $overallStats['total_tournaments'] }}</p>
            </div>
            <span class="text-4xl opacity-30">🏆</span>
        </div>
        <p class="text-white/70 text-sm mt-3">{{ $overallStats['completed_tournaments'] }} completed</p>
    </div>
    <div class="bg-gradient-to-br from-yellow-500 to-orange-500 rounded-2xl shadow-lg p-6 text-white card-hover">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-white/80 text-sm font-medium">Active Now</p>
                <p class="text-4xl font-black mt-1">{{ $overallStats['ongoing_tournaments'] }}</p>
            </div>
            <span class="text-4xl opacity-30">🔴</span>
        </div>
        <p class="text-white/70 text-sm mt-3">{{ $overallStats['upcoming_tournaments'] }} upcoming</p>
    </div>
    <div class="bg-gradient-to-br from-brand-light to-success rounded-2xl shadow-lg p-6 text-white card-hover">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-white/80 text-sm font-medium">Total Matches</p>
                <p class="text-4xl font-black mt-1">{{ $overallStats['total_matches'] }}</p>
            </div>
            <span class="text-4xl opacity-30">🎱</span>
        </div>
        <p class="text-white/70 text-sm mt-3">Completed games</p>
    </div>
</div>

<!-- ELO Ratings Section -->
@if(isset($topRated) && $topRated->count() > 0)
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden mb-8">
    <div class="bg-gradient-to-r from-red-500 to-rose-600 px-6 py-4">
        <h2 class="text-xl font-bold text-white flex items-center gap-2">
            ⚔️ ELO Rankings - Top Rated Players
        </h2>
        <p class="text-white/80 text-sm">Skill-based rating system (starting rating: 1000)</p>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($topRated as $index => $player)
            <a href="{{ route('players.show', $player) }}" class="flex items-center gap-4 p-4 rounded-xl hover:shadow-md transition group border-l-4
                {{ $index === 0 ? 'border-yellow-400 bg-yellow-50 dark:bg-yellow-900/20' : '' }}
                {{ $index === 1 ? 'border-gray-400 bg-surface-alt dark:bg-gray-700' : '' }}
                {{ $index === 2 ? 'border-amber-600 bg-amber-50 dark:bg-amber-900/20' : '' }}
                {{ $index > 2 ? 'border-transparent bg-surface dark:bg-gray-700' : '' }}
            ">
                <div class="w-10 text-center">
                    @if($index === 0)
                        <span class="text-2xl">🥇</span>
                    @elseif($index === 1)
                        <span class="text-2xl">🥈</span>
                    @elseif($index === 2)
                        <span class="text-2xl">🥉</span>
                    @else
                        <span class="font-bold text-gray-400 dark:text-text-muted">#{{ $index + 1 }}</span>
                    @endif
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-rose-600 rounded-xl flex items-center justify-center text-white font-bold text-lg">
                    {{ substr($player->name, 0, 1) }}
                </div>
                <div class="flex-1">
                    <span class="font-bold text-text-primary dark:text-white group-hover:text-brand dark:group-hover:text-gold block">{{ $player->display_name }}</span>
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $player->tier['color'] }} bg-surface-alt dark:bg-gray-600">{{ $player->tier['title'] }}</span>
                </div>
                <div class="text-right">
                    <span class="text-2xl font-black text-danger dark:text-red-400">{{ $player->ranking_points ?? 1000 }}</span>
                    <span class="block text-xs text-text-muted dark:text-gray-400">ELO</span>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Top Scorers -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-brand to-brand-light px-6 py-4">
            <h2 class="text-lg font-bold text-white flex items-center gap-2">
                🏆 Top Scorers (By Wins)
            </h2>
        </div>
        <div class="p-6">
            @if($topScorers->count() > 0)
            <div class="space-y-3">
                @foreach($topScorers as $index => $player)
                <a href="{{ route('players.show', $player) }}" class="flex items-center gap-4 p-3 bg-surface dark:bg-gray-700 rounded-xl hover:bg-brand/5 dark:hover:bg-brand/10 transition group">
                    <div class="w-10 text-center">
                        @if($index === 0)
                            <span class="text-2xl">🥇</span>
                        @elseif($index === 1)
                            <span class="text-2xl">🥈</span>
                        @elseif($index === 2)
                            <span class="text-2xl">🥉</span>
                        @else
                            <span class="font-bold text-gray-400 dark:text-text-muted">#{{ $index + 1 }}</span>
                        @endif
                    </div>
                    <div class="w-10 h-10 bg-gradient-to-br from-brand to-brand-light rounded-lg flex items-center justify-center text-white font-bold">
                        {{ substr($player->name, 0, 1) }}
                    </div>
                    <div class="flex-1">
                        <span class="font-medium text-text-primary dark:text-white group-hover:text-brand dark:group-hover:text-gold">{{ $player->display_name }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-2xl font-black text-success dark:text-green-400">{{ $player->wins }}</span>
                        <span class="text-sm text-text-muted dark:text-gray-400 ml-1">wins</span>
                    </div>
                </a>
                @endforeach
            </div>
            @else
            <div class="text-center py-8">
                <span class="text-4xl">🎱</span>
                <p class="text-text-muted mt-2">No data yet</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Highest Win Rates -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-yellow-400 to-amber-500 px-6 py-4">
            <h2 class="text-lg font-bold text-white flex items-center gap-2">
                ⚡ Highest Win Rates
            </h2>
            <p class="text-white/80 text-sm">Minimum 3 matches</p>
        </div>
        <div class="p-6">
            @if($highestWinRates->count() > 0)
            <div class="space-y-3">
                @foreach($highestWinRates as $index => $player)
                <a href="{{ route('players.show', $player) }}" class="flex items-center gap-4 p-3 bg-surface rounded-xl hover:bg-yellow-50 transition group">
                    <div class="w-10 text-center">
                        @if($index === 0)
                            <span class="text-2xl">🥇</span>
                        @elseif($index === 1)
                            <span class="text-2xl">🥈</span>
                        @elseif($index === 2)
                            <span class="text-2xl">🥉</span>
                        @else
                            <span class="font-bold text-gray-400">#{{ $index + 1 }}</span>
                        @endif
                    </div>
                    <div class="w-10 h-10 bg-gradient-to-br from-yellow-400 to-amber-500 rounded-lg flex items-center justify-center text-white font-bold">
                        {{ substr($player->name, 0, 1) }}
                    </div>
                    <div class="flex-1">
                        <span class="font-medium text-text-primary group-hover:text-brand">{{ $player->display_name }}</span>
                        <span class="text-sm text-text-muted ml-2">{{ $player->total_matches }} matches</span>
                    </div>
                    <div class="text-right">
                        <span class="text-2xl font-black text-yellow-600">{{ $player->win_rate }}%</span>
                    </div>
                </a>
                @endforeach
            </div>
            @else
            <div class="text-center py-8">
                <span class="text-4xl">📊</span>
                <p class="text-text-muted mt-2">No data yet</p>
            </div>
            @endif
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Most Championships -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-brand-light to-success px-6 py-4">
            <h2 class="text-lg font-bold text-white flex items-center gap-2">
                👑 Most Championships
            </h2>
        </div>
        <div class="p-6">
            @if($mostChampionships->count() > 0)
            <div class="space-y-3">
                @foreach($mostChampionships as $index => $player)
                <a href="{{ route('players.show', $player) }}" class="flex items-center gap-4 p-3 bg-gradient-to-r from-purple-50 to-violet-50 rounded-xl hover:from-purple-100 hover:to-violet-100 transition group">
                    <div class="w-10 text-center">
                        <span class="text-2xl">🏆</span>
                    </div>
                    <div class="w-10 h-10 bg-gradient-to-br from-brand-light to-success rounded-lg flex items-center justify-center text-white font-bold">
                        {{ substr($player->name, 0, 1) }}
                    </div>
                    <div class="flex-1">
                        <span class="font-medium text-text-primary group-hover:text-brand">{{ $player->display_name }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-2xl font-black text-purple-600">{{ $player->championships }}</span>
                        <span class="text-sm text-text-muted ml-1">{{ $player->championships === 1 ? 'title' : 'titles' }}</span>
                    </div>
                </a>
                @endforeach
            </div>
            @else
            <div class="text-center py-8">
                <span class="text-4xl">👑</span>
                <p class="text-text-muted mt-2">No champions yet</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Tournament Format Popularity -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-brand to-brand-light px-6 py-4">
            <h2 class="text-lg font-bold text-white flex items-center gap-2">
                📈 Tournament Formats
            </h2>
        </div>
        <div class="p-6">
            @if($popularFormats->count() > 0)
            <div class="space-y-4">
                @foreach($popularFormats as $format)
                @php
                    $percentage = $overallStats['total_tournaments'] > 0
                        ? round(($format->count / $overallStats['total_tournaments']) * 100)
                        : 0;
                @endphp
                <div class="p-4 bg-surface rounded-xl">
                    <div class="flex justify-between items-center mb-2">
                        <span class="font-bold text-text-primary">{{ $format->max_players }} Players</span>
                        <span class="text-sm text-text-muted">{{ $format->count }} tournaments</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-gradient-to-r from-brand to-brand-light h-3 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                    </div>
                    <div class="text-right text-sm text-text-muted mt-1">{{ $percentage }}%</div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8">
                <span class="text-4xl">📈</span>
                <p class="text-text-muted mt-2">No data yet</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Recent Matches -->
<div class="bg-white rounded-2xl shadow-lg overflow-hidden">
    <div class="bg-gradient-to-r from-gray-700 to-gray-900 px-6 py-4 flex justify-between items-center">
        <h2 class="text-lg font-bold text-white flex items-center gap-2">
            🎱 Recent Matches
        </h2>
        <a href="{{ route('tournaments.index') }}" class="text-white/80 hover:text-white text-sm transition">
            View Tournaments →
        </a>
    </div>
    <div class="p-6">
        @if($recentMatches->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($recentMatches as $match)
            <div class="p-4 bg-surface rounded-xl hover:bg-surface-alt transition">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-text-muted font-medium">
                        {{ $match->tournament->name }} • {{ $match->tournament->getRoundName($match->round) }}
                    </span>
                    <span class="text-xs text-gray-400">
                        {{ $match->completed_at ? $match->completed_at->diffForHumans() : '' }}
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="{{ $match->winner_id === $match->player1_id ? 'text-success font-bold' : 'text-text-secondary' }}">
                            {{ $match->winner_id === $match->player1_id ? '🏆' : '' }}
                            {{ $match->player1?->display_name ?? 'TBD' }}
                        </span>
                        <span class="text-gray-400 mx-2">vs</span>
                        <span class="{{ $match->winner_id === $match->player2_id ? 'text-success font-bold' : 'text-text-secondary' }}">
                            {{ $match->winner_id === $match->player2_id ? '🏆' : '' }}
                            {{ $match->player2?->display_name ?? 'TBD' }}
                        </span>
                    </div>
                    <div class="font-mono font-bold">
                        <span class="{{ $match->player1_score > $match->player2_score ? 'text-success' : 'text-text-muted' }}">{{ $match->player1_score }}</span>
                        <span class="text-gray-400 mx-1">-</span>
                        <span class="{{ $match->player2_score > $match->player1_score ? 'text-success' : 'text-text-muted' }}">{{ $match->player2_score }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-12">
            <span class="text-5xl">🎱</span>
            <p class="text-text-muted mt-3">No matches played yet</p>
        </div>
        @endif
    </div>
</div>
@endsection
