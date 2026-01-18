@extends('layouts.app')

@section('title', 'Match Result')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="mb-6">
        <a href="{{ route('tournaments.show', $match->tournament) }}" class="text-brand hover:underline">&larr; Back to Tournament</a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-8">
        <div class="text-center mb-6">
            <span class="text-4xl">🎱</span>
            <h1 class="text-2xl font-bold text-brand mt-2">Enter Match Result</h1>
            <p class="text-text-secondary">{{ $match->tournament->name }}</p>
            <p class="text-sm text-text-muted">{{ $match->tournament->getRoundName($match->round) }} - Match {{ $match->match_number }}</p>
        </div>

        <form action="{{ route('matches.update', $match) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Match Card -->
            <div class="border-2 border-brand rounded-lg p-6 mb-6">
                <!-- Player 1 -->
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-brand text-white rounded-full flex items-center justify-center font-bold">
                            {{ substr($match->player1->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-semibold">{{ $match->player1->display_name }}</p>
                            <p class="text-sm text-text-muted">{{ $match->player1->wins }}W - {{ $match->player1->losses }}L</p>
                        </div>
                    </div>
                    <input type="number" 
                           name="player1_score" 
                           id="player1_score"
                           value="{{ old('player1_score') }}"
                           min="0" 
                           max="100"
                           required
                           class="w-20 h-12 text-center text-2xl font-bold border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent @error('player1_score') border-danger @enderror">
                </div>

                <div class="text-center text-gray-400 font-bold text-xl mb-4">VS</div>

                <!-- Player 2 -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-brand text-white rounded-full flex items-center justify-center font-bold">
                            {{ substr($match->player2->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-semibold">{{ $match->player2->display_name }}</p>
                            <p class="text-sm text-text-muted">{{ $match->player2->wins }}W - {{ $match->player2->losses }}L</p>
                        </div>
                    </div>
                    <input type="number" 
                           name="player2_score" 
                           id="player2_score"
                           value="{{ old('player2_score') }}"
                           min="0" 
                           max="100"
                           required
                           class="w-20 h-12 text-center text-2xl font-bold border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent @error('player2_score') border-danger @enderror">
                </div>
            </div>

            @error('player1_score')
                <p class="mb-2 text-sm text-danger">{{ $message }}</p>
            @enderror
            @error('player2_score')
                <p class="mb-2 text-sm text-danger">{{ $message }}</p>
            @enderror

            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-yellow-700">
                    <strong>⚠️ Note:</strong> Scores cannot be tied. The winner will automatically advance to the next round.
                </p>
            </div>

            <div class="flex gap-4">
                <button type="submit" 
                        class="flex-1 py-3 bg-brand text-white font-semibold rounded-lg hover:bg-success transition">
                    Save Result
                </button>
                <a href="{{ route('tournaments.show', $match->tournament) }}" 
                   class="flex-1 py-3 text-center border-2 border-gray-300 text-text-secondary font-semibold rounded-lg hover:bg-surface transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
