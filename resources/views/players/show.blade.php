@extends('layouts.app')

@section('title', $player->display_name)

@section('content')
<div class="mb-6">
    <a href="{{ route('players.index') }}" class="text-pool-green hover:underline">&larr; Back to Players</a>
</div>

<!-- Player Profile Header -->
<div class="bg-white rounded-lg shadow-md p-8 mb-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div class="flex items-center gap-4">
            <div class="w-20 h-20 bg-pool-green rounded-full flex items-center justify-center text-white text-3xl font-bold">
                {{ substr($player->name, 0, 1) }}
            </div>
            <div>
                <h1 class="text-3xl font-bold text-pool-green">{{ $player->name }}</h1>
                @if($player->nickname)
                    <p class="text-xl text-gray-500">"{{ $player->nickname }}"</p>
                @endif
                @if($player->email)
                    <p class="text-gray-500">{{ $player->email }}</p>
                @endif
            </div>
        </div>
        @auth
            @if(Auth::user()->isAdmin())
            <div class="mt-4 md:mt-0">
                <a href="{{ route('players.edit', $player) }}" class="px-4 py-2 bg-pool-green text-white rounded-lg hover:bg-pool-felt transition">
                    Edit Player
                </a>
            </div>
            @endif
        @endauth
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8">
        <div class="bg-gray-50 rounded-lg p-4 text-center">
            <div class="text-3xl font-bold text-green-600">{{ $player->wins }}</div>
            <div class="text-sm text-gray-500">Wins</div>
        </div>
        <div class="bg-gray-50 rounded-lg p-4 text-center">
            <div class="text-3xl font-bold text-red-600">{{ $player->losses }}</div>
            <div class="text-sm text-gray-500">Losses</div>
        </div>
        <div class="bg-gray-50 rounded-lg p-4 text-center">
            <div class="text-3xl font-bold text-pool-green">{{ $player->total_matches }}</div>
            <div class="text-sm text-gray-500">Total Matches</div>
        </div>
        <div class="bg-gray-50 rounded-lg p-4 text-center">
            <div class="text-3xl font-bold text-pool-green">{{ $player->win_rate }}%</div>
            <div class="text-sm text-gray-500">Win Rate</div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Tournament History -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-pool-green mb-4">Tournament History</h2>
        @if($player->tournaments->count() > 0)
            <div class="space-y-3">
                @foreach($player->tournaments as $tournament)
                <a href="{{ route('tournaments.show', $tournament) }}" class="block p-4 border border-gray-200 rounded-lg hover:border-pool-green transition">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-semibold">{{ $tournament->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $tournament->start_date->format('M d, Y') }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full
                            {{ $tournament->status === 'finished' ? 'bg-gray-100 text-gray-800' : '' }}
                            {{ $tournament->status === 'ongoing' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $tournament->status === 'upcoming' ? 'bg-blue-100 text-blue-800' : '' }}
                        ">
                            {{ ucfirst($tournament->status) }}
                        </span>
                    </div>
                </a>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-center py-4">No tournament history yet</p>
        @endif
    </div>

    <!-- Recent Matches -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-pool-green mb-4">Recent Matches</h2>
        @if($recentMatches->count() > 0)
            <div class="space-y-3">
                @foreach($recentMatches as $match)
                <div class="p-3 border border-gray-200 rounded-lg">
                    @php
                        $isPlayer1 = $match->player1_id === $player->id;
                        $opponent = $isPlayer1 ? $match->player2 : $match->player1;
                        $playerScore = $isPlayer1 ? $match->player1_score : $match->player2_score;
                        $opponentScore = $isPlayer1 ? $match->player2_score : $match->player1_score;
                        $won = $match->winner_id === $player->id;
                    @endphp
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="{{ $won ? 'text-green-600 font-bold' : 'text-red-600' }}">
                                    {{ $won ? 'Won' : 'Lost' }}
                                </span>
                                <span class="text-gray-400">vs</span>
                                <span class="font-medium">{{ $opponent?->display_name ?? 'Unknown' }}</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $match->tournament->name }} • {{ $match->tournament->getRoundName($match->round) }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="font-mono font-bold {{ $won ? 'text-green-600' : 'text-red-600' }}">
                                {{ $playerScore }} - {{ $opponentScore }}
                            </span>
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

@if($player->notes)
<div class="mt-8 bg-white rounded-lg shadow-md p-6">
    <h2 class="text-xl font-semibold text-pool-green mb-4">Notes</h2>
    <p class="text-gray-600 whitespace-pre-wrap">{{ $player->notes }}</p>
</div>
@endif
@endsection
