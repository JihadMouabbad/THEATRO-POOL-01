@extends('layouts.app')

@section('title', 'Tournaments')

@section('content')
<!-- Header -->
<div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
            <span class="w-10 h-10 bg-gradient-to-br from-pool-green to-pool-felt rounded-lg flex items-center justify-center text-white">🏆</span>
            Tournaments
        </h1>
        <p class="text-gray-500 mt-1">Browse and manage pool tournaments</p>
    </div>
    @auth
        @if(Auth::user()->isAdmin())
        <a href="{{ route('tournaments.create') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-gradient-to-r from-pool-green to-pool-felt text-white font-semibold rounded-xl hover:shadow-lg transition-all duration-300 transform hover:scale-105">
            <span>➕</span> Create Tournament
        </a>
        @endif
    @endauth
</div>

<!-- Search and Filter -->
<div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
    <form action="{{ route('tournaments.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
        <div class="flex-1 relative">
            <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">🔍</span>
            <input type="text" 
                   name="search" 
                   value="{{ request('search') }}"
                   placeholder="Search tournaments by name or location..."
                   class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-pool-green focus:border-pool-green transition">
        </div>
        <div class="flex gap-3">
            <select name="status" class="px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-pool-green focus:border-pool-green transition bg-white">
                <option value="">All Statuses</option>
                <option value="upcoming" {{ request('status') === 'upcoming' ? 'selected' : '' }}>🔵 Upcoming</option>
                <option value="ongoing" {{ request('status') === 'ongoing' ? 'selected' : '' }}>🟢 Ongoing</option>
                <option value="finished" {{ request('status') === 'finished' ? 'selected' : '' }}>⚪ Finished</option>
            </select>
            <button type="submit" class="px-6 py-3 bg-gradient-to-r from-pool-green to-pool-felt text-white font-semibold rounded-xl hover:shadow-lg transition-all duration-300">
                Filter
            </button>
        </div>
    </form>
</div>

<!-- Tournaments Grid -->
@if($tournaments->count() > 0)
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($tournaments as $tournament)
    <a href="{{ route('tournaments.show', $tournament) }}" class="bg-white rounded-2xl shadow-lg overflow-hidden card-hover group">
        <div class="h-2 
            {{ $tournament->status === 'upcoming' ? 'bg-gradient-to-r from-blue-400 to-indigo-500' : '' }}
            {{ $tournament->status === 'ongoing' ? 'bg-gradient-to-r from-green-400 to-emerald-500' : '' }}
            {{ $tournament->status === 'finished' ? 'bg-gradient-to-r from-gray-300 to-gray-400' : '' }}
        "></div>
        <div class="p-6">
            <div class="flex justify-between items-start mb-3">
                <h3 class="text-xl font-bold text-gray-800 group-hover:text-pool-green transition-colors">{{ $tournament->name }}</h3>
                <span class="px-3 py-1 text-xs font-bold rounded-full
                    {{ $tournament->status === 'upcoming' ? 'bg-blue-100 text-blue-700' : '' }}
                    {{ $tournament->status === 'ongoing' ? 'bg-green-100 text-green-700 animate-pulse' : '' }}
                    {{ $tournament->status === 'finished' ? 'bg-gray-100 text-gray-600' : '' }}
                ">
                    {{ $tournament->status === 'ongoing' ? '🔴 LIVE' : strtoupper($tournament->status) }}
                </span>
            </div>
            <div class="space-y-2 text-sm text-gray-600 mb-4">
                <p class="flex items-center gap-2">
                    <span class="w-6 h-6 bg-gray-100 rounded-lg flex items-center justify-center text-xs">📍</span>
                    {{ $tournament->location }}
                </p>
                <p class="flex items-center gap-2">
                    <span class="w-6 h-6 bg-gray-100 rounded-lg flex items-center justify-center text-xs">📅</span>
                    {{ $tournament->start_date->format('M d, Y') }}
                </p>
            </div>
            <div class="flex justify-between items-center text-sm">
                <span class="text-gray-500">
                    <span class="font-bold text-pool-green">{{ $tournament->players_count }}</span>/{{ $tournament->max_players }} players
                </span>
                @if($tournament->status === 'ongoing')
                    <span class="text-green-600 font-medium">{{ $tournament->matches()->where('status', 'completed')->count() }} matches</span>
                @endif
            </div>
            @if($tournament->status === 'upcoming')
            <div class="mt-4">
                <div class="flex justify-between text-xs text-gray-500 mb-1">
                    <span>Registration</span>
                    <span>{{ round(($tournament->players_count / $tournament->max_players) * 100) }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                    <div class="bg-gradient-to-r from-pool-green to-pool-felt h-full rounded-full progress-bar" 
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
<div class="bg-white rounded-2xl shadow-lg p-16 text-center">
    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
        <span class="text-5xl">🏆</span>
    </div>
    <h2 class="text-2xl font-bold text-gray-700 mb-2">No Tournaments Found</h2>
    <p class="text-gray-500 max-w-md mx-auto">
        {{ request('search') ? 'Try adjusting your search terms or filters.' : 'Be the first to create an exciting tournament!' }}
    </p>
    @auth
        @if(Auth::user()->isAdmin())
        <a href="{{ route('tournaments.create') }}" class="inline-flex items-center gap-2 mt-6 px-8 py-3 bg-gradient-to-r from-pool-green to-pool-felt text-white font-bold rounded-xl hover:shadow-lg transition-all duration-300 transform hover:scale-105">
            <span>➕</span> Create First Tournament
        </a>
        @endif
    @endauth
</div>
@endif
@endsection
