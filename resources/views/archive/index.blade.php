@extends('layouts.app')

@section('title', 'Tournament Archive')

@section('content')
<!-- Page Header -->
<div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-text-primary dark:text-white flex items-center gap-3">
            <span class="w-12 h-12 bg-gradient-to-br from-gray-600 to-gray-800 rounded-xl flex items-center justify-center text-white shadow-lg">📚</span>
            Tournament Archive
        </h1>
        <p class="text-text-muted dark:text-gray-400 mt-2">Browse through our completed tournaments and their champions</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('tournaments.index') }}" class="px-5 py-2.5 bg-gradient-to-r from-brand to-brand-light text-white font-semibold rounded-xl hover:shadow-lg transition-all duration-300">
            🏆 Current Tournaments
        </a>
    </div>
</div>

<!-- Statistics -->
<div class="grid grid-cols-2 gap-4 mb-8">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 text-center card-hover">
        <div class="text-4xl font-black text-brand dark:text-gold">{{ $stats['total_tournaments'] }}</div>
        <div class="text-sm text-text-muted dark:text-gray-400 font-medium mt-1">Completed Tournaments</div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 text-center card-hover">
        <div class="text-4xl font-black text-brand dark:text-gold">{{ $stats['total_players_participated'] }}</div>
        <div class="text-sm text-text-muted dark:text-gray-400 font-medium mt-1">Players Participated</div>
    </div>
</div>

<!-- Filter Section -->
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-8">
    <form action="{{ route('archive.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
        <div class="flex-1 relative">
            <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">🔍</span>
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Search tournaments..."
                   class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-brand focus:border-brand transition bg-white dark:bg-gray-700 dark:text-white dark:placeholder-gray-400">
        </div>
        @if($availableYears->count() > 0)
        <select name="year" class="px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-brand focus:border-brand transition bg-white dark:bg-gray-700 dark:text-white">
            <option value="">All Years</option>
            @foreach($availableYears as $year)
                <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
            @endforeach
        </select>
        @endif
        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-brand to-brand-light text-white font-semibold rounded-xl hover:shadow-lg transition-all duration-300">
            Filter
        </button>
    </form>
</div>

<!-- Archive Grid -->
@if($tournaments->count() > 0)
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    @foreach($tournaments as $tournament)
    <a href="{{ route('tournaments.show', $tournament) }}"
       class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden card-hover group">
        <div class="h-2 bg-gradient-to-r from-gray-400 to-gray-500"></div>
        <div class="p-6">
            <div class="flex items-start justify-between mb-3">
                <h3 class="text-xl font-bold text-text-primary dark:text-white group-hover:text-brand dark:group-hover:text-gold transition-colors">{{ $tournament->name }}</h3>
                <span class="px-3 py-1 bg-surface-alt dark:bg-gray-700 text-text-secondary dark:text-gray-300 text-xs font-bold rounded-full">FINISHED</span>
            </div>

            <div class="space-y-2 text-sm text-text-muted dark:text-gray-400">
                <p class="flex items-center gap-2">
                    <span>📍</span> {{ $tournament->location }}
                </p>
                <p class="flex items-center gap-2">
                    <span>📅</span> {{ $tournament->end_date ? $tournament->end_date->format('M d, Y') : $tournament->start_date->format('M d, Y') }}
                </p>
                <p class="flex items-center gap-2">
                    <span>👥</span> {{ $tournament->players_count }} players
                </p>
            </div>

            @if($tournament->champion)
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-3 p-3 bg-gradient-to-r from-yellow-50 to-amber-50 dark:from-yellow-900/30 dark:to-amber-900/30 rounded-xl">
                    <span class="text-2xl">🏆</span>
                    <div>
                        <div class="text-xs text-text-muted dark:text-gray-400 uppercase tracking-wide">Champion</div>
                        <div class="font-bold text-text-primary dark:text-white">{{ $tournament->champion->display_name }}</div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </a>
    @endforeach
</div>

<!-- Pagination -->
<div class="flex justify-center">
    {{ $tournaments->links() }}
</div>

@else
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-16 text-center">
    <div class="w-24 h-24 bg-surface-alt dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-6">
        <span class="text-5xl">📚</span>
    </div>
    <h2 class="text-2xl font-bold text-text-secondary dark:text-gray-200 mb-2">No Archived Tournaments</h2>
    <p class="text-text-muted dark:text-gray-400 max-w-md mx-auto">
        {{ request('search') || request('year') ? 'No tournaments match your search criteria.' : 'Completed tournaments will appear here.' }}
    </p>
    @if(request('search') || request('year'))
    <a href="{{ route('archive.index') }}" class="inline-flex items-center gap-2 mt-6 px-6 py-3 bg-surface-alt dark:bg-gray-700 text-text-secondary dark:text-gray-200 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition">
        Clear Filters
    </a>
    @endif
</div>
@endif
@endsection
