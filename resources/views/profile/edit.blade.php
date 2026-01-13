@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('profile.show') }}" class="text-pool-green hover:underline">&larr; Back to Profile</a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-8">
        <h1 class="text-2xl font-bold text-pool-green mb-6">Edit Profile</h1>

        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b">Account Information</h3>
                
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           value="{{ old('name', $user->name) }}"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pool-green focus:border-transparent @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" 
                           name="email" 
                           id="email" 
                           value="{{ old('email', $user->email) }}"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pool-green focus:border-transparent @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            @if($player)
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b">Player Profile</h3>
                
                <div class="mb-4">
                    <label for="nickname" class="block text-sm font-medium text-gray-700 mb-1">Nickname</label>
                    <input type="text" 
                           name="nickname" 
                           id="nickname" 
                           value="{{ old('nickname', $player->nickname) }}"
                           placeholder="Your pool nickname"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pool-green focus:border-transparent @error('nickname') border-red-500 @enderror">
                    @error('nickname')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" 
                           name="phone" 
                           id="phone" 
                           value="{{ old('phone', $player->phone) }}"
                           placeholder="Your phone number"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pool-green focus:border-transparent @error('phone') border-red-500 @enderror">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Player Stats (read-only) -->
                <div class="bg-gray-50 rounded-lg p-4 mt-4">
                    <h4 class="text-sm font-medium text-gray-500 mb-3">Your Stats (read-only)</h4>
                    <div class="grid grid-cols-4 gap-4 text-center">
                        <div>
                            <div class="text-xl font-bold text-green-600">{{ $player->wins }}</div>
                            <div class="text-xs text-gray-500">Wins</div>
                        </div>
                        <div>
                            <div class="text-xl font-bold text-red-600">{{ $player->losses }}</div>
                            <div class="text-xs text-gray-500">Losses</div>
                        </div>
                        <div>
                            <div class="text-xl font-bold text-gray-600">{{ $player->total_matches }}</div>
                            <div class="text-xs text-gray-500">Matches</div>
                        </div>
                        <div>
                            <div class="text-xl font-bold text-blue-600">{{ $player->win_rate }}%</div>
                            <div class="text-xs text-gray-500">Win Rate</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="flex gap-4">
                <button type="submit" 
                        class="flex-1 py-3 bg-pool-green text-white font-semibold rounded-lg hover:bg-pool-felt transition">
                    Save Changes
                </button>
                <a href="{{ route('profile.show') }}" 
                   class="flex-1 py-3 text-center border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
