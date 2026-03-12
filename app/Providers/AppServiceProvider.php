<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Providers\CustomUserProvider;
use Illuminate\Support\Facades\Hash;

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
        // Registrar el Custom User Provider
        Auth::provider('custom_eloquent', function ($app, array $config) {
            return new CustomUserProvider($app['hash'], $config['model']);
        });
    }
}
