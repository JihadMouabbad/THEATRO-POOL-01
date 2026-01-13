@extends('layouts.app')

@section('title', 'Tournament Rules')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
        <span class="w-12 h-12 bg-gradient-to-br from-pool-green to-pool-felt rounded-xl flex items-center justify-center text-white text-2xl">📋</span>
        Tournament Rules & Format
    </h1>
    <p class="text-gray-600 mt-2">Official rules and guidelines for Theatro Pool tournaments</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Rules Section -->
    <div class="lg:col-span-2 space-y-6">
        <!-- 8-Ball Rules -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-pool-green to-pool-felt p-6">
                <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                    <span class="text-3xl">🎱</span> 8-Ball Pool Rules
                </h2>
            </div>
            <div class="p-6 space-y-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <span class="w-8 h-8 bg-pool-green/10 rounded-lg flex items-center justify-center text-pool-green">1</span>
                        Objective
                    </h3>
                    <p class="text-gray-600 leading-relaxed">
                        Each player must pocket all of their designated group of balls (solids or stripes), then legally pocket the 8-ball to win the game.
                    </p>
                </div>

                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <span class="w-8 h-8 bg-pool-green/10 rounded-lg flex items-center justify-center text-pool-green">2</span>
                        The Break
                    </h3>
                    <ul class="text-gray-600 space-y-2">
                        <li class="flex items-start gap-2">
                            <span class="text-pool-green mt-1">•</span>
                            The cue ball must be placed behind the head string
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-pool-green mt-1">•</span>
                            At least 4 balls must hit cushions or a ball must be pocketed
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-pool-green mt-1">•</span>
                            If the 8-ball is pocketed on the break, the breaker may re-rack or spot the 8-ball
                        </li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <span class="w-8 h-8 bg-pool-green/10 rounded-lg flex items-center justify-center text-pool-green">3</span>
                        Legal Shots
                    </h3>
                    <ul class="text-gray-600 space-y-2">
                        <li class="flex items-start gap-2">
                            <span class="text-pool-green mt-1">•</span>
                            The cue ball must first contact a ball of the shooter's group
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-pool-green mt-1">•</span>
                            After contact, any ball must hit a cushion or be pocketed
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-pool-green mt-1">•</span>
                            Call pocket on the 8-ball shot (obvious shots don't need to be called)
                        </li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <span class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center text-red-600">!</span>
                        Fouls
                    </h3>
                    <div class="bg-red-50 rounded-xl p-4">
                        <ul class="text-gray-700 space-y-2">
                            <li class="flex items-start gap-2">
                                <span class="text-red-500 mt-1">✗</span>
                                Scratching (cue ball in pocket)
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-red-500 mt-1">✗</span>
                                Hitting opponent's ball first
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-red-500 mt-1">✗</span>
                                No rail after contact
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-red-500 mt-1">✗</span>
                                Pocketing opponent's ball
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-red-500 mt-1">✗</span>
                                Jumping the cue ball off the table
                            </li>
                        </ul>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <span class="w-8 h-8 bg-pool-gold/20 rounded-lg flex items-center justify-center text-pool-gold">🏆</span>
                        Winning the Game
                    </h3>
                    <div class="bg-green-50 rounded-xl p-4">
                        <ul class="text-gray-700 space-y-2">
                            <li class="flex items-start gap-2">
                                <span class="text-green-500 mt-1">✓</span>
                                Legally pocket all your balls then the 8-ball
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-green-500 mt-1">✓</span>
                                Opponent fouls while shooting at the 8-ball
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-green-500 mt-1">✓</span>
                                Opponent pockets the 8-ball in wrong pocket
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tournament Format -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-6">
                <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                    <span class="text-3xl">🏆</span> Tournament Format
                </h2>
            </div>
            <div class="p-6 space-y-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-3">Single Elimination</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">
                        All Theatro Pool tournaments use a single-elimination bracket format. Lose once and you're out!
                    </p>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="bg-gray-50 rounded-xl p-4 text-center">
                            <div class="text-3xl font-black text-pool-green">8</div>
                            <div class="text-sm text-gray-500">Players</div>
                            <div class="text-xs text-gray-400 mt-1">3 Rounds</div>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4 text-center">
                            <div class="text-3xl font-black text-pool-green">16</div>
                            <div class="text-sm text-gray-500">Players</div>
                            <div class="text-xs text-gray-400 mt-1">4 Rounds</div>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4 text-center">
                            <div class="text-3xl font-black text-pool-green">32</div>
                            <div class="text-sm text-gray-500">Players</div>
                            <div class="text-xs text-gray-400 mt-1">5 Rounds</div>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-3">Match Format</h3>
                    <div class="bg-blue-50 rounded-xl p-4">
                        <ul class="text-gray-700 space-y-2">
                            <li class="flex items-start gap-2">
                                <span class="text-blue-500 mt-1">•</span>
                                Race to 7 (first to 7 wins) for all matches
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-blue-500 mt-1">•</span>
                                Lag for break at the start of each match
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-blue-500 mt-1">•</span>
                                Alternate break after each game
                            </li>
                        </ul>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-3">Bracket Rounds</h3>
                    <div class="space-y-2">
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                            <span class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center font-bold text-gray-600">1</span>
                            <span class="font-medium">Round of 32 / 16 / 8</span>
                            <span class="ml-auto text-sm text-gray-500">First round</span>
                        </div>
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                            <span class="w-8 h-8 bg-blue-200 rounded-full flex items-center justify-center font-bold text-blue-600">QF</span>
                            <span class="font-medium">Quarter Finals</span>
                            <span class="ml-auto text-sm text-gray-500">Top 8</span>
                        </div>
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                            <span class="w-8 h-8 bg-purple-200 rounded-full flex items-center justify-center font-bold text-purple-600">SF</span>
                            <span class="font-medium">Semi Finals</span>
                            <span class="ml-auto text-sm text-gray-500">Top 4</span>
                        </div>
                        <div class="flex items-center gap-3 p-3 bg-pool-gold/20 rounded-lg">
                            <span class="w-8 h-8 bg-pool-gold rounded-full flex items-center justify-center font-bold text-white">F</span>
                            <span class="font-medium">Final</span>
                            <span class="ml-auto text-sm text-pool-gold font-bold">Champion</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Quick Reference -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-purple-500 to-violet-600 p-4">
                <h3 class="text-lg font-bold text-white">📌 Quick Reference</h3>
            </div>
            <div class="p-4 space-y-4">
                <div>
                    <h4 class="font-bold text-gray-800 mb-2">Ball Groups</h4>
                    <div class="flex gap-2">
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm">Solids (1-7)</span>
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">Stripes (9-15)</span>
                    </div>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800 mb-2">8-Ball</h4>
                    <p class="text-sm text-gray-600">Black ball #8 - pocket last to win</p>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800 mb-2">Cue Ball</h4>
                    <p class="text-sm text-gray-600">White ball - only ball you can hit directly</p>
                </div>
            </div>
        </div>

        <!-- Tips -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-4">
                <h3 class="text-lg font-bold text-white">💡 Pro Tips</h3>
            </div>
            <div class="p-4 space-y-3">
                <div class="flex items-start gap-3">
                    <span class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center text-green-600 flex-shrink-0 mt-0.5">1</span>
                    <p class="text-sm text-gray-600">Always think about position for your next shot</p>
                </div>
                <div class="flex items-start gap-3">
                    <span class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center text-green-600 flex-shrink-0 mt-0.5">2</span>
                    <p class="text-sm text-gray-600">Play safe when you don't have a clear shot</p>
                </div>
                <div class="flex items-start gap-3">
                    <span class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center text-green-600 flex-shrink-0 mt-0.5">3</span>
                    <p class="text-sm text-gray-600">Watch the pros play to learn patterns</p>
                </div>
                <div class="flex items-start gap-3">
                    <span class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center text-green-600 flex-shrink-0 mt-0.5">4</span>
                    <p class="text-sm text-gray-600">Practice your break - it's crucial</p>
                </div>
                <div class="flex items-start gap-3">
                    <span class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center text-green-600 flex-shrink-0 mt-0.5">5</span>
                    <p class="text-sm text-gray-600">Stay calm under pressure</p>
                </div>
            </div>
        </div>

        <!-- CTA -->
        <div class="bg-gradient-to-br from-pool-green to-pool-felt rounded-2xl p-6 text-white text-center">
            <span class="text-4xl mb-4 block">🎱</span>
            <h3 class="text-lg font-bold mb-2">Ready to Play?</h3>
            <p class="text-white/80 text-sm mb-4">Join our next tournament and show your skills!</p>
            <a href="{{ route('tournaments.index') }}" class="inline-block px-6 py-2 bg-white text-pool-green font-bold rounded-lg hover:bg-pool-gold hover:text-black transition">
                View Tournaments
            </a>
        </div>
    </div>
</div>
@endsection
