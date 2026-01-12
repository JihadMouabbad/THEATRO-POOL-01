@extends('layouts.app')

@section('title', $player->display_name)

@section('content')
<div class="mb-6">
    <a href="{{ route('players.index') }}" class="inline-flex items-center gap-2 text-pool-green hover:text-pool-felt transition font-medium">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to Players
    </a>
</div>

<!-- Player Profile Header -->
<div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
    <div class="h-24 bg-gradient-to-r from-pool-green via-pool-felt to-pool-light relative">
        <div class="absolute inset-0 opacity-20">
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.4\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        </div>
    </div>
    <div class="px-6 md:px-8 pb-8 -mt-16 relative">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between">
            <div class="flex items-end gap-4">
                <div class="w-28 h-28 bg-gradient-to-br from-pool-green to-pool-felt rounded-2xl flex items-center justify-center text-white text-5xl font-bold shadow-xl border-4 border-white">
                    {{ substr($player->name, 0, 1) }}
                </div>
                <div class="mb-2">
                    <h1 class="text-3xl font-bold text-gray-800">{{ $player->name }}</h1>
                    @if($player->nickname)
                        <p class="text-xl text-pool-green font-medium">"{{ $player->nickname }}"</p>
                    @endif
                    @if($player->email)
                        <p class="text-gray-500 text-sm mt-1 flex items-center gap-1">
                            <span>✉️</span> {{ $player->email }}
                        </p>
                    @endif
                </div>
            </div>
            @auth
                @if(Auth::user()->isAdmin())
                <div class="mt-4 md:mt-0">
                    <a href="{{ route('players.edit', $player) }}" class="px-5 py-2.5 bg-gradient-to-r from-pool-green to-pool-felt text-white font-medium rounded-xl hover:shadow-lg transition-all duration-300 inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Player
                    </a>
                </div>
                @endif
            @endauth
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
    <div class="bg-white rounded-2xl shadow-lg p-6 card-hover group">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">Wins</p>
                <p class="text-4xl font-black text-green-600 mt-1">{{ $player->wins }}</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-green-400 to-emerald-600 rounded-xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform shadow-lg">
                🏆
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-lg p-6 card-hover group">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">Losses</p>
                <p class="text-4xl font-black text-red-600 mt-1">{{ $player->losses }}</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-red-400 to-rose-600 rounded-xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform shadow-lg">
                ❌
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-lg p-6 card-hover group">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">Total Matches</p>
                <p class="text-4xl font-black text-pool-green mt-1">{{ $player->total_matches }}</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-pool-green to-pool-felt rounded-xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform shadow-lg">
                🎱
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-lg p-6 card-hover group relative overflow-hidden">
        <div class="flex items-center justify-between relative z-10">
            <div>
                <p class="text-sm text-gray-500 font-medium">Win Rate</p>
                <p class="text-4xl font-black text-pool-green mt-1">{{ $player->win_rate }}%</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform shadow-lg">
                📊
            </div>
        </div>
        <!-- Progress circle background -->
        <div class="absolute bottom-0 left-0 right-0 h-2 bg-gray-100">
            <div class="h-full bg-gradient-to-r from-pool-green to-pool-felt transition-all duration-1000" style="width: {{ $player->win_rate }}%"></div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Tournament History -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-pool-green to-pool-felt px-6 py-4">
            <h2 class="text-lg font-bold text-white flex items-center gap-2">
                🏆 Tournament History
            </h2>
        </div>
        <div class="p-6">
            @if($player->tournaments->count() > 0)
                <div class="space-y-3">
                    @foreach($player->tournaments->sortByDesc('start_date') as $tournament)
                    <a href="{{ route('tournaments.show', $tournament) }}" class="block p-4 bg-gray-50 rounded-xl hover:bg-pool-green/5 hover:shadow-md transition-all duration-300 group">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-bold text-gray-800 group-hover:text-pool-green transition-colors">{{ $tournament->name }}</h3>
                                <p class="text-sm text-gray-500 mt-1 flex items-center gap-1">
                                    <span>📅</span> {{ $tournament->start_date->format('M d, Y') }}
                                </p>
                            </div>
                            <span class="px-3 py-1 text-xs font-bold rounded-full
                                {{ $tournament->status === 'finished' ? 'bg-gray-100 text-gray-700' : '' }}
                                {{ $tournament->status === 'ongoing' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $tournament->status === 'upcoming' ? 'bg-blue-100 text-blue-700' : '' }}
                            ">
                                {{ strtoupper($tournament->status) }}
                            </span>
                        </div>
                    </a>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <span class="text-5xl">🎱</span>
                    <p class="text-gray-500 mt-3">No tournament history yet</p>
                    <p class="text-sm text-gray-400">Register for a tournament to start competing!</p>
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
                    @php
                        $isPlayer1 = $match->player1_id === $player->id;
                        $opponent = $isPlayer1 ? $match->player2 : $match->player1;
                        $playerScore = $isPlayer1 ? $match->player1_score : $match->player2_score;
                        $opponentScore = $isPlayer1 ? $match->player2_score : $match->player1_score;
                        $won = $match->winner_id === $player->id;
                    @endphp
                    <div class="p-4 rounded-xl transition-colors {{ $won ? 'bg-green-50 border border-green-100' : 'bg-red-50 border border-red-100' }}">
                        <div class="flex justify-between items-center">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xl">{{ $won ? '🏆' : '❌' }}</span>
                                    <span class="font-bold {{ $won ? 'text-green-700' : 'text-red-700' }}">
                                        {{ $won ? 'Victory' : 'Defeat' }}
                                    </span>
                                    <span class="text-gray-400">vs</span>
                                    <a href="{{ route('players.show', $opponent) }}" class="font-medium text-gray-700 hover:text-pool-green transition">
                                        {{ $opponent?->display_name ?? 'Unknown' }}
                                    </a>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $match->tournament->name }} • {{ $match->tournament->getRoundName($match->round) }}
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="font-mono font-bold text-2xl {{ $won ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $playerScore }}
                                </span>
                                <span class="text-gray-400 mx-1">-</span>
                                <span class="font-mono font-bold text-2xl text-gray-400">
                                    {{ $opponentScore }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <span class="text-5xl">🎱</span>
                    <p class="text-gray-500 mt-3">No matches played yet</p>
                    <p class="text-sm text-gray-400">Join a tournament to start playing!</p>
                </div>
            @endif
        </div>
    </div>
</div>

@if($player->notes)
<div class="bg-white rounded-2xl shadow-lg overflow-hidden">
    <div class="bg-gradient-to-r from-gray-600 to-gray-700 px-6 py-4">
        <h2 class="text-lg font-bold text-white flex items-center gap-2">
            📝 Notes
        </h2>
    </div>
    <div class="p-6">
        <p class="text-gray-600 whitespace-pre-wrap leading-relaxed">{{ $player->notes }}</p>
    </div>
</div>
@endif
@endsection
