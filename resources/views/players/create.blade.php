@extends('layouts.app')

@section('title', 'Add Player')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('players.index') }}" class="text-brand hover:underline">&larr; Back to Players</a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-8">
        <h1 class="text-2xl font-bold text-brand mb-6">Add New Player</h1>

        <form action="{{ route('players.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-text-secondary mb-1">Full Name *</label>
                <input type="text" 
                       name="name" 
                       id="name" 
                       value="{{ old('name') }}"
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent @error('name') border-danger @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="nickname" class="block text-sm font-medium text-text-secondary mb-1">Nickname</label>
                <input type="text" 
                       name="nickname" 
                       id="nickname" 
                       value="{{ old('nickname') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent @error('nickname') border-danger @enderror">
                @error('nickname')
                    <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-text-secondary mb-1">Email</label>
                <input type="email" 
                       name="email" 
                       id="email" 
                       value="{{ old('email') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent @error('email') border-danger @enderror">
                @error('email')
                    <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="phone" class="block text-sm font-medium text-text-secondary mb-1">Phone</label>
                <input type="text" 
                       name="phone" 
                       id="phone" 
                       value="{{ old('phone') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent @error('phone') border-danger @enderror">
                @error('phone')
                    <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-text-secondary mb-1">Notes</label>
                <textarea name="notes" 
                          id="notes" 
                          rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent @error('notes') border-danger @enderror">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-4">
                <button type="submit" 
                        class="flex-1 py-3 bg-brand text-white font-semibold rounded-lg hover:bg-success transition">
                    Add Player
                </button>
                <a href="{{ route('players.index') }}" 
                   class="flex-1 py-3 text-center border-2 border-gray-300 text-text-secondary font-semibold rounded-lg hover:bg-surface transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
