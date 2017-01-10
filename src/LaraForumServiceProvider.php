<?php

namespace FsFlex\LaraForum;

use Illuminate\Support\ServiceProvider;

class LaraForumServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        include __DIR__ . '/routers.php';
        $this->loadViewsFrom(__DIR__ . '/Views', 'forum');
        $this->loadMigrationsFrom(__DIR__.'/Migrations');
        $this->mergeConfigFrom(__DIR__.'/resources/config/laraforum.php','config');

        //$this->publishes([__DIR__.'Views/'=>resource_path('views/laraforum/')]);
        $this->publishes([__DIR__.'/resources/public'=>public_path('forum')],'public');
        $this->publishes([
            __DIR__.'/resources/config/laraforum.php' => config_path('laraforum.php')
        ], 'config');

    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app['laraforum'] = $this->app->share(function ($app) {
            return new LaraForum;
        });
    }
}