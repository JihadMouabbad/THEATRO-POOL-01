<?php

namespace App\Providers;

use App\Models\Player;
use App\Models\PoolMatch;
use App\Models\Tournament;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Bind 'match' route parameter to PoolMatch model
        Route::model('match', PoolMatch::class);

        // Share footer statistics across all views using view composer
        View::composer('layouts.app', function ($view) {
            $view->with('footerStats', [
                'tournaments' => Tournament::count(),
                'players' => Player::count(),
                'matches' => PoolMatch::where('status', PoolMatch::STATUS_COMPLETED)->count(),
            ]);
        });
    }
}
