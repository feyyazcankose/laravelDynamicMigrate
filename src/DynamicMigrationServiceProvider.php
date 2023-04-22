<?php

namespace Feyyazcankose\LaravelDynamicMigrate;

use Feyyazcankose\LaravelDynamicMigrate\Console\Commands\DynamicMigrateCommand;
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
        if (!file_exists(base_path("app") . '/Seeders')) {
            mkdir(base_path("app") . '/Seeders', 0777, true);
        }

        $this->commands([
            DynamicMigrateCommand::class
        ]);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        // $this->loadConsole


    }
}
