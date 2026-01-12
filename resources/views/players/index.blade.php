@extends('layouts.app')

@section('title', 'Players')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-pool-green">Players</h1>
    @auth
        @if(Auth::user()->isAdmin())
        <a href="{{ route('players.create') }}" class="px-4 py-2 bg-pool-green text-white rounded-lg hover:bg-pool-felt transition">
            + Add Player
        </a>
        @endif
    @endauth
</div>

<!-- Search and Filter -->
<div class="bg-white rounded-lg shadow-md p-4 mb-6">
    <form action="{{ route('players.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <input type="text" 
                   name="search" 
                   value="{{ request('search') }}"
                   placeholder="Search players by name, nickname, or email..."
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pool-green focus:border-transparent">
        </div>
        <div class="flex gap-2">
            <select name="sort" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pool-green">
                <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Sort by Name</option>
                <option value="wins" {{ request('sort') === 'wins' ? 'selected' : '' }}>Sort by Wins</option>
                <option value="total_matches" {{ request('sort') === 'total_matches' ? 'selected' : '' }}>Sort by Matches</option>
                <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>Sort by Date Added</option>
            </select>
            <select name="direction" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pool-green">
                <option value="asc" {{ request('direction') === 'asc' ? 'selected' : '' }}>Ascending</option>
                <option value="desc" {{ request('direction') === 'desc' ? 'selected' : '' }}>Descending</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-pool-green text-white rounded-lg hover:bg-pool-felt transition">
                Filter
            </button>
        </div>
    </form>
</div>

<!-- Players Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    @if($players->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Player</th>
                    <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Wins</th>
                    <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Losses</th>
                    <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Total Matches</th>
                    <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Win Rate</th>
                    <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($players as $player)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div>
                            <a href="{{ route('players.show', $player) }}" class="font-semibold text-pool-green hover:underline">
                                {{ $player->name }}
                            </a>
                            @if($player->nickname)
                            <span class="text-gray-500 text-sm">({{ $player->nickname }})</span>
                            @endif
                        </div>
                        @if($player->email)
                        <div class="text-sm text-gray-500">{{ $player->email }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-green-600 font-medium">{{ $player->wins }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-red-600 font-medium">{{ $player->losses }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="font-medium">{{ $player->total_matches }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="font-medium">{{ $player->win_rate }}%</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('players.show', $player) }}" class="text-pool-green hover:underline text-sm">
                                View
                            </a>
                            @auth
                                @if(Auth::user()->isAdmin())
                                <a href="{{ route('players.edit', $player) }}" class="text-blue-600 hover:underline text-sm">
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
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $players->links() }}
    </div>
    @else
    <div class="text-center py-12">
        <span class="text-4xl">👥</span>
        <p class="text-gray-500 mt-2">No players found</p>
        @auth
            @if(Auth::user()->isAdmin())
            <a href="{{ route('players.create') }}" class="inline-block mt-4 px-4 py-2 bg-pool-green text-white rounded-lg hover:bg-pool-felt transition">
                Add First Player
            </a>
            @endif
        @endauth
    </div>
    @endif
</div>
@endsection
