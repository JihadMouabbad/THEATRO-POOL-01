<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Theatro Pool') }} - @yield('title', 'Tournament Management')</title>

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'pool-green': '#1a472a',
                        'pool-felt': '#2d5a3d',
                        'pool-wood': '#8B4513',
                    }
                }
            }
        }
    </script>
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-pool-green shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <!-- Logo -->
                    <a href="{{ route('home') }}" class="flex items-center">
                        <span class="text-2xl mr-2">🎱</span>
                        <span class="font-bold text-xl text-white">Theatro Pool</span>
                    </a>
                    
                    <!-- Main Navigation -->
                    <div class="hidden md:ml-10 md:flex md:space-x-4">
                        <a href="{{ route('tournaments.index') }}" 
                           class="px-3 py-2 rounded-md text-sm font-medium text-white hover:bg-pool-felt transition {{ request()->routeIs('tournaments.*') ? 'bg-pool-felt' : '' }}">
                            Tournaments
                        </a>
                        <a href="{{ route('players.index') }}" 
                           class="px-3 py-2 rounded-md text-sm font-medium text-white hover:bg-pool-felt transition {{ request()->routeIs('players.*') ? 'bg-pool-felt' : '' }}">
                            Players
                        </a>
                        @auth
                            <a href="{{ route('dashboard') }}" 
                               class="px-3 py-2 rounded-md text-sm font-medium text-white hover:bg-pool-felt transition {{ request()->routeIs('dashboard') ? 'bg-pool-felt' : '' }}">
                                Dashboard
                            </a>
                        @endauth
                    </div>
                </div>
                
                <!-- User Menu -->
                <div class="flex items-center">
                    @auth
                        <div class="flex items-center space-x-4">
                            <span class="text-white text-sm">
                                {{ Auth::user()->name }}
                                @if(Auth::user()->isAdmin())
                                    <span class="ml-1 px-2 py-0.5 bg-yellow-500 text-xs rounded-full text-black">Admin</span>
                                @endif
                            </span>
                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 transition">
                                    Logout
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-white hover:text-gray-200 transition">
                                Login
                            </a>
                            <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-medium text-pool-green bg-white rounded-md hover:bg-gray-100 transition">
                                Register
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <main class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-pool-green text-white py-6 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-sm">&copy; {{ date('Y') }} Theatro Pool - 8-Ball Tournament Management</p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
