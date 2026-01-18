@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
<!-- Hero Section -->
<div class="relative -mt-8 -mx-4 sm:-mx-6 lg:-mx-8 mb-16">
    <div class="bg-gradient-hero py-20 px-4 sm:px-6 lg:px-8 rounded-b-3xl shadow-2xl overflow-hidden relative">
        <!-- Decorative elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>
        </div>

        <div class="max-w-4xl mx-auto text-center relative z-10">
            <div class="inline-block mb-6 animate-bounce-slow">
                <span class="text-8xl md:text-9xl filter drop-shadow-lg">🎱</span>
            </div>
            <h1 class="text-4xl md:text-6xl font-extrabold text-white mb-6 tracking-tight">
                Welcome to <span class="text-gold">Theatro Pool</span>
            </h1>
            <p class="text-xl md:text-2xl text-white/80 max-w-2xl mx-auto mb-10 leading-relaxed">
                The ultimate 8-Ball Pool tournament management system.
                Create epic tournaments, track champions, and celebrate victories.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('tournaments.index') }}"
                   class="group px-8 py-4 bg-white text-brand font-bold rounded-xl hover:bg-gold hover:text-black transition-all duration-300 transform hover:scale-105 hover:shadow-xl flex items-center justify-center gap-2">
                    <span class="text-xl">🏆</span>
                    View Tournaments
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
                <a href="{{ route('players.index') }}"
                   class="group px-8 py-4 bg-transparent text-white font-bold rounded-xl border-2 border-white/50 hover:bg-white/10 hover:border-white transition-all duration-300 flex items-center justify-center gap-2">
                    <span class="text-xl">👥</span>
                    Browse Players
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Live Stats Section -->
<div class="mb-16">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 card-hover text-center group">
            <div class="w-16 h-16 bg-gradient-to-br from-brand to-brand-light rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                <span class="text-3xl text-white">🏆</span>
            </div>
            <div class="text-5xl font-black text-brand dark:text-gold mb-2 stat-number">{{ $stats['tournaments'] }}</div>
            <div class="text-text-muted dark:text-gray-400 font-medium uppercase tracking-wide text-sm">Tournaments</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 card-hover text-center group">
            <div class="w-16 h-16 bg-gradient-to-br from-brand to-brand-light rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                <span class="text-3xl text-white">👥</span>
            </div>
            <div class="text-5xl font-black text-brand dark:text-gold mb-2 stat-number">{{ $stats['players'] }}</div>
            <div class="text-text-muted dark:text-gray-400 font-medium uppercase tracking-wide text-sm">Players</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 card-hover text-center group">
            <div class="w-16 h-16 bg-gradient-to-br from-brand to-brand-light rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                <span class="text-3xl text-white">🎱</span>
            </div>
            <div class="text-5xl font-black text-brand dark:text-gold mb-2 stat-number">{{ $stats['matches'] }}</div>
            <div class="text-text-muted dark:text-gray-400 font-medium uppercase tracking-wide text-sm">Matches Played</div>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="mb-16">
    <h2 class="text-3xl font-bold text-center text-text-primary dark:text-white mb-12">
        Why Choose <span class="text-brand dark:text-gold">Theatro Pool</span>?
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 card-hover group">
            <div class="w-14 h-14 bg-gradient-to-br from-brand to-blue-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                <span class="text-2xl">🏆</span>
            </div>
            <h3 class="text-xl font-bold text-text-primary dark:text-white mb-3">Tournament Management</h3>
            <p class="text-text-secondary dark:text-gray-300 leading-relaxed">
                Create and manage single-elimination tournaments for 8, 16, or 32 players with automatic bracket generation.
            </p>
            <ul class="mt-4 space-y-2 text-sm text-text-muted dark:text-gray-400">
                <li class="flex items-center gap-2"><span class="text-green-500">✓</span> Automatic seeding</li>
                <li class="flex items-center gap-2"><span class="text-green-500">✓</span> Live bracket updates</li>
                <li class="flex items-center gap-2"><span class="text-green-500">✓</span> Winner advancement</li>
            </ul>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 card-hover group">
            <div class="w-14 h-14 bg-gradient-to-br from-success to-success-light rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                <span class="text-2xl">📊</span>
            </div>
            <h3 class="text-xl font-bold text-text-primary dark:text-white mb-3">Player Statistics</h3>
            <p class="text-text-secondary dark:text-gray-300 leading-relaxed">
                Track comprehensive player stats including wins, losses, win rate, and tournament history.
            </p>
            <ul class="mt-4 space-y-2 text-sm text-text-muted dark:text-gray-400">
                <li class="flex items-center gap-2"><span class="text-green-500">✓</span> Win/Loss tracking</li>
                <li class="flex items-center gap-2"><span class="text-green-500">✓</span> Player rankings</li>
                <li class="flex items-center gap-2"><span class="text-green-500">✓</span> Match history</li>
            </ul>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 card-hover group">
            <div class="w-14 h-14 bg-gradient-to-br from-brand-light to-success rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                <span class="text-2xl">🎯</span>
            </div>
            <h3 class="text-xl font-bold text-text-primary dark:text-white mb-3">Live Brackets</h3>
            <p class="text-text-secondary dark:text-gray-300 leading-relaxed">
                Beautiful visual bracket display with real-time updates as matches are completed.
            </p>
            <ul class="mt-4 space-y-2 text-sm text-text-muted dark:text-gray-400">
                <li class="flex items-center gap-2"><span class="text-green-500">✓</span> Visual brackets</li>
                <li class="flex items-center gap-2"><span class="text-green-500">✓</span> Score tracking</li>
                <li class="flex items-center gap-2"><span class="text-green-500">✓</span> Champion display</li>
            </ul>
        </div>
    </div>
