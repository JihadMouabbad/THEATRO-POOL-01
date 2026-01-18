<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth" x-data="themeManager()" x-init="init()" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Theatro Pool - Professional 8-Ball Pool Tournament Management System">
    <meta name="theme-color" content="#1a472a" id="theme-color-meta">

    <title>{{ config('app.name', 'Theatro Pool') }} - @yield('title', 'Tournament Management')</title>

    <!-- Alpine.js for reactive theme management -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Prevent flash of wrong theme -->
    <script>
        if (localStorage.getItem('darkMode') === 'true' || (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        // PALETTE BILLARD PROFESSIONNELLE
                        // Vert principal (brand / header / boutons)
                        'brand': '#0F3D2E',
                        'brand-dark': '#0a2d22',
                        'brand-light': '#145a43',
                        // Vert secondaire (hover / live / succès)
                        'success': '#1FA36B',
                        'success-dark': '#178a59',
                        'success-light': '#25c47f',
                        // Blanc cassé (background principal)
                        'surface': '#F7F9F8',
                        'surface-alt': '#EEF2F0',
                        // Gris foncé (texte principal)
                        'text-primary': '#1E1E1E',
                        'text-secondary': '#4A4A4A',
                        'text-muted': '#6B7280',
                        // Or discret (trophées / victoires / highlights)
                        'gold': '#C9A227',
                        'gold-dark': '#a8861f',
                        'gold-light': '#dbb82d',
                        // Couleurs sémantiques
                        'danger': '#DC2626',
                        'warning': '#F59E0B',
                        'info': '#3B82F6',
                        // LEGACY aliases (pour compatibilité)
                        'pool-green': '#0F3D2E',
                        'pool-felt': '#145a43',
                        'pool-light': '#1FA36B',
                        'pool-gold': '#C9A227',
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
        /* ===========================================
           PALETTE BILLARD PROFESSIONNELLE - VARIABLES CSS
           ===========================================
           Vert principal   : #0F3D2E (header, sidebar, footer, boutons primaires)
           Vert secondaire  : #1FA36B (hover, live, succès)
           Blanc cassé      : #F7F9F8 (background principal)
           Gris foncé       : #1E1E1E (texte principal)
           Or discret       : #C9A227 (trophées, victoires, highlights)
        */
        :root {
            /* Couleurs principales */
            --brand: #0F3D2E;
            --brand-dark: #0a2d22;
            --brand-light: #145a43;
            --success: #1FA36B;
            --success-dark: #178a59;
            --surface: #F7F9F8;
            --surface-alt: #EEF2F0;
            --text-primary: #1E1E1E;
            --text-secondary: #4A4A4A;
            --text-muted: #6B7280;
            --gold: #C9A227;
            --gold-dark: #a8861f;
            --danger: #DC2626;
            --warning: #F59E0B;
            --info: #3B82F6;
        }

        /* Base body styles */
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.5;
            margin: 0;
            padding: 0;
            background-color: var(--surface);
            color: var(--text-primary);
            min-height: 100vh;
        }

        a { color: var(--brand); text-decoration: none; }
        a:hover { color: var(--success); }

        [x-cloak] { display: none !important; }

        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: var(--surface-alt); border-radius: 4px; }
        ::-webkit-scrollbar-thumb { background: var(--brand); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--brand-light); }

        /* Gradient backgrounds */
        .bg-gradient-brand {
            background: linear-gradient(135deg, var(--brand) 0%, var(--brand-light) 100%);
        }

        .bg-gradient-pool {
            background: linear-gradient(135deg, #0F3D2E 0%, #145a43 50%, #1FA36B 100%);
            box-shadow: 0 4px 20px rgba(15, 61, 46, 0.3);
        }

        .bg-gradient-hero {
            background: linear-gradient(180deg, #0F3D2E 0%, #145a43 100%);
        }

        /* Navigation link hover effect */
        nav a:hover {
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
        }

        /* Active nav link */
        nav a.bg-white\/20 {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Glass effect */
        .glass {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        /* Card styling */
        .card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        /* Card hover effects */
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px -10px rgba(15, 61, 46, 0.3);
        }

        /* Brand button styles */
        .btn-brand {
            background-color: var(--brand);
            color: white;
            transition: all 0.3s ease;
        }
        .btn-brand:hover {
            background-color: var(--success);
        }

        /* Success/Live button styles */
        .btn-success {
            background-color: var(--success);
            color: white;
        }
        .btn-success:hover {
            background-color: var(--success-dark);
        }

        /* Gold highlight */
        .text-gold { color: var(--gold); }
        .bg-gold { background-color: var(--gold); }
        .border-gold { border-color: var(--gold); }

        /* Badge styles */
        .badge-live {
            background-color: var(--success);
            color: white;
            animation: pulse 2s infinite;
        }

        .badge-gold {
            background-color: rgba(201, 162, 39, 0.15);
            color: var(--gold-dark);
        }

        /* Bracket connector lines */
        .bracket-connector::after {
            content: '';
            position: absolute;
            right: -2rem;
            top: 50%;
            width: 2rem;
            height: 2px;
            background: var(--brand);
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

        /* Reduced motion accessibility support */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
            .animate-bounce-slow, .animate-pulse-slow, .animate-pulse, .animate-glow {
                animation: none !important;
            }
            .trophy-shine::after {
                animation: none !important;
            }
        }

        /* Dark mode specific styles */
        .dark body,
        .dark {
            color-scheme: dark;
            --surface: #121212;
            --surface-alt: #1e1e1e;
            --text-primary: #F7F9F8;
            --text-secondary: #B0B0B0;
            --text-muted: #808080;
        }

        .dark .glass-effect {
            background: rgba(30, 30, 30, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .dark .card-hover:hover {
            box-shadow: 0 12px 24px -10px rgba(0, 0, 0, 0.7);
        }

        .dark .bracket-connector::after {
            background: var(--gold);
        }

        /* Dark mode navigation */
        .dark .bg-gradient-pool {
            background: linear-gradient(135deg, #0a2d22 0%, #0F3D2E 50%, #145a43 100%);
            border-bottom: 1px solid rgba(201, 162, 39, 0.3);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
        }

        .dark nav a:hover {
            text-shadow: 0 0 15px rgba(201, 162, 39, 0.4);
        }

        .dark nav a.bg-white\/20 {
            background: rgba(201, 162, 39, 0.15);
            border: 1px solid rgba(201, 162, 39, 0.2);
        }

        /* Dark mode cards */
        .dark .bg-white {
            background-color: #1e1e1e !important;
        }

        .dark .shadow-lg,
        .dark .shadow-xl {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5), 0 8px 10px -6px rgba(0, 0, 0, 0.3) !important;
        }

        /* Dark mode text colors */
        .dark .text-text-secondary,
        .dark .text-text-secondary {
            color: #B0B0B0 !important;
        }

        .dark .text-text-muted {
            color: #808080 !important;
        }

        .dark .text-text-primary,
        .dark .text-gray-900 {
            color: #F7F9F8 !important;
        }

        .dark .text-brand,
        .dark .text-brand {
            color: var(--gold) !important;
        }

        /* Dark mode borders */
        .dark .border-gray-200,
        .dark .border-gray-300 {
            border-color: #2d2d2d !important;
        }

        /* Dark mode backgrounds */
        .dark .bg-surface,
        .dark .bg-surface-alt {
            background-color: #121212 !important;
        }

        .dark .bg-gray-200 {
            background-color: #1e1e1e !important;
        }

        /* Dark mode hover states */
        .dark .hover\:bg-surface:hover {
            background-color: #2d2d2d !important;
        }

        /* Dark mode form inputs */
        .dark input,
        .dark select,
        .dark textarea {
            background-color: #1e1e1e !important;
            border-color: #2d2d2d !important;
            color: #F7F9F8 !important;
        }

        .dark input:focus,
        .dark select:focus,
        .dark textarea:focus {
            border-color: var(--gold) !important;
            box-shadow: 0 0 0 3px rgba(201, 162, 39, 0.2) !important;
        }

        .dark input::placeholder,
        .dark textarea::placeholder {
            color: #737373 !important;
        }

        /* Dark mode tables */
        .dark table {
            background-color: #1e1e1e;
        }

        .dark th {
            background-color: #121212 !important;
            color: #F7F9F8 !important;
        }

        .dark td {
            border-color: #2d2d2d !important;
        }

        .dark tr:hover td {
            background-color: #2d2d2d !important;
        }

        /* Dark mode footer */
        .dark footer {
            background: linear-gradient(135deg, #0a2d22 0%, #0F3D2E 100%) !important;
        }

        /* Dark mode badges */
        .dark .bg-green-100 { background-color: rgba(31, 163, 107, 0.2) !important; }
        .dark .bg-blue-100 { background-color: rgba(59, 130, 246, 0.2) !important; }
        .dark .bg-yellow-100 { background-color: rgba(201, 162, 39, 0.2) !important; }
        .dark .bg-red-100 { background-color: rgba(220, 38, 38, 0.2) !important; }

        .dark .text-green-800,
        .dark .text-success { color: #1FA36B !important; }
        .dark .text-blue-800,
        .dark .text-blue-700 { color: #60a5fa !important; }
        .dark .text-yellow-800,
        .dark .text-yellow-700 { color: var(--gold) !important; }
        .dark .text-red-800,
        .dark .text-danger { color: #f87171 !important; }

        /* Dark mode dividers */
        .dark hr,
        .dark .divide-gray-200 > * + * {
            border-color: #2d2d2d !important;
        }

        /* Dark scrollbar */
        .dark ::-webkit-scrollbar-track { background: #1e1e1e; }
        .dark ::-webkit-scrollbar-thumb { background: #2d2d2d; }
        .dark ::-webkit-scrollbar-thumb:hover { background: #404040; }

        /* Theme toggle button animation */
        .theme-toggle {
            transition: transform 0.3s ease, background-color 0.3s ease;
        }
        .theme-toggle:hover {
            transform: rotate(15deg) scale(1.1);
        }

        /* Dark mode transitions */
        .dark-transition {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }
    </style>
</head>
<body class="bg-surface dark:bg-[#121212] min-h-screen flex flex-col transition-colors duration-300">
    <!-- Navigation -->
    <nav class="bg-gradient-pool shadow-xl sticky top-0 z-50 no-print">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <!-- Logo -->
                    <a href="{{ route('home') }}" class="flex items-center group">
                        <span class="text-3xl mr-2 group-hover:animate-bounce-slow transition-all">🎱</span>
                        <span class="font-bold text-xl text-white group-hover:text-gold transition-colors">Theatro Pool</span>
                    </a>

                    <!-- Main Navigation -->
                    <div class="hidden md:ml-10 md:flex md:space-x-1">
                        <a href="{{ route('tournaments.index') }}"
                           class="px-3 py-2 rounded-lg text-sm font-medium text-white hover:bg-white/20 transition-all duration-300 flex items-center gap-1 {{ request()->routeIs('tournaments.*') ? 'bg-white/20 shadow-inner' : '' }}">
                            <span>🏆</span> Tournaments
                        </a>
                        <a href="{{ route('players.index') }}"
                           class="px-3 py-2 rounded-lg text-sm font-medium text-white hover:bg-white/20 transition-all duration-300 flex items-center gap-1 {{ request()->routeIs('players.*') ? 'bg-white/20 shadow-inner' : '' }}">
                            <span>👥</span> Players
                        </a>
                        <a href="{{ route('rankings.index') }}"
                           class="px-3 py-2 rounded-lg text-sm font-medium text-white hover:bg-white/20 transition-all duration-300 flex items-center gap-1 {{ request()->routeIs('rankings.*') ? 'bg-white/20 shadow-inner' : '' }}">
                            <span>👑</span> Rankings
                        </a>
                        <a href="{{ route('head-to-head.index') }}"
                           class="px-3 py-2 rounded-lg text-sm font-medium text-white hover:bg-white/20 transition-all duration-300 flex items-center gap-1 {{ request()->routeIs('head-to-head.*') ? 'bg-white/20 shadow-inner' : '' }}">
                            <span>⚔️</span> H2H
                        </a>
                        <a href="{{ route('activity.index') }}"
                           class="px-3 py-2 rounded-lg text-sm font-medium text-white hover:bg-white/20 transition-all duration-300 flex items-center gap-1 {{ request()->routeIs('activity.*') ? 'bg-white/20 shadow-inner' : '' }}">
                            <span>📰</span> Activity
                        </a>
                        <a href="{{ route('statistics.index') }}"
                           class="px-3 py-2 rounded-lg text-sm font-medium text-white hover:bg-white/20 transition-all duration-300 flex items-center gap-1 {{ request()->routeIs('statistics.*') ? 'bg-white/20 shadow-inner' : '' }}">
                            <span>📊</span> Stats
                        </a>
                        <a href="{{ route('rules.index') }}"
                           class="px-3 py-2 rounded-lg text-sm font-medium text-white hover:bg-white/20 transition-all duration-300 flex items-center gap-1 {{ request()->routeIs('rules.*') ? 'bg-white/20 shadow-inner' : '' }}">
                            <span>📋</span> Rules
                        </a>
                        <a href="{{ route('archive.index') }}"
                           class="px-3 py-2 rounded-lg text-sm font-medium text-white hover:bg-white/20 transition-all duration-300 flex items-center gap-1 {{ request()->routeIs('archive.*') ? 'bg-white/20 shadow-inner' : '' }}">
                            <span>📚</span> Archive
                        </a>
                        @auth
                            <a href="{{ route('dashboard') }}"
                               class="px-3 py-2 rounded-lg text-sm font-medium text-white hover:bg-white/20 transition-all duration-300 flex items-center gap-1 {{ request()->routeIs('dashboard') ? 'bg-white/20 shadow-inner' : '' }}">
                                <span>⚙️</span> Dashboard
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center gap-2">
                    <!-- Mobile Theme Toggle -->
                    <button @click="darkMode = !darkMode" type="button"
                            class="theme-toggle p-2 rounded-lg bg-white/10 hover:bg-white/20 text-white focus:outline-none focus:ring-2 focus:ring-white/30"
                            :title="darkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode'">
                        <!-- Sun Icon (shown in dark mode) -->
                        <svg x-show="darkMode" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <!-- Moon Icon (shown in light mode) -->
                        <svg x-show="!darkMode" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                    </button>

                    <button type="button" onclick="toggleMobileMenu()" class="text-white hover:text-gray-200 focus:outline-none p-2">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>

                <!-- User Menu -->
                <div class="hidden md:flex items-center gap-4">
                    <!-- Desktop Theme Toggle -->
                    <button @click="darkMode = !darkMode" type="button"
                            class="theme-toggle p-2 rounded-lg bg-white/10 hover:bg-white/20 text-white focus:outline-none focus:ring-2 focus:ring-white/30"
                            :title="darkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode'">
                        <!-- Sun Icon (shown in dark mode) -->
                        <svg x-show="darkMode" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 rotate-90" x-transition:enter-end="opacity-100 rotate-0" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <!-- Moon Icon (shown in light mode) -->
                        <svg x-show="!darkMode" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -rotate-90" x-transition:enter-end="opacity-100 rotate-0" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                    </button>

                    @auth
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('profile.show') }}" class="flex items-center gap-2 hover:opacity-80 transition">
                                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                                <span class="text-white text-sm font-medium">
                                    {{ Auth::user()->name }}
                                </span>
                                @if(Auth::user()->isAdmin())
                                    <span class="px-2 py-0.5 bg-gold text-xs rounded-full text-black font-bold animate-pulse-slow">Admin</span>
                                @endif
                            </a>
                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-500/80 rounded-lg hover:bg-red-600 transition-all duration-300 hover:shadow-lg">
                                    Logout
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-white hover:text-gold transition-all duration-300">
                                Login
                            </a>
                            <a href="{{ route('register') }}" class="px-5 py-2 text-sm font-bold text-brand bg-white rounded-lg hover:bg-gold hover:text-black transition-all duration-300 hover:shadow-lg hover:scale-105">
                                Register
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div id="mobileMenu" class="hidden md:hidden bg-pool-felt dark:bg-neutral-800 mobile-menu-enter">
            <div class="px-4 py-3 space-y-2">
                <a href="{{ route('tournaments.index') }}" class="block px-4 py-2 text-white hover:bg-white/10 rounded-lg transition">🏆 Tournaments</a>
                <a href="{{ route('players.index') }}" class="block px-4 py-2 text-white hover:bg-white/10 rounded-lg transition">👥 Players</a>
                <a href="{{ route('rankings.index') }}" class="block px-4 py-2 text-white hover:bg-white/10 rounded-lg transition">👑 Rankings</a>
                <a href="{{ route('head-to-head.index') }}" class="block px-4 py-2 text-white hover:bg-white/10 rounded-lg transition">⚔️ Head-to-Head</a>
                <a href="{{ route('activity.index') }}" class="block px-4 py-2 text-white hover:bg-white/10 rounded-lg transition">📰 Activity</a>
                <a href="{{ route('statistics.index') }}" class="block px-4 py-2 text-white hover:bg-white/10 rounded-lg transition">📊 Statistics</a>
                <a href="{{ route('rules.index') }}" class="block px-4 py-2 text-white hover:bg-white/10 rounded-lg transition">📋 Rules</a>
                <a href="{{ route('archive.index') }}" class="block px-4 py-2 text-white hover:bg-white/10 rounded-lg transition">📚 Archive</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-white hover:bg-white/10 rounded-lg transition">⚙️ Dashboard</a>
                    <hr class="border-white/20 my-2">
                    <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-white hover:bg-white/10 rounded-lg transition">👤 My Profile</a>
                    <div class="px-4 py-2 text-white/70 text-sm">Signed in as {{ Auth::user()->name }}</div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-red-300 hover:bg-red-500/20 rounded-lg transition">Logout</button>
                    </form>
                @else
                    <hr class="border-white/20 my-2">
                    <a href="{{ route('login') }}" class="block px-4 py-2 text-white hover:bg-white/10 rounded-lg transition">Login</a>
                    <a href="{{ route('register') }}" class="block px-4 py-2 text-gold font-bold hover:bg-white/10 rounded-lg transition">Register</a>
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
                <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/30 dark:to-emerald-900/30 border-l-4 border-green-500 text-success dark:text-green-300 px-6 py-4 rounded-r-lg shadow-md animate-slide-up flex items-center gap-3" role="alert">
                    <span class="text-2xl">✅</span>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-gradient-to-r from-red-50 to-rose-50 dark:from-red-900/30 dark:to-rose-900/30 border-l-4 border-danger text-danger dark:text-red-300 px-6 py-4 rounded-r-lg shadow-md animate-slide-up flex items-center gap-3" role="alert">
                    <span class="text-2xl">❌</span>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 bg-gradient-to-r from-red-50 to-rose-50 dark:from-red-900/30 dark:to-rose-900/30 border-l-4 border-danger text-danger dark:text-red-300 px-6 py-4 rounded-r-lg shadow-md animate-slide-up" role="alert">
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
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="text-center md:text-left">
                    <div class="flex items-center justify-center md:justify-start gap-2 mb-3">
                        <span class="text-3xl">🎱</span>
                        <span class="text-xl font-bold">Theatro Pool</span>
                    </div>
                    <p class="text-white/70 text-sm">Professional tournament management for billiard halls.</p>
                </div>
                <div class="text-center">
                    <h4 class="font-semibold mb-3">Tournaments</h4>
                    <div class="space-y-2 text-sm text-white/70">
                        <a href="{{ route('tournaments.index') }}" class="block hover:text-white transition">🏆 Active</a>
                        <a href="{{ route('archive.index') }}" class="block hover:text-white transition">📚 Archive</a>
                        <a href="{{ route('rules.index') }}" class="block hover:text-white transition">📋 Rules</a>
                    </div>
                </div>
                <div class="text-center">
                    <h4 class="font-semibold mb-3">Players</h4>
                    <div class="space-y-2 text-sm text-white/70">
                        <a href="{{ route('players.index') }}" class="block hover:text-white transition">👥 All Players</a>
                        <a href="{{ route('rankings.index') }}" class="block hover:text-white transition">👑 Rankings</a>
                        <a href="{{ route('head-to-head.index') }}" class="block hover:text-white transition">⚔️ Head-to-Head</a>
                        <a href="{{ route('statistics.index') }}" class="block hover:text-white transition">📊 Statistics</a>
                    </div>
                </div>
                <div class="text-center md:text-right">
                    <h4 class="font-semibold mb-3">Stats</h4>
                    <div class="text-sm text-white/70">
                        <p>{{ $footerStats['tournaments'] ?? 0 }} Tournaments</p>
                        <p>{{ $footerStats['players'] ?? 0 }} Players</p>
                        <p>{{ $footerStats['matches'] ?? 0 }} Matches</p>
                    </div>
                    <a href="{{ route('activity.index') }}" class="inline-block mt-3 text-gold hover:text-white transition text-sm">📰 View Activity →</a>
                </div>
            </div>
            <div class="border-t border-white/20 mt-6 pt-6 text-center text-sm text-white/60">
                <p>&copy; {{ date('Y') }} Theatro Pool - Built with ❤️ for Pool Enthusiasts</p>
            </div>
        </div>
    </footer>

    <!-- Theme Manager Script -->
    <script>
        function themeManager() {
            return {
                darkMode: false,
                init() {
                    // Check localStorage first, then system preference
                    const stored = localStorage.getItem('darkMode');
                    if (stored !== null) {
                        this.darkMode = stored === 'true';
                    } else {
                        this.darkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
                    }

                    // Watch for system preference changes
                    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
                        if (localStorage.getItem('darkMode') === null) {
                            this.darkMode = e.matches;
                        }
                    });

                    // Watch darkMode changes and persist
                    this.$watch('darkMode', val => {
                        localStorage.setItem('darkMode', val);
                        // Update meta theme color
                        document.getElementById('theme-color-meta').content = val ? '#111827' : '#1a472a';
                    });
                }
            }
        }
    </script>

    @stack('scripts')
</body>
</html>
