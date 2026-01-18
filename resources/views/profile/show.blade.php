@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Profile Header -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden mb-6">
        <div class="h-32 bg-gradient-to-r from-brand via-pool-felt to-success"></div>
        <div class="relative px-6 pb-6">
            <div class="flex flex-col md:flex-row md:items-end gap-4 -mt-12">
                <div class="w-24 h-24 bg-white dark:bg-gray-700 rounded-2xl shadow-lg flex items-center justify-center text-4xl font-bold text-brand dark:text-gold border-4 border-white dark:border-gray-700">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-text-primary dark:text-white">{{ $user->name }}</h1>
                    <p class="text-text-muted dark:text-gray-400">{{ $user->email }}</p>
                    <div class="flex items-center gap-2 mt-1">
                        @if($user->isAdmin())
                            <span class="px-3 py-1 bg-gold text-black text-xs font-bold rounded-full">Admin</span>
                        @else
                            <span class="px-3 py-1 bg-brand text-white text-xs font-bold rounded-full">Player</span>
                        @endif
                        @if($player)
                            <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-400 text-xs font-bold rounded-full">Player Profile Active</span>
                        @endif
                    </div>
                </div>
                <a href="{{ route('profile.edit') }}" class="px-6 py-2 bg-brand text-white font-semibold rounded-xl hover:bg-success transition">
                    ✏️ Edit Profile
                </a>
            </div>
        </div>
    </div>

    @if($player)
        <!-- Player Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 text-center card-hover">
                <div class="text-3xl font-black text-success dark:text-green-400">{{ $player->wins }}</div>
                <div class="text-sm text-text-muted dark:text-gray-400 font-medium">Wins</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 text-center card-hover">
                <div class="text-3xl font-black text-danger dark:text-red-400">{{ $player->losses }}</div>
                <div class="text-sm text-text-muted dark:text-gray-400 font-medium">Losses</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 text-center card-hover">
                <div class="text-3xl font-black text-brand dark:text-gold">{{ $player->total_matches }}</div>
                <div class="text-sm text-text-muted dark:text-gray-400 font-medium">Total Matches</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 text-center card-hover">
                <div class="text-3xl font-black text-blue-600 dark:text-blue-400">{{ $player->win_rate }}%</div>
                <div class="text-sm text-text-muted dark:text-gray-400 font-medium">Win Rate</div>
            </div>
        </div>

        <!-- Player Details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h2 class="text-lg font-bold text-text-primary dark:text-white mb-4 flex items-center gap-2">
                    <span>👤</span> Player Details
                </h2>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-text-muted dark:text-gray-400">Name</span>
                        <span class="font-medium text-text-primary dark:text-white">{{ $player->name }}</span>
                    </div>
                    @if($player->nickname)
                    <div class="flex justify-between">
                        <span class="text-text-muted dark:text-gray-400">Nickname</span>
                        <span class="font-medium text-text-primary dark:text-white">{{ $player->nickname }}</span>
                    </div>
                    @endif
                    @if($player->phone)
                    <div class="flex justify-between">
                        <span class="text-text-muted dark:text-gray-400">Phone</span>
                        <span class="font-medium text-text-primary dark:text-white">{{ $player->phone }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-text-muted dark:text-gray-400">Member Since</span>
                        <span class="font-medium text-text-primary dark:text-white">{{ $player->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h2 class="text-lg font-bold text-text-primary dark:text-white mb-4 flex items-center gap-2">
                    <span>🏆</span> Active Tournaments
                </h2>
                @if($upcomingTournaments->count() > 0)
                    <div class="space-y-3">
                        @foreach($upcomingTournaments as $tournament)
                        <a href="{{ route('tournaments.show', $tournament) }}" class="block p-3 bg-surface dark:bg-gray-700/50 rounded-lg hover:bg-surface-alt dark:hover:bg-gray-700 transition">
                            <div class="font-medium text-text-primary dark:text-white">{{ $tournament->name }}</div>
                            <div class="text-sm text-text-muted dark:text-gray-400">{{ $tournament->start_date->format('M d, Y') }} • {{ ucfirst($tournament->status) }}</div>
                        </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-text-muted dark:text-gray-400 text-center py-4">No active tournaments</p>
                    <a href="{{ route('tournaments.index') }}" class="block text-center text-brand dark:text-gold hover:underline">
                        Browse tournaments →
                    </a>
                @endif
            </div>
        </div>

        <!-- Recent Matches -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <h2 class="text-lg font-bold text-text-primary dark:text-white mb-4 flex items-center gap-2">
                <span>🎱</span> Recent Matches
            </h2>
            @if($recentMatches->count() > 0)
                <div class="space-y-3">
                    @foreach($recentMatches as $match)
                    <div class="p-4 bg-surface dark:bg-gray-700/50 rounded-lg flex items-center justify-between">
                        <div>
                            <div class="font-medium text-text-primary dark:text-white">
                                @if($match->player1_id === $player->id)
                                    vs {{ $match->player2->display_name ?? 'TBD' }}
                                @else
                                    vs {{ $match->player1->display_name ?? 'TBD' }}
                                @endif
                            </div>
                            <div class="text-sm text-text-muted dark:text-gray-400">{{ $match->tournament->name }}</div>
                        </div>
                        <div class="text-right">
                            @if($match->winner_id === $player->id)
                                <span class="px-3 py-1 bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-400 text-sm font-bold rounded-full">Won</span>
                            @else
                                <span class="px-3 py-1 bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-400 text-sm font-bold rounded-full">Lost</span>
                            @endif
                            <div class="text-sm text-text-muted dark:text-gray-400 mt-1">{{ $match->player1_score }} - {{ $match->player2_score }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-text-muted dark:text-gray-400 text-center py-8">No matches played yet</p>
            @endif
        </div>
    @else
        <!-- Create Player Profile CTA -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 text-center">
            <div class="w-20 h-20 bg-brand/10 dark:bg-brand/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="text-4xl">🎱</span>
            </div>
            <h2 class="text-xl font-bold text-text-primary dark:text-white mb-2">Create Your Player Profile</h2>
            <p class="text-text-muted dark:text-gray-400 mb-6 max-w-md mx-auto">
                To join tournaments and track your stats, you need to create a player profile.
            </p>
            <form action="{{ route('profile.createPlayer') }}" method="POST" class="max-w-md mx-auto">
                @csrf
                <div class="mb-4">
                    <label for="nickname" class="block text-sm font-medium text-text-secondary dark:text-gray-300 mb-1 text-left">Nickname (optional)</label>
                    <input type="text"
                           name="nickname"
                           id="nickname"
                           placeholder="Your pool nickname"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent bg-white dark:bg-gray-700 dark:text-white dark:placeholder-gray-400">
                </div>
                <div class="mb-6">
                    <label for="phone" class="block text-sm font-medium text-text-secondary dark:text-gray-300 mb-1 text-left">Phone (optional)</label>
                    <input type="text"
                           name="phone"
                           id="phone"
                           placeholder="Your phone number"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent bg-white dark:bg-gray-700 dark:text-white dark:placeholder-gray-400">
                </div>
                <button type="submit" class="w-full py-3 bg-brand text-white font-semibold rounded-lg hover:bg-success transition">
                    Create Player Profile
                </button>
            </form>
        </div>
    @endif
</div>
@endsection
