<?php

namespace Feyyazcankose\LaravelDynamicMigrate\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\File;

class DynamicMigrateCommand extends Command
{
    protected $signature = 'dynamic:migrate';
    protected $description = 'Dynamically create or update database tables based on Eloquent models.';

    public function handle()
    {
        $models = self::getModels();
        foreach ($models as $key => $modelString) {
            $model = self::getModel($modelString);
            if (@$model->requireds) {
                foreach ($model->requireds as $requiredKey => $required) {
                    $requiredModel = self::getModel($required);
                    $this->dynamicMigrate($requiredModel);
                }
            }
            $this->dynamicMigrate($model);
        }

        $this->info('Dynamic migration completed.');
        $seeders = self::getSeeders();
        foreach ($seeders as $key => $value) {
            $seeder = self::getSeeder($value);
            $this->info($seeder->seed());
        }

        $this->info('Dynamic seeder completed.');
    }

    public function dynamicMigrate($model)
    {
        if (!Schema::hasTable($model->getTable())) {
            if (method_exists($model, "setColumns")) {
                Schema::create($model->getTable(), function (Blueprint $table) use ($model) {
                    $model->setColumns($table);
                });
                $this->info('Table created successfully: ' . $model->getTable());
            }
            else
                $this->error('This model '.$model->getTable().' setColumns method not found');
        } else if (method_exists($model, "updateColumns")) {
            Schema::table($model->getTable(), function (Blueprint $table) use ($model) {
                $model->updateColumns($table);
            });
            $this->info('Table updated successfully: ' . $model->getTable());
        } else {
            $this->warn('Table already exists: ' . $model->getTable());
        }
    }

    public static function getModels()
    {
        $models = array();
        $path = app_path('Models');
        $files = File::allFiles($path);
        foreach ($files as $file) {
            $model = basename($file, '.php');
            array_push($models, $model);
        }
        return $models;
    }

    public static function getSeeders()
    {
        $models = array();
        $path = app_path('Seeders');
        $files = File::allFiles($path);
        foreach ($files as $file) {
            $model = basename($file, '.php');
            array_push($models, $model);
        }
        return $models;
    }

    public static function getModel($tableName)
    {
        return new ('\App' . '\Models\\' . $tableName);
    }

    public static function getSeeder($seederName)
    {
        return new ('\App' . '\Seeders\\' . $seederName);
    }
}
