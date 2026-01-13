@extends('layouts.app')

@section('title', 'Head-to-Head Comparison')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
        <span class="w-12 h-12 bg-gradient-to-br from-pool-green to-pool-felt rounded-xl flex items-center justify-center text-white text-2xl">⚔️</span>
        Head-to-Head Comparison
    </h1>
    <p class="text-gray-600 mt-2">Compare two players' performance against each other</p>
</div>

<!-- Player Selection Form -->
<div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
    <form action="{{ route('head-to-head.compare') }}" method="GET" class="grid grid-cols-1 md:grid-cols-7 gap-4 items-end">
        <div class="md:col-span-3">
            <label for="player1_id" class="block text-sm font-medium text-gray-700 mb-2">Player 1</label>
            <select name="player1_id" id="player1_id" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-pool-green focus:border-transparent transition">
                <option value="">Select Player 1</option>
                @foreach($players as $player)
                    <option value="{{ $player->id }}" {{ isset($player1) && $player1->id === $player->id ? 'selected' : '' }}>
                        {{ $player->display_name }} ({{ $player->wins }}W - {{ $player->losses }}L)
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex items-center justify-center text-3xl text-gray-400 font-bold">
            VS
        </div>
        <div class="md:col-span-3">
            <label for="player2_id" class="block text-sm font-medium text-gray-700 mb-2">Player 2</label>
            <select name="player2_id" id="player2_id" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-pool-green focus:border-transparent transition">
                <option value="">Select Player 2</option>
                @foreach($players as $player)
                    <option value="{{ $player->id }}" {{ isset($player2) && $player2->id === $player->id ? 'selected' : '' }}>
                        {{ $player->display_name }} ({{ $player->wins }}W - {{ $player->losses }}L)
                    </option>
                @endforeach
            </select>
        </div>
    </form>
    <div class="mt-4 flex justify-center">
        <button type="submit" form="compare-form" onclick="this.form = document.querySelector('form'); this.form.submit();"
                class="px-8 py-3 bg-gradient-to-r from-pool-green to-pool-felt text-white font-bold rounded-xl hover:shadow-lg transition transform hover:scale-105">
            Compare Players
        </button>
    </div>
</div>

