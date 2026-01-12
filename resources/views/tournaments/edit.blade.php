@extends('layouts.app')

@section('title', 'Edit ' . $tournament->name)

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('tournaments.show', $tournament) }}" class="text-pool-green hover:underline">&larr; Back to Tournament</a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-8">
        <h1 class="text-2xl font-bold text-pool-green mb-6">Edit Tournament</h1>

        <form action="{{ route('tournaments.update', $tournament) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Tournament Name *</label>
                <input type="text" 
                       name="name" 
                       id="name" 
                       value="{{ old('name', $tournament->name) }}"
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pool-green focus:border-transparent @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" 
                          id="description" 
                          rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pool-green focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description', $tournament->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date *</label>
                    <input type="date" 
                           name="start_date" 
                           id="start_date" 
                           value="{{ old('start_date', $tournament->start_date->format('Y-m-d')) }}"
                           required
                           min="{{ date('Y-m-d') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pool-green focus:border-transparent @error('start_date') border-red-500 @enderror">
                    @error('start_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="max_players" class="block text-sm font-medium text-gray-700 mb-1">Tournament Size *</label>
                    <select name="max_players" 
                            id="max_players" 
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pool-green focus:border-transparent @error('max_players') border-red-500 @enderror">
                        @foreach($allowedPlayerCounts as $count)
                            @if($count >= $tournament->players()->count())
                            <option value="{{ $count }}" {{ old('max_players', $tournament->max_players) == $count ? 'selected' : '' }}>
                                {{ $count }} Players
                            </option>
                            @endif
                        @endforeach
                    </select>
                    @error('max_players')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Cannot be less than current registered players ({{ $tournament->players()->count() }})</p>
                </div>
            </div>

            <div class="mb-6">
                <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location *</label>
                <input type="text" 
                       name="location" 
                       id="location" 
                       value="{{ old('location', $tournament->location) }}"
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pool-green focus:border-transparent @error('location') border-red-500 @enderror">
                @error('location')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-4">
                <button type="submit" 
                        class="flex-1 py-3 bg-pool-green text-white font-semibold rounded-lg hover:bg-pool-felt transition">
                    Update Tournament
                </button>
                <a href="{{ route('tournaments.show', $tournament) }}" 
                   class="flex-1 py-3 text-center border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <!-- Delete Tournament -->
    <div class="mt-8 bg-red-50 rounded-lg border border-red-200 p-6">
        <h3 class="text-lg font-semibold text-red-700 mb-2">Danger Zone</h3>
        <p class="text-red-600 text-sm mb-4">Deleting a tournament will remove all associated matches and player registrations.</p>
        <form action="{{ route('tournaments.destroy', $tournament) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this tournament? This action cannot be undone.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                Delete Tournament
            </button>
        </form>
    </div>
</div>
@endsection
