@extends('layouts.app')

@section('title', 'Create Tournament')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('tournaments.index') }}" class="text-brand hover:text-success transition">&larr; Back to Tournaments</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-8">
        <h1 class="text-2xl font-bold text-brand dark:text-gold mb-6">Create New Tournament</h1>

        <form action="{{ route('tournaments.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-text-secondary dark:text-gray-300 mb-1">Tournament Name *</label>
                <input type="text"
                       name="name"
                       id="name"
                       value="{{ old('name') }}"
                       required
                       placeholder="e.g., Summer Pool Championship 2024"
                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent bg-white dark:bg-gray-700 text-text-primary dark:text-white @error('name') border-danger @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-text-secondary dark:text-gray-300 mb-1">Description</label>
                <textarea name="description"
                          id="description"
                          rows="3"
                          placeholder="Describe the tournament..."
                          class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent bg-white dark:bg-gray-700 text-text-primary dark:text-white @error('description') border-danger @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-text-secondary dark:text-gray-300 mb-1">Start Date *</label>
                    <input type="date"
                           name="start_date"
                           id="start_date"
                           value="{{ old('start_date') }}"
                           required
                           min="{{ date('Y-m-d') }}"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent bg-white dark:bg-gray-700 text-text-primary dark:text-white @error('start_date') border-danger @enderror">
                    @error('start_date')
                        <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="max_players" class="block text-sm font-medium text-text-secondary dark:text-gray-300 mb-1">Tournament Size *</label>
                    <select name="max_players"
                            id="max_players"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent bg-white dark:bg-gray-700 text-text-primary dark:text-white @error('max_players') border-danger @enderror">
                        <option value="">Select size</option>
                        @foreach($allowedPlayerCounts as $count)
                            <option value="{{ $count }}" {{ old('max_players') == $count ? 'selected' : '' }}>
                                {{ $count }} Players ({{ (int) log($count, 2) }} rounds)
                            </option>
                        @endforeach
                    </select>
                    @error('max_players')
                        <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-6">
                <label for="location" class="block text-sm font-medium text-text-secondary dark:text-gray-300 mb-1">Location *</label>
                <input type="text"
                       name="location"
                       id="location"
                       value="{{ old('location') }}"
                       required
                       placeholder="e.g., Theatro Pool Hall - Main Room"
                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent bg-white dark:bg-gray-700 text-text-primary dark:text-white @error('location') border-danger @enderror">
                @error('location')
                    <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                @enderror
            </div>

            <!-- Info Box -->
            <div class="mb-6 bg-brand/10 dark:bg-brand/20 border border-brand/30 rounded-lg p-4">
                <h4 class="font-semibold text-brand dark:text-gold mb-2">ℹ️ Tournament Information</h4>
                <ul class="text-sm text-text-secondary dark:text-gray-300 space-y-1">
                    <li>• Single-elimination format (one loss and you're out)</li>
                    <li>• You need exactly the selected number of players to start</li>
                    <li>• Bracket will be automatically generated when you start the tournament</li>
                </ul>
            </div>

            <div class="flex gap-4">
                <button type="submit"
                        class="flex-1 py-3 bg-brand hover:bg-success text-white font-semibold rounded-lg transition">
                    Create Tournament
                </button>
                <a href="{{ route('tournaments.index') }}"
                   class="flex-1 py-3 text-center border-2 border-gray-300 dark:border-gray-600 text-text-secondary dark:text-gray-300 font-semibold rounded-lg hover:bg-surface dark:hover:bg-gray-700 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
