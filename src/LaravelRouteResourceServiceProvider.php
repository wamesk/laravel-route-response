<?php

namespace Wame\LaravelRouteResource;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class LaravelRouteResourceServiceProvider extends ServiceProvider
{
    public function register()
    {
        /// Publish assets
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/wame-route.php' => config_path('wame-route.php'),
            ], 'config');

        }

        /// Load config
        $this->mergeConfigFrom(__DIR__.'/../config/wame-route.php', 'wame-route');

        /// Register routes
        $this->registerRoutes();
    }

    public function boot()
    {
        //
    }

    /**
     * @return void
     */
    protected function registerRoutes(): void
    {
        Route::group($this->routeConfiguration(), fn() => $this->loadRoutesFrom(__DIR__.'/../routes/api.php'));
    }

    /**
     * @return array
     */
    protected function routeConfiguration(): array
    {
        return [];
    }
}
