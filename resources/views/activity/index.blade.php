@extends('layouts.app')

@section('title', 'Recent Activity')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-text-primary flex items-center gap-3">
        <span class="w-12 h-12 bg-gradient-to-br from-brand to-brand-light rounded-xl flex items-center justify-center text-white text-2xl">📰</span>
        Recent Activity
    </h1>
    <p class="text-text-secondary mt-2">Stay updated with the latest matches and tournament updates</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Activity Feed -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Today's Matches -->
        @if($todayMatches->count() > 0)
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-success to-success-light p-4">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <span>📅</span> Today's Results
                    <span class="ml-auto px-2 py-0.5 bg-white/20 rounded-full text-sm">{{ $todayMatches->count() }}</span>
                </h3>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($todayMatches as $match)
                <div class="p-4 hover:bg-surface transition">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center gap-2">
                                <span class="font-medium {{ $match->winner_id === $match->player1_id ? 'text-success' : 'text-text-secondary' }}">
                                    {{ $match->player1->display_name }}
                                </span>
                                @if($match->winner_id === $match->player1_id)
                                    <span class="text-xs">🏆</span>
                                @endif
                            </div>
                            <span class="font-bold text-gray-400">
                                {{ $match->player1_score }} - {{ $match->player2_score }}
                            </span>
                            <div class="flex items-center gap-2">
                                <span class="font-medium {{ $match->winner_id === $match->player2_id ? 'text-success' : 'text-text-secondary' }}">
                                    {{ $match->player2->display_name }}
                                </span>
                                @if($match->winner_id === $match->player2_id)
                                    <span class="text-xs">🏆</span>
                                @endif
                            </div>
                        </div>
                        <div class="text-right">
                            <a href="{{ route('tournaments.show', $match->tournament) }}" class="text-sm text-brand hover:underline">
                                {{ $match->tournament->name }}
                            </a>
                            <div class="text-xs text-gray-400">{{ $match->updated_at->format('g:i A') }}</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Recent Match Results -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-brand to-brand-light p-4">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <span>🎱</span> Recent Matches
                </h3>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($recentMatches as $match)
                <div class="p-4 hover:bg-surface transition">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-brand to-brand-light rounded-full flex items-center justify-center text-white text-sm font-bold">
                                {{ substr($match->winner->name ?? '?', 0, 1) }}
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('players.show', $match->player1) }}" class="font-medium {{ $match->winner_id === $match->player1_id ? 'text-success' : 'text-text-secondary' }} hover:underline">
                                        {{ $match->player1->display_name }}
                                    </a>
                                    <span class="font-bold text-gray-400">{{ $match->player1_score }} - {{ $match->player2_score }}</span>
                                    <a href="{{ route('players.show', $match->player2) }}" class="font-medium {{ $match->winner_id === $match->player2_id ? 'text-success' : 'text-text-secondary' }} hover:underline">
                                        {{ $match->player2->display_name }}
                                    </a>
                                </div>
                                <div class="text-sm text-text-muted">
                                    <a href="{{ route('tournaments.show', $match->tournament) }}" class="hover:text-brand">{{ $match->tournament->name }}</a>
                                    · {{ $match->tournament->getRoundName($match->round) }}
                                </div>
                            </div>
                        </div>
                        <div class="text-sm text-gray-400">
                            {{ $match->updated_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-text-muted">
                    <span class="text-4xl mb-4 block">🎱</span>
                    No matches played yet.
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Live Tournaments -->
        @if($liveTournaments->count() > 0)
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-red-500 to-rose-600 p-4">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <span class="animate-pulse">🔴</span> Live Now
                </h3>
            </div>
            <div class="p-4 space-y-3">
                @foreach($liveTournaments as $tournament)
                <a href="{{ route('tournaments.show', $tournament) }}" class="block p-3 bg-surface rounded-xl hover:bg-surface-alt transition">
                    <div class="font-bold text-text-primary">{{ $tournament->name }}</div>
                    <div class="text-sm text-text-muted flex items-center gap-2 mt-1">
                        <span>📍 {{ $tournament->location }}</span>
                    </div>
                    <div class="text-xs text-success mt-1">
                        {{ $tournament->players_count }} players competing
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Recent Tournaments -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-brand to-brand-light p-4">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <span>🏆</span> Recent Tournaments
                </h3>
            </div>
            <div class="p-4 space-y-3">
                @foreach($recentTournaments as $tournament)
                <a href="{{ route('tournaments.show', $tournament) }}" class="block p-3 bg-surface rounded-xl hover:bg-surface-alt transition">
                    <div class="flex items-center justify-between">
                        <div class="font-medium text-text-primary">{{ $tournament->name }}</div>
                        <span class="px-2 py-0.5 text-xs rounded-full font-bold
                            {{ $tournament->status === 'ongoing' ? 'bg-green-100 text-success' : '' }}
                            {{ $tournament->status === 'upcoming' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $tournament->status === 'finished' ? 'bg-surface-alt text-text-secondary' : '' }}">
                            {{ ucfirst($tournament->status) }}
                        </span>
                    </div>
                    <div class="text-xs text-text-muted mt-1">
                        {{ $tournament->start_date->format('M d, Y') }}
                    </div>
                </a>
                @endforeach
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-text-primary mb-4 flex items-center gap-2">
                <span>📊</span> Quick Stats
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-text-secondary">Matches Today</span>
                    <span class="font-bold text-brand">{{ $todayMatches->count() }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-text-secondary">Live Tournaments</span>
                    <span class="font-bold text-brand">{{ $liveTournaments->count() }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-text-secondary">Recent Matches</span>
                    <span class="font-bold text-brand">{{ $recentMatches->count() }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
