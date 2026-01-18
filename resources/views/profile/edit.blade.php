@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('profile.show') }}" class="text-brand dark:text-gold hover:underline">&larr; Back to Profile</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-8">
        <h1 class="text-2xl font-bold text-brand dark:text-gold mb-6">Edit Profile</h1>

        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <h3 class="text-lg font-semibold text-text-secondary dark:text-gray-200 mb-4 pb-2 border-b dark:border-gray-700">Account Information</h3>

                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-text-secondary dark:text-gray-300 mb-1">Full Name *</label>
                    <input type="text"
                           name="name"
                           id="name"
                           value="{{ old('name', $user->name) }}"
                           required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent bg-white dark:bg-gray-700 dark:text-white @error('name') border-danger @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-danger dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-text-secondary dark:text-gray-300 mb-1">Email *</label>
                    <input type="email"
                           name="email"
                           id="email"
                           value="{{ old('email', $user->email) }}"
                           required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent bg-white dark:bg-gray-700 dark:text-white @error('email') border-danger @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-danger dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            @if($player)
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-text-secondary dark:text-gray-200 mb-4 pb-2 border-b dark:border-gray-700">Player Profile</h3>

                <div class="mb-4">
                    <label for="nickname" class="block text-sm font-medium text-text-secondary dark:text-gray-300 mb-1">Nickname</label>
                    <input type="text"
                           name="nickname"
                           id="nickname"
                           value="{{ old('nickname', $player->nickname) }}"
                           placeholder="Your pool nickname"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent bg-white dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 @error('nickname') border-danger @enderror">
                    @error('nickname')
                        <p class="mt-1 text-sm text-danger dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="phone" class="block text-sm font-medium text-text-secondary dark:text-gray-300 mb-1">Phone</label>
                    <input type="text"
                           name="phone"
                           id="phone"
                           value="{{ old('phone', $player->phone) }}"
                           placeholder="Your phone number"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent bg-white dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 @error('phone') border-danger @enderror">
                    @error('phone')
                        <p class="mt-1 text-sm text-danger dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Player Stats (read-only) -->
                <div class="bg-surface dark:bg-gray-700/50 rounded-lg p-4 mt-4">
                    <h4 class="text-sm font-medium text-text-muted dark:text-gray-400 mb-3">Your Stats (read-only)</h4>
                    <div class="grid grid-cols-4 gap-4 text-center">
                        <div>
                            <div class="text-xl font-bold text-success dark:text-green-400">{{ $player->wins }}</div>
                            <div class="text-xs text-text-muted dark:text-gray-400">Wins</div>
                        </div>
                        <div>
                            <div class="text-xl font-bold text-danger dark:text-red-400">{{ $player->losses }}</div>
                            <div class="text-xs text-text-muted dark:text-gray-400">Losses</div>
                        </div>
                        <div>
                            <div class="text-xl font-bold text-text-secondary dark:text-gray-300">{{ $player->total_matches }}</div>
                            <div class="text-xs text-text-muted dark:text-gray-400">Matches</div>
                        </div>
                        <div>
                            <div class="text-xl font-bold text-blue-600 dark:text-blue-400">{{ $player->win_rate }}%</div>
                            <div class="text-xs text-text-muted dark:text-gray-400">Win Rate</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="flex gap-4">
                <button type="submit"
                        class="flex-1 py-3 bg-brand text-white font-semibold rounded-lg hover:bg-success transition">
                    Save Changes
                </button>
                <a href="{{ route('profile.show') }}"
                   class="flex-1 py-3 text-center border-2 border-gray-300 dark:border-gray-600 text-text-secondary dark:text-gray-200 font-semibold rounded-lg hover:bg-surface dark:hover:bg-gray-700 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
