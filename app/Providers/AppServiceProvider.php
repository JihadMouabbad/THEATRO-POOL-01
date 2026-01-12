<?php

namespace App\Providers;

use App\Models\PoolMatch;
use Illuminate\Support\Facades\Route;
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
    }
}
