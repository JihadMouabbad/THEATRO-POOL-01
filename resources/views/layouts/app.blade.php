<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Theatro Pool - Professional 8-Ball Pool Tournament Management System">
    <meta name="theme-color" content="#1a472a">

    <title>{{ config('app.name', 'Theatro Pool') }} - @yield('title', 'Tournament Management')</title>

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'pool-green': '#1a472a',
                        'pool-felt': '#2d5a3d',
                        'pool-light': '#3d7a5d',
                        'pool-wood': '#8B4513',
                        'pool-gold': '#FFD700',
                        'pool-silver': '#C0C0C0',
                        'pool-bronze': '#CD7F32',
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-out',
                        'slide-up': 'slideUp 0.5s ease-out',
                        'pulse-slow': 'pulse 3s infinite',
                        'bounce-slow': 'bounce 2s infinite',
                        'glow': 'glow 2s ease-in-out infinite alternate',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        glow: {
                            '0%': { boxShadow: '0 0 5px rgba(26, 71, 42, 0.5)' },
                            '100%': { boxShadow: '0 0 20px rgba(26, 71, 42, 0.8)' },
                        },
                    },
                }
            }
        }
    </script>
    
    <style>
        [x-cloak] { display: none !important; }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb { background: #1a472a; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #2d5a3d; }
        
        /* Gradient backgrounds */
        .bg-gradient-pool {
            background: linear-gradient(135deg, #1a472a 0%, #2d5a3d 50%, #3d7a5d 100%);
        }
        
        .bg-gradient-hero {
            background: linear-gradient(180deg, #1a472a 0%, #2d5a3d 100%);
        }
        
        /* Glass effect */
        .glass {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
        
        /* Card hover effects */
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px -10px rgba(26, 71, 42, 0.3);
        }
        
        /* Bracket connector lines */
        .bracket-connector::after {
            content: '';
            position: absolute;
            right: -2rem;
            top: 50%;
            width: 2rem;
            height: 2px;
            background: #1a472a;
        }
        
        /* Print styles */
        @media print {
            .no-print { display: none !important; }
            .print-full { width: 100% !important; }
        }
        
        /* Stat counter animation */
        .stat-number {
            font-variant-numeric: tabular-nums;
        }
        
        /* Trophy shine effect */
        .trophy-shine {
            position: relative;
            overflow: hidden;
        }
        .trophy-shine::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                to right,
                transparent 0%,
                rgba(255,255,255,0.3) 50%,
                transparent 100%
            );
            transform: rotate(30deg);
            animation: shine 3s infinite;
        }
        @keyframes shine {
            0% { transform: translateX(-100%) rotate(30deg); }
            100% { transform: translateX(100%) rotate(30deg); }
        }
        
        /* Progress bar animation */
        .progress-bar {
            transition: width 1s ease-in-out;
        }
        
        /* Mobile menu */
        .mobile-menu-enter { animation: slideDown 0.3s ease-out; }
        @keyframes slideDown {
            0% { opacity: 0; transform: translateY(-10px); }
            100% { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen flex flex-col dark:from-gray-900 dark:to-gray-800">
    <!-- Navigation -->
    <nav class="bg-gradient-pool shadow-xl sticky top-0 z-50 no-print">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <!-- Logo -->
                    <a href="{{ route('home') }}" class="flex items-center group">
                        <span class="text-3xl mr-2 group-hover:animate-bounce-slow transition-all">🎱</span>
                        <span class="font-bold text-xl text-white group-hover:text-pool-gold transition-colors">Theatro Pool</span>
                    </a>
                    
                    <!-- Main Navigation -->
                    <div class="hidden md:ml-10 md:flex md:space-x-2">
                        <a href="{{ route('tournaments.index') }}" 
                           class="px-4 py-2 rounded-lg text-sm font-medium text-white hover:bg-white/20 transition-all duration-300 flex items-center gap-2 {{ request()->routeIs('tournaments.*') ? 'bg-white/20 shadow-inner' : '' }}">
                            <span>🏆</span> Tournaments
                        </a>
                        <a href="{{ route('players.index') }}" 
                           class="px-4 py-2 rounded-lg text-sm font-medium text-white hover:bg-white/20 transition-all duration-300 flex items-center gap-2 {{ request()->routeIs('players.*') ? 'bg-white/20 shadow-inner' : '' }}">
                            <span>👥</span> Players
                        </a>
                        @auth
                            <a href="{{ route('dashboard') }}" 
                               class="px-4 py-2 rounded-lg text-sm font-medium text-white hover:bg-white/20 transition-all duration-300 flex items-center gap-2 {{ request()->routeIs('dashboard') ? 'bg-white/20 shadow-inner' : '' }}">
                                <span>📊</span> Dashboard
                            </a>
                        @endauth
                    </div>
                </div>
                
                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button type="button" onclick="toggleMobileMenu()" class="text-white hover:text-gray-200 focus:outline-none p-2">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
                
                <!-- User Menu -->
                <div class="hidden md:flex items-center">
                    @auth
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                                <span class="text-white text-sm font-medium">
                                    {{ Auth::user()->name }}
                                </span>
                                @if(Auth::user()->isAdmin())
                                    <span class="px-2 py-0.5 bg-pool-gold text-xs rounded-full text-black font-bold animate-pulse-slow">Admin</span>
                                @endif
                            </div>
                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-500/80 rounded-lg hover:bg-red-600 transition-all duration-300 hover:shadow-lg">
                                    Logout
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-white hover:text-pool-gold transition-all duration-300">
                                Login
                            </a>
                            <a href="{{ route('register') }}" class="px-5 py-2 text-sm font-bold text-pool-green bg-white rounded-lg hover:bg-pool-gold hover:text-black transition-all duration-300 hover:shadow-lg hover:scale-105">
                                Register
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
        
        <!-- Mobile menu -->
        <div id="mobileMenu" class="hidden md:hidden bg-pool-felt mobile-menu-enter">
            <div class="px-4 py-3 space-y-2">
                <a href="{{ route('tournaments.index') }}" class="block px-4 py-2 text-white hover:bg-white/10 rounded-lg transition">🏆 Tournaments</a>
                <a href="{{ route('players.index') }}" class="block px-4 py-2 text-white hover:bg-white/10 rounded-lg transition">👥 Players</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-white hover:bg-white/10 rounded-lg transition">📊 Dashboard</a>
                    <hr class="border-white/20 my-2">
                    <div class="px-4 py-2 text-white/70 text-sm">Signed in as {{ Auth::user()->name }}</div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-red-300 hover:bg-red-500/20 rounded-lg transition">Logout</button>
                    </form>
                @else
                    <hr class="border-white/20 my-2">
                    <a href="{{ route('login') }}" class="block px-4 py-2 text-white hover:bg-white/10 rounded-lg transition">Login</a>
                    <a href="{{ route('register') }}" class="block px-4 py-2 text-pool-gold font-bold hover:bg-white/10 rounded-lg transition">Register</a>
                @endauth
            </div>
        </div>
    </nav>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        }
    </script>

    <!-- Page Content -->
    <main class="py-8 flex-grow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Flash Messages with animations -->
            @if(session('success'))
                <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-700 px-6 py-4 rounded-r-lg shadow-md animate-slide-up flex items-center gap-3" role="alert">
                    <span class="text-2xl">✅</span>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-500 text-red-700 px-6 py-4 rounded-r-lg shadow-md animate-slide-up flex items-center gap-3" role="alert">
                    <span class="text-2xl">❌</span>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-500 text-red-700 px-6 py-4 rounded-r-lg shadow-md animate-slide-up" role="alert">
                    <div class="flex items-start gap-3">
                        <span class="text-2xl">⚠️</span>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="animate-fade-in">
                @yield('content')
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gradient-pool text-white py-8 mt-auto no-print">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center md:text-left">
                    <div class="flex items-center justify-center md:justify-start gap-2 mb-3">
                        <span class="text-3xl">🎱</span>
                        <span class="text-xl font-bold">Theatro Pool</span>
                    </div>
                    <p class="text-white/70 text-sm">Professional tournament management for billiard halls.</p>
                </div>
                <div class="text-center">
                    <h4 class="font-semibold mb-3">Quick Links</h4>
                    <div class="space-y-2 text-sm text-white/70">
                        <a href="{{ route('tournaments.index') }}" class="block hover:text-white transition">Tournaments</a>
                        <a href="{{ route('players.index') }}" class="block hover:text-white transition">Players</a>
                    </div>
                </div>
                <div class="text-center md:text-right">
                    <h4 class="font-semibold mb-3">Statistics</h4>
                    <div class="text-sm text-white/70">
                        <p>{{ \App\Models\Tournament::count() }} Tournaments</p>
                        <p>{{ \App\Models\Player::count() }} Players</p>
                        <p>{{ \App\Models\PoolMatch::where('status', 'completed')->count() }} Matches</p>
                    </div>
                </div>
            </div>
            <div class="border-t border-white/20 mt-6 pt-6 text-center text-sm text-white/60">
                <p>&copy; {{ date('Y') }} Theatro Pool - Built with ❤️ for Pool Enthusiasts</p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