</div>

<!-- Active Tournaments Section -->
@if($activeTournaments->count() > 0)
<div class="mb-16">
    <div class="flex items-center justify-between mb-8">
        <h2 class="text-3xl font-bold text-text-primary dark:text-white flex items-center gap-3">
            <span class="w-10 h-10 bg-green-100 dark:bg-green-900/50 rounded-lg flex items-center justify-center">🔴</span>
            Live Tournaments
        </h2>
        <a href="{{ route('tournaments.index') }}" class="text-brand dark:text-gold font-semibold hover:underline flex items-center gap-1">
            View All
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($activeTournaments as $tournament)
        <a href="{{ route('tournaments.show', $tournament) }}"
           class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden card-hover group">
            <div class="h-2 bg-gradient-to-r from-success to-success-light"></div>
            <div class="p-6">
                <div class="flex items-start justify-between mb-3">
                    <h3 class="text-xl font-bold text-text-primary dark:text-white group-hover:text-brand dark:group-hover:text-gold transition-colors">{{ $tournament->name }}</h3>
                    <span class="px-3 py-1 bg-green-100 dark:bg-green-900/50 text-success dark:text-green-400 text-xs font-bold rounded-full animate-pulse">LIVE</span>
                </div>
                <p class="text-text-muted dark:text-gray-400 flex items-center gap-2 mb-2">
                    <span>📍</span> {{ $tournament->location }}
                </p>
                <div class="flex justify-between items-center mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <span class="text-sm text-text-muted dark:text-gray-400">{{ $tournament->players->count() }} players</span>
                    <span class="text-sm font-semibold text-brand dark:text-gold">{{ $tournament->matches->where('status', 'completed')->count() }} matches played</span>
                </div>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endif

