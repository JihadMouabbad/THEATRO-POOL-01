@extends('layouts.app')

@section('title', 'Match Details')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="mb-6">
        <a href="{{ route('tournaments.show', $match->tournament) }}" class="text-pool-green hover:underline">&larr; Back to Tournament</a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-8">
        <div class="text-center mb-6">
            <span class="text-4xl">🎱</span>
            <h1 class="text-2xl font-bold text-pool-green mt-2">Match Details</h1>
            <p class="text-gray-600">{{ $match->tournament->name }}</p>
            <p class="text-sm text-gray-500">{{ $match->tournament->getRoundName($match->round) }} - Match {{ $match->match_number }}</p>
        </div>

        <!-- Match Card -->
        <div class="border-2 {{ $match->isCompleted() ? 'border-gray-300' : 'border-pool-green' }} rounded-lg overflow-hidden mb-6">
            <!-- Player 1 -->
            <div class="flex items-center justify-between p-4 {{ $match->winner_id === $match->player1_id ? 'bg-green-50' : 'bg-white' }}">
                <div class="flex items-center gap-3">
                    @if($match->winner_id === $match->player1_id)
                    <span class="text-2xl">🏆</span>
                    @endif
                    <div class="w-12 h-12 bg-pool-green text-white rounded-full flex items-center justify-center font-bold">
                        {{ $match->player1 ? substr($match->player1->name, 0, 1) : '?' }}
                    </div>
                    <div>
                        @if($match->player1)
                        <a href="{{ route('players.show', $match->player1) }}" class="font-semibold hover:text-pool-green {{ $match->winner_id === $match->player1_id ? 'text-green-700' : '' }}">
                            {{ $match->player1->display_name }}
                        </a>
                        @else
                        <span class="font-semibold text-gray-400">TBD</span>
                        @endif
                    </div>
                </div>
                <span class="text-3xl font-bold {{ $match->winner_id === $match->player1_id ? 'text-green-700' : 'text-gray-700' }}">
                    {{ $match->player1_score ?? '-' }}
                </span>
            </div>

            <div class="text-center py-2 bg-gray-100 text-gray-500 font-bold">VS</div>

            <!-- Player 2 -->
            <div class="flex items-center justify-between p-4 {{ $match->winner_id === $match->player2_id ? 'bg-green-50' : 'bg-white' }}">
                <div class="flex items-center gap-3">
                    @if($match->winner_id === $match->player2_id)
                    <span class="text-2xl">🏆</span>
                    @endif
                    <div class="w-12 h-12 bg-pool-green text-white rounded-full flex items-center justify-center font-bold">
                        {{ $match->player2 ? substr($match->player2->name, 0, 1) : '?' }}
                    </div>
                    <div>
                        @if($match->player2)
                        <a href="{{ route('players.show', $match->player2) }}" class="font-semibold hover:text-pool-green {{ $match->winner_id === $match->player2_id ? 'text-green-700' : '' }}">
                            {{ $match->player2->display_name }}
                        </a>
                        @else
                        <span class="font-semibold text-gray-400">TBD</span>
                        @endif
                    </div>
                </div>
                <span class="text-3xl font-bold {{ $match->winner_id === $match->player2_id ? 'text-green-700' : 'text-gray-700' }}">
                    {{ $match->player2_score ?? '-' }}
                </span>
            </div>
        </div>

        <!-- Match Status -->
        <div class="text-center">
            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium
                {{ $match->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                {{ $match->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : '' }}
                {{ $match->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
            ">
                @if($match->isCompleted())
                    ✅ Completed
                @elseif($match->hasBothPlayers())
                    ⏳ Waiting for Result
                @else
                    🔜 Awaiting Players
                @endif
            </span>
        </div>

        @if($match->isCompleted() && $match->completed_at)
        <p class="text-center text-sm text-gray-500 mt-4">
            Completed on {{ $match->completed_at->format('M d, Y g:i A') }}
        </p>
        @endif

        @if($match->nextMatch)
        <div class="mt-6 p-4 bg-blue-50 rounded-lg text-center">
            <p class="text-sm text-blue-700">
                Winner advances to: {{ $match->tournament->getRoundName($match->nextMatch->round) }}
            </p>
        </div>
        @endif
    </div>
</div>
@endsection
