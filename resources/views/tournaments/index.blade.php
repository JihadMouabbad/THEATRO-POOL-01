@extends('layouts.app')

@section('title', 'Tournaments')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-pool-green">Tournaments</h1>
    @auth
        @if(Auth::user()->isAdmin())
        <a href="{{ route('tournaments.create') }}" class="px-4 py-2 bg-pool-green text-white rounded-lg hover:bg-pool-felt transition">
            + Create Tournament
        </a>
        @endif
    @endauth
</div>

<!-- Search and Filter -->
<div class="bg-white rounded-lg shadow-md p-4 mb-6">
    <form action="{{ route('tournaments.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <input type="text" 
                   name="search" 
                   value="{{ request('search') }}"
                   placeholder="Search tournaments by name or location..."
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pool-green focus:border-transparent">
        </div>
        <div class="flex gap-2">
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pool-green">
                <option value="">All Statuses</option>
                <option value="upcoming" {{ request('status') === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                <option value="ongoing" {{ request('status') === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                <option value="finished" {{ request('status') === 'finished' ? 'selected' : '' }}>Finished</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-pool-green text-white rounded-lg hover:bg-pool-felt transition">
                Filter
            </button>
        </div>
    </form>
</div>

<!-- Tournaments Grid -->
@if($tournaments->count() > 0)
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($tournaments as $tournament)
    <a href="{{ route('tournaments.show', $tournament) }}" class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
        <div class="h-2 
            {{ $tournament->status === 'upcoming' ? 'bg-blue-500' : '' }}
            {{ $tournament->status === 'ongoing' ? 'bg-green-500' : '' }}
            {{ $tournament->status === 'finished' ? 'bg-gray-400' : '' }}
        "></div>
        <div class="p-6">
            <div class="flex justify-between items-start mb-2">
                <h3 class="text-xl font-semibold text-pool-green">{{ $tournament->name }}</h3>
                <span class="px-2 py-1 text-xs rounded-full
                    {{ $tournament->status === 'upcoming' ? 'bg-blue-100 text-blue-800' : '' }}
                    {{ $tournament->status === 'ongoing' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $tournament->status === 'finished' ? 'bg-gray-100 text-gray-800' : '' }}
                ">
                    {{ ucfirst($tournament->status) }}
                </span>
            </div>
            <p class="text-gray-600 text-sm mb-4">
                📍 {{ $tournament->location }}
            </p>
            <div class="flex justify-between items-center text-sm text-gray-500">
                <span>📅 {{ $tournament->start_date->format('M d, Y') }}</span>
                <span>👥 {{ $tournament->players_count }}/{{ $tournament->max_players }}</span>
            </div>
            @if($tournament->status === 'upcoming')
            <div class="mt-4">
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-pool-green h-2 rounded-full" style="width: {{ ($tournament->players_count / $tournament->max_players) * 100 }}%"></div>
                </div>
                <p class="text-xs text-gray-500 mt-1">Registration: {{ $tournament->players_count }}/{{ $tournament->max_players }} players</p>
            </div>
            @endif
        </div>
    </a>
    @endforeach
</div>

<!-- Pagination -->
<div class="mt-6">
    {{ $tournaments->links() }}
</div>
@else
<div class="bg-white rounded-lg shadow-md p-12 text-center">
    <span class="text-6xl">🏆</span>
    <p class="text-gray-500 mt-4 text-lg">No tournaments found</p>
    @auth
        @if(Auth::user()->isAdmin())
        <a href="{{ route('tournaments.create') }}" class="inline-block mt-4 px-6 py-3 bg-pool-green text-white rounded-lg hover:bg-pool-felt transition">
            Create First Tournament
        </a>
        @endif
    @endauth
</div>
@endif
@endsection