@if($upcomingTournaments->count() > 0)
<div class="mb-16">
    <div class="flex items-center justify-between mb-8">
        <h2 class="text-3xl font-bold text-text-primary dark:text-white flex items-center gap-3">
            <span class="w-10 h-10 bg-blue-100 dark:bg-blue-900/50 rounded-lg flex items-center justify-center">📅</span>
            Upcoming Tournaments
        </h2>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($upcomingTournaments as $tournament)
        <a href="{{ route('tournaments.show', $tournament) }}"
           class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden card-hover group">
            <div class="h-2 bg-gradient-to-r from-brand to-brand-light"></div>
            <div class="p-6">
                <div class="flex items-start justify-between mb-3">
                    <h3 class="text-xl font-bold text-text-primary dark:text-white group-hover:text-brand dark:group-hover:text-gold transition-colors">{{ $tournament->name }}</h3>
                    <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-400 text-xs font-bold rounded-full">UPCOMING</span>
                </div>
                <p class="text-text-muted dark:text-gray-400 flex items-center gap-2 mb-2">
                    <span>📅</span> {{ $tournament->start_date->format('M d, Y') }}
                </p>
                <p class="text-text-muted dark:text-gray-400 flex items-center gap-2">
                    <span>📍</span> {{ $tournament->location }}
                </p>
                <div class="mt-4">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-text-muted dark:text-gray-400">Registration</span>
                        <span class="font-semibold text-text-secondary dark:text-gray-300">{{ $tournament->players->count() }}/{{ $tournament->max_players }}</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="bg-gradient-to-r from-brand to-brand-light h-2 rounded-full progress-bar"
                             style="width: {{ ($tournament->players->count() / $tournament->max_players) * 100 }}%"></div>
                    </div>
                </div>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endif

<!-- Top Players Section -->
@if($topPlayers->count() > 0)
<div class="mb-16">
    <h2 class="text-3xl font-bold text-center text-text-primary dark:text-white mb-8 flex items-center justify-center gap-3">
        <span class="text-4xl">👑</span>
        Hall of Fame
    </h2>
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
        <div class="grid grid-cols-1 md:grid-cols-5 divide-y md:divide-y-0 md:divide-x divide-gray-100 dark:divide-gray-700">
            @foreach($topPlayers as $index => $player)
            <a href="{{ route('players.show', $player) }}" class="p-6 text-center hover:bg-surface dark:hover:bg-gray-700/50 transition group">
                <div class="relative inline-block mb-3">
                    <div class="w-16 h-16 bg-gradient-to-br from-brand to-brand-light rounded-full flex items-center justify-center text-white text-2xl font-bold group-hover:scale-110 transition-transform">
                        {{ substr($player->name, 0, 1) }}
                    </div>
                    @if($index === 0)
                        <span class="absolute -top-2 -right-2 text-2xl trophy-shine">🥇</span>
                    @elseif($index === 1)
                        <span class="absolute -top-2 -right-2 text-2xl">🥈</span>
                    @elseif($index === 2)
                        <span class="absolute -top-2 -right-2 text-2xl">🥉</span>
                    @endif
                </div>
                <h4 class="font-bold text-text-primary dark:text-white group-hover:text-brand dark:group-hover:text-gold transition-colors">{{ $player->display_name }}</h4>
                <div class="text-sm text-text-muted dark:text-gray-400 mt-1">{{ $player->wins }}W - {{ $player->losses }}L</div>
                <div class="text-xs font-semibold text-brand dark:text-gold mt-1">{{ $player->win_rate }}% Win Rate</div>
            </a>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- CTA Section -->
<div class="text-center bg-gradient-to-r from-brand via-pool-felt to-pool-green rounded-3xl p-12 text-white shadow-2xl">
    <h2 class="text-3xl md:text-4xl font-bold mb-4">Ready to Host Your Tournament?</h2>
    <p class="text-xl text-white/80 mb-8 max-w-2xl mx-auto">
        Join thousands of billiard halls using Theatro Pool to manage their tournaments professionally.
    </p>
    @auth
        <a href="{{ route('tournaments.create') }}"
           class="inline-flex items-center gap-2 px-8 py-4 bg-white text-brand font-bold rounded-xl hover:bg-gold hover:text-black transition-all duration-300 transform hover:scale-105 shadow-lg">
            <span class="text-xl">🏆</span>
            Create Tournament
        </a>
    @else
        <a href="{{ route('register') }}"
           class="inline-flex items-center gap-2 px-8 py-4 bg-white text-brand font-bold rounded-xl hover:bg-gold hover:text-black transition-all duration-300 transform hover:scale-105 shadow-lg">
            Get Started Free
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
            </svg>
        </a>
    @endauth
</div>
@endsection
