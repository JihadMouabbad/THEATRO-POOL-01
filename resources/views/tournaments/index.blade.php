@extends('layouts.app')

@section('title', 'Tournaments')

@section('content')
<!-- Header -->
<div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-8">
    <div>
        <h1 class="text-3xl font-bold text-text-primary dark:text-white flex items-center gap-3">
            <span class="w-10 h-10 bg-gradient-to-br from-brand to-brand-light rounded-lg flex items-center justify-center text-white">🏆</span>
            Tournaments
        </h1>
        <p class="text-text-muted dark:text-gray-400 mt-1">Browse and manage pool tournaments</p>
    </div>
    @auth
        @if(Auth::user()->isAdmin())
        <a href="{{ route('tournaments.create') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-brand hover:bg-success text-white font-semibold rounded-xl hover:shadow-lg transition-all duration-300 transform hover:scale-105">
            <span>➕</span> Create Tournament
        </a>
        @endif
    @endauth
</div>

<!-- Search and Filter -->
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-8">
    <form action="{{ route('tournaments.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
        <div class="flex-1 relative">
            <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-text-muted">🔍</span>
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Search tournaments by name or location..."
                   class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-brand focus:border-brand transition bg-white dark:bg-gray-700 dark:text-white dark:placeholder-gray-400">
        </div>
        <div class="flex gap-3">
            <select name="status" class="px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-brand focus:border-brand transition bg-white dark:bg-gray-700 dark:text-white">
                <option value="">All Statuses</option>
                <option value="upcoming" {{ request('status') === 'upcoming' ? 'selected' : '' }}>📅 Upcoming</option>
                <option value="ongoing" {{ request('status') === 'ongoing' ? 'selected' : '' }}>🔴 Ongoing</option>
                <option value="finished" {{ request('status') === 'finished' ? 'selected' : '' }}>✅ Finished</option>
            </select>
            <button type="submit" class="px-6 py-3 bg-brand hover:bg-success text-white font-semibold rounded-xl hover:shadow-lg transition-all duration-300">
                Filter
            </button>
        </div>
    </form>
</div>

<!-- Tournaments Grid -->
@if($tournaments->count() > 0)
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($tournaments as $tournament)
    <a href="{{ route('tournaments.show', $tournament) }}" class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden card-hover group">
        <div class="h-2
            {{ $tournament->status === 'upcoming' ? 'bg-gradient-to-r from-brand to-brand-light' : '' }}
            {{ $tournament->status === 'ongoing' ? 'bg-gradient-to-r from-success to-success-light' : '' }}
            {{ $tournament->status === 'finished' ? 'bg-gradient-to-r from-gray-300 to-gray-400' : '' }}
        "></div>
        <div class="p-6">
            <div class="flex justify-between items-start mb-3">
                <h3 class="text-xl font-bold text-text-primary dark:text-white group-hover:text-brand dark:group-hover:text-gold transition-colors">{{ $tournament->name }}</h3>
                <span class="px-3 py-1 text-xs font-bold rounded-full
                    {{ $tournament->status === 'upcoming' ? 'bg-brand/20 text-brand dark:text-gold' : '' }}
                    {{ $tournament->status === 'ongoing' ? 'bg-success/20 text-success animate-pulse' : '' }}
                    {{ $tournament->status === 'finished' ? 'bg-surface-alt dark:bg-gray-700 text-text-muted dark:text-gray-400' : '' }}
                ">
                    {{ $tournament->status === 'ongoing' ? '🔴 LIVE' : strtoupper($tournament->status) }}
                </span>
            </div>
            <div class="space-y-2 text-sm text-text-secondary dark:text-gray-300 mb-4">
                <p class="flex items-center gap-2">
                    <span class="w-6 h-6 bg-surface-alt dark:bg-gray-700 rounded-lg flex items-center justify-center text-xs">📍</span>
                    {{ $tournament->location }}
                </p>
                <p class="flex items-center gap-2">
                    <span class="w-6 h-6 bg-surface-alt dark:bg-gray-700 rounded-lg flex items-center justify-center text-xs">📅</span>
                    {{ $tournament->start_date->format('M d, Y') }}
                </p>
            </div>
            <div class="flex justify-between items-center text-sm">
                <span class="text-text-muted dark:text-gray-400">
                    <span class="font-bold text-brand dark:text-gold">{{ $tournament->players_count }}</span>/{{ $tournament->max_players }} players
                </span>
                @if($tournament->status === 'ongoing')
                    <span class="text-success font-medium">{{ $tournament->matches()->where('status', 'completed')->count() }} matches</span>
                @endif
            </div>
            @if($tournament->status === 'upcoming')
            <div class="mt-4">
                <div class="flex justify-between text-xs text-text-muted dark:text-gray-400 mb-1">
                    <span>Registration</span>
                    <span>{{ round(($tournament->players_count / $tournament->max_players) * 100) }}%</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 overflow-hidden">
                    <div class="bg-gradient-to-r from-brand to-success h-full rounded-full progress-bar"
                         style="width: {{ ($tournament->players_count / $tournament->max_players) * 100 }}%"></div>
                </div>
            </div>
            @endif
        </div>
    </a>
    @endforeach
</div>

<!-- Pagination -->
<div class="mt-8">
    {{ $tournaments->links() }}
</div>
@else
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-16 text-center">
    <div class="w-24 h-24 bg-surface-alt dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-6">
        <span class="text-5xl">🏆</span>
    </div>
    <h2 class="text-2xl font-bold text-text-primary dark:text-gray-200 mb-2">No Tournaments Found</h2>
    <p class="text-text-muted dark:text-gray-400 max-w-md mx-auto">
        {{ request('search') ? 'Try adjusting your search terms or filters.' : 'Be the first to create an exciting tournament!' }}
    </p>
    @auth
        @if(Auth::user()->isAdmin())
        <a href="{{ route('tournaments.create') }}" class="inline-flex items-center gap-2 mt-6 px-8 py-3 bg-brand hover:bg-success text-white font-bold rounded-xl hover:shadow-lg transition-all duration-300 transform hover:scale-105">
            <span>➕</span> Create First Tournament
        </a>
        @endif
    @endauth
</div>
@endif
@endsection
