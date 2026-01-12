@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Header with gradient -->
            <div class="bg-gradient-pool p-8 text-center">
                <span class="text-6xl inline-block animate-bounce-slow">🎱</span>
                <h2 class="text-2xl font-bold text-white mt-4">Welcome Back!</h2>
                <p class="text-white/70 mt-1">Login to manage your tournaments</p>
            </div>

            <div class="p-8">
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-5">
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">✉️</span>
                            <input type="email" 
                                   name="email" 
                                   id="email" 
                                   value="{{ old('email') }}"
                                   required 
                                   autofocus
                                   placeholder="your@email.com"
                                   class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-pool-green focus:border-pool-green transition @error('email') border-red-500 @enderror">
                        </div>
                        @error('email')
                            <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                <span>⚠️</span> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="mb-5">
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">🔒</span>
                            <input type="password" 
                                   name="password" 
                                   id="password" 
                                   required
                                   placeholder="••••••••"
                                   class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-pool-green focus:border-pool-green transition @error('password') border-red-500 @enderror">
                        </div>
                        @error('password')
                            <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                <span>⚠️</span> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="flex items-center cursor-pointer group">
                            <input type="checkbox" name="remember" class="w-5 h-5 rounded border-2 border-gray-300 text-pool-green focus:ring-pool-green transition">
                            <span class="ml-3 text-sm text-gray-600 group-hover:text-pool-green transition">Remember me</span>
                        </label>
                    </div>

                    <button type="submit" 
                            class="w-full py-4 bg-gradient-to-r from-pool-green to-pool-felt text-white font-bold rounded-xl hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02] flex items-center justify-center gap-2">
                        <span>🎱</span> Login
                    </button>
                </form>

                <div class="mt-8 text-center">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-white text-gray-500">New to Theatro Pool?</span>
                        </div>
                    </div>
                    <a href="{{ route('register') }}" class="mt-4 inline-flex items-center gap-2 text-pool-green font-bold hover:text-pool-felt transition">
                        <span>✨</span> Create an account
                    </a>
                </div>
            </div>
        </div>

        <!-- Demo credentials hint -->
        <div class="mt-6 text-center text-sm text-gray-500">
            <p class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                <strong>Demo Credentials:</strong><br>
                Admin: admin@theatropool.com / password<br>
                Player: player@theatropool.com / password
            </p>
        </div>
    </div>
</div>
@endsection