@if(isset($player1) && isset($player2) && isset($stats))
<!-- Comparison Results -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Player 1 Card -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="h-2 bg-gradient-to-r from-blue-500 to-blue-600"></div>
        <div class="p-6 text-center">
            <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-3xl font-bold mx-auto mb-4">
                {{ substr($player1->name, 0, 1) }}
            </div>
            <h3 class="text-xl font-bold text-gray-800">{{ $player1->display_name }}</h3>
            <p class="text-gray-500">{{ $player1->name }}</p>
            <div class="mt-4 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Overall Record</span>
                    <span class="font-bold">{{ $player1->wins }}W - {{ $player1->losses }}L</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Win Rate</span>
                    <span class="font-bold text-pool-green">{{ $player1->win_rate }}%</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Total Matches</span>
                    <span class="font-bold">{{ $player1->total_matches }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- VS Stats -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="h-2 bg-gradient-to-r from-pool-gold to-yellow-500"></div>
        <div class="p-6 text-center">
            <div class="text-6xl mb-4">⚔️</div>
            <h3 class="text-lg font-bold text-gray-800 mb-4">Head-to-Head Record</h3>
            
            @if($stats['total_matches'] > 0)
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div class="text-center">
                        <div class="text-3xl font-black text-blue-600">{{ $stats['player1_wins'] }}</div>
                        <div class="text-xs text-gray-500">{{ $player1->display_name }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xl font-bold text-gray-400">-</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-black text-red-600">{{ $stats['player2_wins'] }}</div>
                        <div class="text-xs text-gray-500">{{ $player2->display_name }}</div>
                    </div>
                </div>

                <!-- Win Bar -->
                <div class="relative h-4 bg-gray-200 rounded-full overflow-hidden mb-4">
                    @php
                        $player1Percent = $stats['total_matches'] > 0 ? ($stats['player1_wins'] / $stats['total_matches']) * 100 : 50;
                    @endphp
                    <div class="absolute left-0 top-0 h-full bg-blue-500 transition-all duration-500" style="width: {{ $player1Percent }}%"></div>
                    <div class="absolute right-0 top-0 h-full bg-red-500 transition-all duration-500" style="width: {{ 100 - $player1Percent }}%"></div>
                </div>

                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Total Matches</span>
                        <span class="font-bold">{{ $stats['total_matches'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Total Points</span>
                        <span class="font-bold">{{ $stats['player1_total_score'] }} - {{ $stats['player2_total_score'] }}</span>
                    </div>
                </div>
            @else
                <div class="text-gray-500 py-8">
                    <span class="text-4xl mb-4 block">🤝</span>
                    These players have never faced each other.
                </div>
            @endif
        </div>
    </div>

    <!-- Player 2 Card -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="h-2 bg-gradient-to-r from-red-500 to-red-600"></div>
        <div class="p-6 text-center">
            <div class="w-20 h-20 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center text-white text-3xl font-bold mx-auto mb-4">
                {{ substr($player2->name, 0, 1) }}
            </div>
            <h3 class="text-xl font-bold text-gray-800">{{ $player2->display_name }}</h3>
            <p class="text-gray-500">{{ $player2->name }}</p>
            <div class="mt-4 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Overall Record</span>
                    <span class="font-bold">{{ $player2->wins }}W - {{ $player2->losses }}L</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Win Rate</span>
                    <span class="font-bold text-pool-green">{{ $player2->win_rate }}%</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Total Matches</span>
                    <span class="font-bold">{{ $player2->total_matches }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

@if($matches->count() > 0)
<!-- Match History -->
<div class="bg-white rounded-2xl shadow-lg overflow-hidden">
    <div class="bg-gradient-to-r from-pool-green to-pool-felt p-4">
        <h3 class="text-lg font-bold text-white">Match History</h3>
    </div>
    <div class="divide-y divide-gray-100">
        @foreach($matches as $match)
        <div class="p-4 hover:bg-gray-50 transition">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="text-center">
                        <div class="font-bold {{ $match->winner_id === $player1->id ? 'text-green-600' : 'text-gray-600' }}">
                            {{ $match->player1_id === $player1->id ? $match->player1_score : $match->player2_score }}
                        </div>
                        <div class="text-xs {{ $match->winner_id === $player1->id ? 'text-green-500' : 'text-gray-400' }}">
                            {{ $player1->display_name }}
                        </div>
                    </div>
                    <div class="text-gray-400 font-bold">-</div>
                    <div class="text-center">
                        <div class="font-bold {{ $match->winner_id === $player2->id ? 'text-green-600' : 'text-gray-600' }}">
                            {{ $match->player1_id === $player2->id ? $match->player1_score : $match->player2_score }}
                        </div>
                        <div class="text-xs {{ $match->winner_id === $player2->id ? 'text-green-500' : 'text-gray-400' }}">
                            {{ $player2->display_name }}
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="font-medium text-gray-800">{{ $match->tournament->name }}</div>
                    <div class="text-sm text-gray-500">{{ $match->tournament->getRoundName($match->round) }}</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
@else
<!-- No Comparison Yet -->
<div class="bg-white rounded-2xl shadow-lg p-12 text-center">
    <div class="text-6xl mb-4">⚔️</div>
    <h3 class="text-xl font-bold text-gray-800 mb-2">Select Two Players to Compare</h3>
    <p class="text-gray-500">Choose two players from the dropdowns above to see their head-to-head statistics and match history.</p>
</div>
@endif

<script>
// Auto-submit form when both players are selected
document.querySelectorAll('select').forEach(select => {
    select.addEventListener('change', function() {
        const player1 = document.getElementById('player1_id').value;
        const player2 = document.getElementById('player2_id').value;
        if (player1 && player2 && player1 !== player2) {
            this.form.submit();
        }
    });
});
</script>
@endsection
