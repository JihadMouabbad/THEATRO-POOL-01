@extends('layouts.app')

@section('title', 'Players')

@section('content')
<!-- Header -->
<div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
            <span class="w-10 h-10 bg-gradient-to-br from-pool-green to-pool-felt rounded-lg flex items-center justify-center text-white">👥</span>
            Players
        </h1>
        <p class="text-gray-500 mt-1">{{ $players->total() }} players registered</p>
    </div>
    @auth
        @if(Auth::user()->isAdmin())
        <a href="{{ route('players.create') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-gradient-to-r from-pool-green to-pool-felt text-white font-semibold rounded-xl hover:shadow-lg transition-all duration-300 transform hover:scale-105">
            <span>➕</span> Add Player
        </a>
        @endif
    @endauth
</div>

<!-- Search and Filter -->
<div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
    <form action="{{ route('players.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
        <div class="flex-1 relative">
            <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">🔍</span>
            <input type="text" 
                   name="search" 
                   value="{{ request('search') }}"
                   placeholder="Search players by name, nickname, or email..."
                   class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-pool-green focus:border-pool-green transition">
        </div>
        <div class="flex flex-wrap gap-3">
            <select name="sort" class="px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-pool-green focus:border-pool-green transition bg-white">
                <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>📝 Name</option>
                <option value="wins" {{ request('sort') === 'wins' ? 'selected' : '' }}>🏆 Wins</option>
                <option value="total_matches" {{ request('sort') === 'total_matches' ? 'selected' : '' }}>🎱 Matches</option>
                <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>📅 Date Added</option>
            </select>
            <select name="direction" class="px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-pool-green focus:border-pool-green transition bg-white">
                <option value="asc" {{ request('direction') === 'asc' ? 'selected' : '' }}>↑ Ascending</option>
                <option value="desc" {{ request('direction') === 'desc' ? 'selected' : '' }}>↓ Descending</option>
            </select>
            <button type="submit" class="px-6 py-3 bg-gradient-to-r from-pool-green to-pool-felt text-white font-semibold rounded-xl hover:shadow-lg transition-all duration-300">
                Filter
            </button>
        </div>
    </form>
</div>

<!-- Players Grid/Table -->
@if($players->count() > 0)

<!-- Mobile View - Cards -->
<div class="md:hidden space-y-4 mb-6">
    @foreach($players as $index => $player)
    <a href="{{ route('players.show', $player) }}" class="block bg-white rounded-2xl shadow-lg p-5 card-hover">
        <div class="flex items-center gap-4">
            <div class="relative">
                <div class="w-14 h-14 bg-gradient-to-br from-pool-green to-pool-felt rounded-xl flex items-center justify-center text-white text-xl font-bold">
                    {{ substr($player->name, 0, 1) }}
                </div>
                @if($index === 0 && request('sort') === 'wins' && request('direction') === 'desc')
                    <span class="absolute -top-2 -right-2 text-lg">🥇</span>
                @endif
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-gray-800">{{ $player->name }}</h3>
                @if($player->nickname)
                <p class="text-sm text-gray-500">"{{ $player->nickname }}"</p>
                @endif
            </div>
            <div class="text-right">
                <div class="text-lg font-bold text-pool-green">{{ $player->win_rate }}%</div>
                <div class="text-xs text-gray-500">{{ $player->wins }}W - {{ $player->losses }}L</div>
            </div>
        </div>
    </a>
    @endforeach
</div>

<!-- Desktop View - Table -->
<div class="hidden md:block bg-white rounded-2xl shadow-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">#</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Player</th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Wins</th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Losses</th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Matches</th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Win Rate</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($players as $index => $player)
                <tr class="hover:bg-gray-50 transition-colors group">
                    <td class="px-6 py-4">
                        @if(request('sort') === 'wins' && request('direction') === 'desc')
                            @if($index === 0)
                                <span class="text-xl">🥇</span>
                            @elseif($index === 1)
                                <span class="text-xl">🥈</span>
                            @elseif($index === 2)
                                <span class="text-xl">🥉</span>
                            @else
                                <span class="text-gray-400 font-bold">{{ ($players->currentPage() - 1) * $players->perPage() + $index + 1 }}</span>
                            @endif
                        @else
                            <span class="text-gray-400 font-bold">{{ ($players->currentPage() - 1) * $players->perPage() + $index + 1 }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-pool-green to-pool-felt rounded-xl flex items-center justify-center text-white font-bold text-sm shadow">
                                {{ substr($player->name, 0, 1) }}
                            </div>
                            <div>
                                <a href="{{ route('players.show', $player) }}" class="font-bold text-gray-800 hover:text-pool-green transition">
                                    {{ $player->name }}
                                </a>
                                @if($player->nickname)
                                <p class="text-sm text-gray-500">"{{ $player->nickname }}"</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 bg-green-100 text-green-700 font-bold rounded-lg">{{ $player->wins }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 bg-red-100 text-red-700 font-bold rounded-lg">{{ $player->losses }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="font-bold text-gray-700">{{ $player->total_matches }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <div class="w-16 h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-pool-green to-pool-felt rounded-full" style="width: {{ $player->win_rate }}%"></div>
                            </div>
                            <span class="font-bold text-pool-green">{{ $player->win_rate }}%</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <a href="{{ route('players.show', $player) }}" class="px-3 py-1.5 bg-pool-green text-white text-sm font-medium rounded-lg hover:bg-pool-felt transition">
                                View
                            </a>
                            @auth
                                @if(Auth::user()->isAdmin())
                                <a href="{{ route('players.edit', $player) }}" class="px-3 py-1.5 bg-blue-500 text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition">
                                    Edit
                                </a>
                                @endif
                            @endauth
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
        {{ $players->links() }}
    </div>
</div>

<!-- Mobile Pagination -->
<div class="md:hidden mt-6">
    {{ $players->links() }}
</div>

@else
<div class="bg-white rounded-2xl shadow-lg p-16 text-center">
    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
        <span class="text-5xl">👥</span>
    </div>
    <h2 class="text-2xl font-bold text-gray-700 mb-2">No Players Found</h2>
    <p class="text-gray-500 max-w-md mx-auto">
        {{ request('search') ? 'Try adjusting your search terms.' : 'Add your first player to get started!' }}
    </p>
    @auth
        @if(Auth::user()->isAdmin())
        <a href="{{ route('players.create') }}" class="inline-flex items-center gap-2 mt-6 px-8 py-3 bg-gradient-to-r from-pool-green to-pool-felt text-white font-bold rounded-xl hover:shadow-lg transition-all duration-300 transform hover:scale-105">
            <span>➕</span> Add First Player
        </a>
        @endif
    @endauth
</div>
@endif
@endsection
