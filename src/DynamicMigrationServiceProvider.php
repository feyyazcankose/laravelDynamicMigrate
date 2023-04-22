<?php

namespace Feyyazcankose\LaravelDynamicMigrate;

use Illuminate\Support\ServiceProvider;

class DynamicMigrationServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        // $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
    }
}