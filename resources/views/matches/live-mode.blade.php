<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎱 LIVE: {{ $match->player1?->display_name ?? 'TBD' }} vs {{ $match->player2?->display_name ?? 'TBD' }}</title>
    <meta http-equiv="refresh" content="5">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'pool-green': '#0F5132',
                        'pool-felt': '#1A7B4C',
                        'pool-gold': '#FFD700',
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(239, 68, 68, 0.5); }
            50% { box-shadow: 0 0 40px rgba(239, 68, 68, 0.8); }
        }
        .live-indicator {
            animation: pulse-glow 1.5s ease-in-out infinite;
        }
        @keyframes score-pop {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        .score-animate {
            animation: score-pop 0.3s ease-in-out;
        }
        body {
            background: linear-gradient(135deg, #0F5132 0%, #1A7B4C 50%, #0F5132 100%);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    <!-- Header Bar -->
    <header class="bg-black/30 backdrop-blur-sm py-4 px-6">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-4">
                <span class="text-4xl">🎱</span>
                <div>
                    <h1 class="text-white font-bold text-xl">THEATRO POOL</h1>
                    <p class="text-white/70 text-sm">{{ $match->tournament->name }}</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                @if($match->isInProgress())
                <div class="flex items-center gap-2 px-4 py-2 bg-red-600 rounded-full live-indicator">
                    <span class="w-3 h-3 bg-white rounded-full animate-pulse"></span>
                    <span class="text-white font-bold text-sm">LIVE</span>
                </div>
                @elseif($match->isCompleted())
                <div class="px-4 py-2 bg-green-600 rounded-full">
                    <span class="text-white font-bold text-sm">✅ COMPLETED</span>
                </div>
                @else
                <div class="px-4 py-2 bg-yellow-600 rounded-full">
                    <span class="text-white font-bold text-sm">⏳ PENDING</span>
                </div>
                @endif
                <a href="{{ route('tournaments.show', $match->tournament) }}" class="text-white/70 hover:text-white transition">
                    ✕ Exit
                </a>
            </div>
        </div>
    </header>

    <!-- Main Scoreboard -->
    <main class="flex-1 flex items-center justify-center p-8">
        <div class="w-full max-w-5xl">
            <!-- Round Info -->
            <div class="text-center mb-8">
                <span class="inline-block px-6 py-2 bg-white/10 text-white/90 rounded-full text-lg font-medium backdrop-blur-sm">
                    {{ $match->tournament->getRoundName($match->round) }} • Match {{ $match->match_number }}
                </span>
            </div>

            <!-- Scoreboard Card -->
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
                <div class="grid grid-cols-3 divide-x divide-gray-200">
                    <!-- Player 1 -->
                    <div class="p-8 {{ $match->winner_id === $match->player1_id ? 'bg-gradient-to-br from-green-50 to-emerald-100' : '' }}">
                        <div class="text-center">
                            @if($match->winner_id === $match->player1_id)
                            <div class="text-6xl mb-4 animate-bounce">🏆</div>
                            @else
                            <div class="w-24 h-24 mx-auto bg-gradient-to-br from-brand to-brand-light rounded-full flex items-center justify-center text-white text-4xl font-bold mb-4 shadow-lg">
                                {{ $match->player1 ? strtoupper(substr($match->player1->name, 0, 1)) : '?' }}
                            </div>
                            @endif
                            <h2 class="text-2xl font-bold text-text-primary mb-2">
                                {{ $match->player1?->display_name ?? 'TBD' }}
                            </h2>
                            @if($match->player1)
                            <p class="text-text-muted text-sm">
                                {{ $match->player1->wins }}W - {{ $match->player1->losses }}L
                            </p>
                            @endif
                        </div>
                    </div>

                    <!-- Score Display -->
                    <div class="p-8 bg-gradient-to-b from-gray-50 to-gray-100 flex flex-col items-center justify-center">
                        <div class="text-7xl font-black text-text-primary flex items-center gap-4">
                            <span class="score-animate {{ $match->winner_id === $match->player1_id ? 'text-success' : '' }}">
                                {{ $match->player1_score ?? 0 }}
                            </span>
                            <span class="text-4xl text-gray-400">-</span>
                            <span class="score-animate {{ $match->winner_id === $match->player2_id ? 'text-success' : '' }}">
                                {{ $match->player2_score ?? 0 }}
                            </span>
                        </div>
                        @if($match->table_number)
                        <div class="mt-6 px-4 py-2 bg-brand/10 rounded-full">
                            <span class="text-brand font-semibold">Table {{ $match->table_number }}</span>
                        </div>
                        @endif
                        @if($match->isCompleted())
                        <div class="mt-4 text-success font-bold text-lg">
                            FINAL
                        </div>
                        @elseif($match->isInProgress())
                        <div class="mt-4 flex items-center gap-2">
                            <span class="w-2 h-2 bg-red-500 rounded-full animate-ping"></span>
                            <span class="text-red-500 font-bold">IN PROGRESS</span>
                        </div>
                        @endif
                    </div>

                    <!-- Player 2 -->
                    <div class="p-8 {{ $match->winner_id === $match->player2_id ? 'bg-gradient-to-br from-green-50 to-emerald-100' : '' }}">
                        <div class="text-center">
                            @if($match->winner_id === $match->player2_id)
                            <div class="text-6xl mb-4 animate-bounce">🏆</div>
                            @else
                            <div class="w-24 h-24 mx-auto bg-gradient-to-br from-brand to-brand-light rounded-full flex items-center justify-center text-white text-4xl font-bold mb-4 shadow-lg">
                                {{ $match->player2 ? strtoupper(substr($match->player2->name, 0, 1)) : '?' }}
                            </div>
                            @endif
                            <h2 class="text-2xl font-bold text-text-primary mb-2">
                                {{ $match->player2?->display_name ?? 'TBD' }}
                            </h2>
                            @if($match->player2)
                            <p class="text-text-muted text-sm">
                                {{ $match->player2->wins }}W - {{ $match->player2->losses }}L
                            </p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Footer Info -->
                @if($match->referee || $match->scheduled_at)
                <div class="bg-surface px-8 py-4 flex justify-center gap-8 text-sm text-text-secondary border-t">
                    @if($match->referee)
                    <span class="flex items-center gap-2">
                        <span>👤</span> Referee: {{ $match->referee->display_name }}
                    </span>
                    @endif
                    @if($match->scheduled_at)
                    <span class="flex items-center gap-2">
                        <span>📅</span> {{ $match->scheduled_at->format('M d, Y g:i A') }}
                    </span>
                    @endif
                </div>
                @endif
            </div>

            <!-- Navigation -->
            @if($match->nextMatch && $match->isCompleted())
            <div class="text-center mt-8">
                <p class="text-white/70 mb-3">Winner advances to:</p>
                <a href="{{ route('matches.liveMode', $match->nextMatch) }}"
                   class="inline-flex items-center gap-2 px-6 py-3 bg-white/20 hover:bg-white/30 text-white font-semibold rounded-xl transition backdrop-blur-sm">
                    {{ $match->tournament->getRoundName($match->nextMatch->round) }}
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            </div>
            @endif
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-black/30 backdrop-blur-sm py-4 text-center">
        <p class="text-white/50 text-sm">
            Auto-refreshing every 5 seconds • {{ now()->format('g:i:s A') }}
        </p>
    </footer>

    @auth
        @if(Auth::user()->isAdmin() && !$match->isCompleted() && $match->hasBothPlayers())
        <!-- Admin Quick Score Panel -->
        <div class="fixed bottom-24 right-8 bg-white rounded-2xl shadow-2xl p-6 max-w-sm">
            <h3 class="font-bold text-text-primary mb-4 flex items-center gap-2">
                <span>⚙️</span> Admin Controls
            </h3>
            <form action="{{ route('matches.update', $match) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs text-text-muted mb-1">{{ $match->player1->display_name }}</label>
                        <input type="number" name="player1_score" value="{{ $match->player1_score ?? 0 }}" min="0" max="100"
                               class="w-full px-3 py-2 border rounded-lg text-center font-bold text-xl">
                    </div>
                    <div>
                        <label class="block text-xs text-text-muted mb-1">{{ $match->player2->display_name }}</label>
                        <input type="number" name="player2_score" value="{{ $match->player2_score ?? 0 }}" min="0" max="100"
                               class="w-full px-3 py-2 border rounded-lg text-center font-bold text-xl">
                    </div>
                </div>
                <button type="submit" class="w-full py-3 bg-brand text-white font-bold rounded-xl hover:bg-success transition">
                    Submit Result
                </button>
            </form>
        </div>
        @endif
    @endauth
</body>
</html>
