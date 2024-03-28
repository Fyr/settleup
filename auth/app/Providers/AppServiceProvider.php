<?php

namespace App\Providers;

use App\Services\AuthService;
use App\Services\AuthServiceInterface;
use App\Services\AuthTokenGuard;
use App\Services\CarrierKeyService;
use App\Services\CarrierKeyServiceInterface;
use App\Services\UserService;
use App\Services\UserServiceInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(
            AuthServiceInterface::class,
            AuthService::class
        );

        $this->app->bind(
            UserServiceInterface::class,
            UserService::class
        );

        $this->app->bind(
            CarrierKeyServiceInterface::class,
            CarrierKeyService::class
        );
    }

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(): void
    {
        Auth::extend('authToken', fn ($app, $name, array $config) => new AuthTokenGuard(
            Auth::createUserProvider($config['provider']),
            $app['request'],
            $config['input_key'],
            $config['storage_key'],
            $config['hash'],
        ));
    }
}
