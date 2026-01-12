@extends('layouts.app')

@section('title', 'Rankings & Leaderboard')

@section('content')
<!-- Page Header -->
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
        <span class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-amber-500 rounded-xl flex items-center justify-center text-white shadow-lg">👑</span>
        Rankings & Leaderboard
    </h1>
    <p class="text-gray-500 mt-2">The best players in Theatro Pool based on performance</p>
</div>

<!-- Statistics Overview -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-xl shadow-lg p-5 text-center card-hover">
        <div class="text-3xl font-black text-pool-green">{{ $stats['total_players'] }}</div>
        <div class="text-sm text-gray-500 font-medium">Total Players</div>
    </div>
    <div class="bg-white rounded-xl shadow-lg p-5 text-center card-hover">
        <div class="text-3xl font-black text-pool-green">{{ $stats['players_with_matches'] }}</div>
        <div class="text-sm text-gray-500 font-medium">Ranked Players</div>
    </div>
    <div class="bg-white rounded-xl shadow-lg p-5 text-center card-hover">
        <div class="text-3xl font-black text-pool-green">{{ $stats['total_matches'] }}</div>
        <div class="text-sm text-gray-500 font-medium">Matches Played</div>
    </div>
    <div class="bg-white rounded-xl shadow-lg p-5 text-center card-hover">
        <div class="text-3xl font-black text-pool-green">{{ $stats['total_tournaments'] }}</div>
        <div class="text-sm text-gray-500 font-medium">Tournaments Completed</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Main Rankings Table -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-pool-green to-pool-felt px-6 py-4 flex justify-between items-center">
                <h2 class="text-lg font-bold text-white flex items-center gap-2">
                    🏆 Player Rankings
                </h2>
                <div class="flex gap-2">
                    <a href="{{ route('rankings.index', ['sort' => 'win_rate']) }}" 
                       class="px-3 py-1 text-sm rounded-lg {{ $sortBy === 'win_rate' ? 'bg-white text-pool-green font-bold' : 'bg-white/20 text-white hover:bg-white/30' }} transition">
                        Win Rate
                    </a>
                    <a href="{{ route('rankings.index', ['sort' => 'wins']) }}" 
                       class="px-3 py-1 text-sm rounded-lg {{ $sortBy === 'wins' ? 'bg-white text-pool-green font-bold' : 'bg-white/20 text-white hover:bg-white/30' }} transition">
                        Wins
                    </a>
                    <a href="{{ route('rankings.index', ['sort' => 'total_matches']) }}" 
                       class="px-3 py-1 text-sm rounded-lg {{ $sortBy === 'total_matches' ? 'bg-white text-pool-green font-bold' : 'bg-white/20 text-white hover:bg-white/30' }} transition">
                        Matches
                    </a>
                </div>
            </div>
            <div class="p-6">
                @if($rankedPlayers->count() > 0)
                <div class="space-y-3">
                    @foreach($rankedPlayers as $index => $player)
                    <a href="{{ route('players.show', $player) }}" 
                       class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-pool-green/5 hover:shadow-md transition-all duration-300 group">
                        <div class="flex items-center gap-4">
                            <div class="w-12 text-center">
                                @if($index === 0)
                                    <span class="text-3xl">🥇</span>
                                @elseif($index === 1)
                                    <span class="text-3xl">🥈</span>
                                @elseif($index === 2)
                                    <span class="text-3xl">🥉</span>
                                @else
                                    <span class="text-2xl font-black text-gray-400">#{{ $index + 1 }}</span>
                                @endif
                            </div>
                            <div class="w-14 h-14 bg-gradient-to-br from-pool-green to-pool-felt rounded-xl flex items-center justify-center text-white text-xl font-bold shadow-md group-hover:scale-110 transition-transform">
                                {{ substr($player->name, 0, 1) }}
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800 group-hover:text-pool-green transition">{{ $player->display_name }}</h3>
                                <p class="text-sm text-gray-500">{{ $player->total_matches }} matches played</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-black text-pool-green">{{ $player->win_rate }}%</div>
                            <div class="text-sm text-gray-500">
                                <span class="text-green-600 font-bold">{{ $player->wins }}W</span>
                                <span class="mx-1">-</span>
                                <span class="text-red-500 font-bold">{{ $player->losses }}L</span>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
                @else
                <div class="text-center py-12">
                    <span class="text-5xl">🎱</span>
                    <p class="text-gray-500 mt-3">No ranked players yet</p>
                    <p class="text-sm text-gray-400">Players need to complete matches to appear here</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Recent Champions -->
        @if($recentChampions->count() > 0)
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-yellow-400 to-amber-500 px-6 py-4">
                <h2 class="text-lg font-bold text-white flex items-center gap-2">
                    🏆 Recent Champions
                </h2>
            </div>
            <div class="p-4">
                <div class="space-y-3">
                    @foreach($recentChampions as $tournament)
                    <div class="flex items-center gap-3 p-3 bg-gradient-to-r from-yellow-50 to-amber-50 rounded-xl">
                        <span class="text-2xl">🏆</span>
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('players.show', $tournament->champion) }}" class="font-bold text-gray-800 hover:text-pool-green truncate block">
                                {{ $tournament->champion->display_name }}
                            </a>
                            <a href="{{ route('tournaments.show', $tournament) }}" class="text-xs text-gray-500 hover:text-pool-green truncate block">
                                {{ $tournament->name }}
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Most Active Players -->
        @if($mostActive->count() > 0)
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-4">
                <h2 class="text-lg font-bold text-white flex items-center gap-2">
                    ⚡ Most Active
                </h2>
            </div>
            <div class="p-4">
                <div class="space-y-3">
                    @foreach($mostActive as $index => $player)
                    <a href="{{ route('players.show', $player) }}" class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl hover:bg-blue-50 transition group">
                        <span class="w-8 text-center font-bold text-gray-400">{{ $index + 1 }}</span>
                        <div class="w-10 h-10 bg-gradient-to-br from-pool-green to-pool-felt rounded-lg flex items-center justify-center text-white font-bold">
                            {{ substr($player->name, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="font-medium text-gray-800 group-hover:text-pool-green truncate block">{{ $player->display_name }}</span>
                        </div>
                        <span class="text-sm font-bold text-blue-600">{{ $player->total_matches }} games</span>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Quick Links -->
        <div class="bg-gradient-to-br from-pool-green to-pool-felt rounded-2xl p-6 text-white">
            <h3 class="font-bold mb-4 flex items-center gap-2">
                🔗 Quick Links
            </h3>
            <div class="space-y-2">
                <a href="{{ route('players.index') }}" class="block py-2 px-3 bg-white/10 rounded-lg hover:bg-white/20 transition">
                    👥 View All Players
                </a>
                <a href="{{ route('tournaments.index') }}" class="block py-2 px-3 bg-white/10 rounded-lg hover:bg-white/20 transition">
                    🏆 Browse Tournaments
                </a>
                <a href="{{ route('archive.index') }}" class="block py-2 px-3 bg-white/10 rounded-lg hover:bg-white/20 transition">
                    📚 Tournament Archive
                </a>
                <a href="{{ route('statistics.index') }}" class="block py-2 px-3 bg-white/10 rounded-lg hover:bg-white/20 transition">
                    📊 View Statistics
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
