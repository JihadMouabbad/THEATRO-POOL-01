@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Profile Header -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
        <div class="h-32 bg-gradient-to-r from-pool-green via-pool-felt to-pool-light"></div>
        <div class="relative px-6 pb-6">
            <div class="flex flex-col md:flex-row md:items-end gap-4 -mt-12">
                <div class="w-24 h-24 bg-white rounded-2xl shadow-lg flex items-center justify-center text-4xl font-bold text-pool-green border-4 border-white">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-800">{{ $user->name }}</h1>
                    <p class="text-gray-500">{{ $user->email }}</p>
                    <div class="flex items-center gap-2 mt-1">
                        @if($user->isAdmin())
                            <span class="px-3 py-1 bg-pool-gold text-black text-xs font-bold rounded-full">Admin</span>
                        @else
                            <span class="px-3 py-1 bg-pool-green text-white text-xs font-bold rounded-full">Player</span>
                        @endif
                        @if($player)
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-bold rounded-full">Player Profile Active</span>
                        @endif
                    </div>
                </div>
                <a href="{{ route('profile.edit') }}" class="px-6 py-2 bg-pool-green text-white font-semibold rounded-xl hover:bg-pool-felt transition">
                    ✏️ Edit Profile
                </a>
            </div>
        </div>
    </div>

    @if($player)
        <!-- Player Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-lg p-6 text-center card-hover">
                <div class="text-3xl font-black text-green-600">{{ $player->wins }}</div>
                <div class="text-sm text-gray-500 font-medium">Wins</div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 text-center card-hover">
                <div class="text-3xl font-black text-red-600">{{ $player->losses }}</div>
                <div class="text-sm text-gray-500 font-medium">Losses</div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 text-center card-hover">
                <div class="text-3xl font-black text-pool-green">{{ $player->total_matches }}</div>
                <div class="text-sm text-gray-500 font-medium">Total Matches</div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 text-center card-hover">
                <div class="text-3xl font-black text-blue-600">{{ $player->win_rate }}%</div>
                <div class="text-sm text-gray-500 font-medium">Win Rate</div>
            </div>
        </div>

        <!-- Player Details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <span>👤</span> Player Details
                </h2>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Name</span>
                        <span class="font-medium">{{ $player->name }}</span>
                    </div>
                    @if($player->nickname)
                    <div class="flex justify-between">
                        <span class="text-gray-500">Nickname</span>
                        <span class="font-medium">{{ $player->nickname }}</span>
                    </div>
                    @endif
                    @if($player->phone)
                    <div class="flex justify-between">
                        <span class="text-gray-500">Phone</span>
                        <span class="font-medium">{{ $player->phone }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-500">Member Since</span>
                        <span class="font-medium">{{ $player->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <span>🏆</span> Active Tournaments
                </h2>
                @if($upcomingTournaments->count() > 0)
                    <div class="space-y-3">
                        @foreach($upcomingTournaments as $tournament)
                        <a href="{{ route('tournaments.show', $tournament) }}" class="block p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div class="font-medium text-gray-800">{{ $tournament->name }}</div>
                            <div class="text-sm text-gray-500">{{ $tournament->start_date->format('M d, Y') }} • {{ ucfirst($tournament->status) }}</div>
                        </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No active tournaments</p>
                    <a href="{{ route('tournaments.index') }}" class="block text-center text-pool-green hover:underline">
                        Browse tournaments →
                    </a>
                @endif
            </div>
        </div>

        <!-- Recent Matches -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span>🎱</span> Recent Matches
            </h2>
            @if($recentMatches->count() > 0)
                <div class="space-y-3">
                    @foreach($recentMatches as $match)
                    <div class="p-4 bg-gray-50 rounded-lg flex items-center justify-between">
                        <div>
                            <div class="font-medium">
                                @if($match->player1_id === $player->id)
                                    vs {{ $match->player2->display_name ?? 'TBD' }}
                                @else
                                    vs {{ $match->player1->display_name ?? 'TBD' }}
                                @endif
                            </div>
                            <div class="text-sm text-gray-500">{{ $match->tournament->name }}</div>
                        </div>
                        <div class="text-right">
                            @if($match->winner_id === $player->id)
                                <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-bold rounded-full">Won</span>
                            @else
                                <span class="px-3 py-1 bg-red-100 text-red-800 text-sm font-bold rounded-full">Lost</span>
                            @endif
                            <div class="text-sm text-gray-500 mt-1">{{ $match->player1_score }} - {{ $match->player2_score }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8">No matches played yet</p>
            @endif
        </div>
    @else
        <!-- Create Player Profile CTA -->
        <div class="bg-white rounded-xl shadow-lg p-8 text-center">
            <div class="w-20 h-20 bg-pool-green/10 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="text-4xl">🎱</span>
            </div>
            <h2 class="text-xl font-bold text-gray-800 mb-2">Create Your Player Profile</h2>
            <p class="text-gray-500 mb-6 max-w-md mx-auto">
                To join tournaments and track your stats, you need to create a player profile.
            </p>
            <form action="{{ route('profile.createPlayer') }}" method="POST" class="max-w-md mx-auto">
                @csrf
                <div class="mb-4">
                    <label for="nickname" class="block text-sm font-medium text-gray-700 mb-1 text-left">Nickname (optional)</label>
                    <input type="text" 
                           name="nickname" 
                           id="nickname" 
                           placeholder="Your pool nickname"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pool-green focus:border-transparent">
                </div>
                <div class="mb-6">
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1 text-left">Phone (optional)</label>
                    <input type="text" 
                           name="phone" 
                           id="phone" 
                           placeholder="Your phone number"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pool-green focus:border-transparent">
                </div>
                <button type="submit" class="w-full py-3 bg-pool-green text-white font-semibold rounded-lg hover:bg-pool-felt transition">
                    Create Player Profile
                </button>
            </form>
        </div>
    @endif
</div>
@endsection
