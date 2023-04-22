<?php

namespace App\Console\Commands;

use BadMethodCallException;
use Illuminate\Console\Command;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Str;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Illuminate\Support\Facades\Schema;

class DynamicController extends Command
{
    use Macroable;
    protected $signature = 'dynamic:controller {name} {table} {content}';
    protected $description = 'Create a dynamic controller';
    protected $_fieldHidden = [
        "created_at",
        "deleted_at",
        "updated_at",
        "meta_title",
        "meta_description",
        'id'
    ];

    public function handle()
    {
        $controllerName = $this->argument('name');
        $table = $this->argument('table');
        $model = Str::studly(strtolower(Str::singular($table)));
        $content = $this->argument('content');
        $base = strtolower($model);
        $stubFile = "controller.dynamic.content.sub";
        $pluralValue = ucfirst($table);
        $baseColumns = [];
        $contentColumns = [];
        $contentGetString = [];
        $baseTableFields = $this->getFields($table);

        $single = $model;
        if ($content == 'true') {
            $relationColumn = $base . '_id';
            $_tableContent = $base . '_contents';
        } else {
            $relationColumn = "";
            $_tableContent = "";
            $stubFile = "controller.dynamic.sub";
        }


        $operationChoices = [
            'create', 'edit', 'destroy'
        ];

        $operations = $this->askWithChoices('Select list operations:', $operationChoices, null, true);
        $this->info('Selected list operations: ' . implode(', ', $operations));

        foreach ($operations as $key => $column) {
            $operations[$key] = "'" . $column . "'";
        }

        // Column Choices
        $columnChoices = $baseTableFields;
        $baseColumns = $this->askWithChoices('List columns:', $columnChoices, null, true);
        $this->info('Selected List Base Columns: ' . implode(', ', $baseColumns));


        foreach ($baseColumns as $key => $column) {
            $baseColumnsString[] = "'$column' => " . "'" . $this->ask('Label (' . $column . '):') . "'";
        }

        if ($content == 'true') {
            $contentTableFields = $this->getFields($_tableContent);

            // Column Choices
            $columnChoices = $contentTableFields;
            $contentColumns = $this->askWithChoices('List columns:', $columnChoices, null, true);
            $this->info('Selected List Content Columns: ' . implode(', ', $contentColumns));

            $changeColumn = [];

            foreach ($contentColumns as $key => $column) {
                $contentColumnsString[] = "'$column' => " . "'" . $this->ask('Label (' . $column . '):') . "'";
                $contentGetString[] =   "'" . $_tableContent . "'.'" . $column . "'";
            }

            $fieldsDync = [...$baseColumnsString, ...$contentColumnsString];
        } else {
            $fieldsDync = $baseColumnsString;
        }

        $stub = file_get_contents(getcwd() . "/stubs/" . $stubFile);
        $controllerPath = app_path("Http/Controllers/{$controllerName}.php");

        $stub = str_replace(
            ['singleValue', 'relationColumnValue', 'tableContentValue', "relationColumnValue", "pluralValue", "prefixValue", "viewFolderValue", "tableValue", "fieldsDync", "operationsDync", "contentGetString"],
            ["$single", "$relationColumn", "$_tableContent", "$relationColumn", "$pluralValue", $base, $model, "$table", implode(', ', $fieldsDync), implode(', ', $operations), implode(', ', $contentGetString)],
            $stub
        );

        $stub = str_replace(
            ['{{ namespace }}', '{{ rootNamespace }}', '{{ class }}'],
            ["App" . explode("\App", explode(".", $this->getNamespace($controllerPath))[0])[1], 'App\\', $controllerName],
            $stub
        );

        file_put_contents($controllerPath, $stub);
        $this->info("Controller {$controllerName} created successfully!");
        $this->info("Wait for blade files created...");

        // $this->createBlade($model);

        $this->info("Blade {$model} created successfully!");
    }

    protected function getNamespace($name)
    {
        $namespace = trim(config('app.namespace'), '\\');

        if (!str_contains($name, '/')) {
            return $namespace . '\\Http\\Controllers';
        }

        $segments = explode('/', $name);

        foreach ($segments as $key => $segment) {
            $segments[$key] = str_replace('-', '_', ucwords($segment, '-'));
        }

        $name = implode('\\', $segments);

        if (str_starts_with($name, '\\')) {
            return $name;
        }

        return $namespace . '\\' . $name;
    }

    /**
     * Display a multiple-choice question to the user and ask for their selection(s).
     *
     * @param string $question
     * @param array $choices
     * @param mixed $default
     * @param bool $allowMultiple
     * @return array
     */
    public function askWithChoices($question, $choices, $default = null, $allowMultiple = false)
    {
        if (!is_array($choices) || empty($choices)) {
            throw new \InvalidArgumentException('Choices must be a non-empty array.');
        }

        $question = $question . ' [' . implode('/', $choices) . ']';

        if ($allowMultiple) {
            $selected = $this->choice($question, $choices, $default, null, true);

            return is_array($selected) ? $selected : [$selected];
        }

        $selected = $this->choice($question, $choices, $default);

        return [$selected];
    }


    public function getModel($tableName)
    {
        return new ('\App' . '\Models\\' . Str::studly(strtolower(Str::singular($tableName))));
    }

    public function getFields($table)
    {
        $columns = Schema::getColumnListing($table);
        $columnField = [];
        foreach ($columns as $key => $field) {
            if (!in_array($field, $this->_fieldHidden))
                $columnField[] = $field;
        }

        return $columnField;
    }

}
