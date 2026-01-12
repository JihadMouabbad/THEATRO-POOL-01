@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
<div class="text-center py-12">
    <!-- Hero Section -->
    <div class="mb-12">
        <span class="text-8xl mb-4 block">🎱</span>
        <h1 class="text-4xl font-bold text-pool-green mb-4">Welcome to Theatro Pool</h1>
        <p class="text-xl text-gray-600 max-w-2xl mx-auto">
            The ultimate 8-Ball Pool tournament management system for billiard halls. 
            Create tournaments, manage players, and track matches with ease.
        </p>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-3xl font-bold text-pool-green">{{ \App\Models\Tournament::count() }}</div>
            <div class="text-gray-600">Tournaments</div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-3xl font-bold text-pool-green">{{ \App\Models\Player::count() }}</div>
            <div class="text-gray-600">Players</div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-3xl font-bold text-pool-green">{{ \App\Models\PoolMatch::where('status', 'completed')->count() }}</div>
            <div class="text-gray-600">Matches Played</div>
        </div>
    </div>

    <!-- Call to Action -->
    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="{{ route('tournaments.index') }}" 
           class="px-8 py-3 bg-pool-green text-white font-semibold rounded-lg hover:bg-pool-felt transition">
            View Tournaments
        </a>
        <a href="{{ route('players.index') }}" 
           class="px-8 py-3 bg-white text-pool-green font-semibold rounded-lg border-2 border-pool-green hover:bg-gray-50 transition">
            Browse Players
        </a>
    </div>

    <!-- Features -->
    <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-4xl mb-4">🏆</div>
            <h3 class="text-xl font-semibold mb-2">Tournament Management</h3>
            <p class="text-gray-600">
                Create and manage single-elimination tournaments for 8, 16, or 32 players.
            </p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-4xl mb-4">📊</div>
            <h3 class="text-xl font-semibold mb-2">Player Statistics</h3>
            <p class="text-gray-600">
                Track wins, losses, and overall performance for every player.
            </p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-4xl mb-4">🎯</div>
            <h3 class="text-xl font-semibold mb-2">Live Brackets</h3>
            <p class="text-gray-600">
                Visual bracket display with real-time updates as matches are completed.
            </p>
        </div>
    </div>

    <!-- Active Tournaments Preview -->
    @php
        $activeTournaments = \App\Models\Tournament::where('status', 'ongoing')
            ->orderBy('start_date')
            ->take(3)
            ->get();
    @endphp

    @if($activeTournaments->count() > 0)
    <div class="mt-16">
        <h2 class="text-2xl font-bold text-pool-green mb-6">Active Tournaments</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($activeTournaments as $tournament)
            <a href="{{ route('tournaments.show', $tournament) }}" 
               class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition text-left">
                <h3 class="text-lg font-semibold text-pool-green">{{ $tournament->name }}</h3>
                <p class="text-gray-600 text-sm mt-1">{{ $tournament->location }}</p>
                <div class="mt-4 flex justify-between items-center">
                    <span class="text-sm text-gray-500">{{ $tournament->players->count() }} players</span>
                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Ongoing</span>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
