<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HeadToHeadController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RankingsController;
use App\Http\Controllers\RulesController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\TournamentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application.
| These routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group.
|
*/

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Admin-only routes (require admin role) - Place BEFORE public routes with route parameters
Route::middleware(['auth', 'admin'])->group(function () {
    // Player management - MUST be before /players/{player}
    Route::get('/players/create', [PlayerController::class, 'create'])->name('players.create');
    Route::post('/players', [PlayerController::class, 'store'])->name('players.store');
    Route::get('/players/{player}/edit', [PlayerController::class, 'edit'])->name('players.edit');
    Route::put('/players/{player}', [PlayerController::class, 'update'])->name('players.update');
    Route::delete('/players/{player}', [PlayerController::class, 'destroy'])->name('players.destroy');

    // Tournament management - MUST be before /tournaments/{tournament}
    Route::get('/tournaments/create', [TournamentController::class, 'create'])->name('tournaments.create');
    Route::post('/tournaments', [TournamentController::class, 'store'])->name('tournaments.store');
    Route::get('/tournaments/{tournament}/edit', [TournamentController::class, 'edit'])->name('tournaments.edit');
    Route::put('/tournaments/{tournament}', [TournamentController::class, 'update'])->name('tournaments.update');
    Route::delete('/tournaments/{tournament}', [TournamentController::class, 'destroy'])->name('tournaments.destroy');

    // Tournament player registration (admin can register any player)
    Route::post('/tournaments/{tournament}/register-player', [TournamentController::class, 'registerPlayer'])
        ->name('tournaments.registerPlayer');
    Route::delete('/tournaments/{tournament}/unregister-player/{player}', [TournamentController::class, 'unregisterPlayer'])
        ->name('tournaments.unregisterPlayer');

    // Generate bracket
    Route::post('/tournaments/{tournament}/generate-bracket', [TournamentController::class, 'generateBracket'])
        ->name('tournaments.generateBracket');

    // Match result management
    Route::get('/matches/{match}/edit', [MatchController::class, 'edit'])->name('matches.edit');
    Route::put('/matches/{match}', [MatchController::class, 'update'])->name('matches.update');
});

// Public tournament viewing (anyone can view)
Route::get('/tournaments', [TournamentController::class, 'index'])->name('tournaments.index');
Route::get('/tournaments/{tournament}', [TournamentController::class, 'show'])->name('tournaments.show');

// Public player profiles (anyone can view)
Route::get('/players', [PlayerController::class, 'index'])->name('players.index');
Route::get('/players/{player}', [PlayerController::class, 'show'])->name('players.show');

// Public pages - Rankings, Archive, Statistics
Route::get('/rankings', [RankingsController::class, 'index'])->name('rankings.index');
Route::get('/archive', [ArchiveController::class, 'index'])->name('archive.index');
Route::get('/statistics', [StatisticsController::class, 'index'])->name('statistics.index');

// New public pages - Activity, Head-to-Head, Rules
Route::get('/activity', [ActivityController::class, 'index'])->name('activity.index');
Route::get('/head-to-head', [HeadToHeadController::class, 'index'])->name('head-to-head.index');
Route::get('/head-to-head/compare', [HeadToHeadController::class, 'compare'])->name('head-to-head.compare');
Route::get('/rules', [RulesController::class, 'index'])->name('rules.index');

// Authenticated routes (require login)
Route::middleware(['auth'])->group(function () {
    // Dashboard (all authenticated users)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Match viewing
    Route::get('/matches/{match}', [MatchController::class, 'show'])->name('matches.show');
    
    // User Profile routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/create-player', [ProfileController::class, 'createPlayer'])->name('profile.createPlayer');
    
    // Player self-registration for tournaments (players can register themselves)
    Route::post('/tournaments/{tournament}/join', [TournamentController::class, 'joinTournament'])
        ->name('tournaments.join');
    Route::post('/tournaments/{tournament}/leave', [TournamentController::class, 'leaveTournament'])
        ->name('tournaments.leave');
});
