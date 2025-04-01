<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Spatie\ModelInfo\ModelFinder;
use Spatie\ModelInfo\ModelInfo;

class ModelInfoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'model:info
                            {model? : The model class name (optional, if not provided all models will be listed)}
                            {--attributes : Show model attributes}
                            {--relations : Show model relations}
                            {--table-only : Show only model table names}
                            {--detail : Show detailed information including attributes and relations}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display information about Laravel models';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $modelName = $this->argument('model');
        
        if ($modelName) {
            $this->showModelInfo($modelName);
        } else {
            $this->listAllModels();
        }

        return 0;
    }

    /**
     * List all models in the application.
     *
     * @return void
     */
    protected function listAllModels(): void
    {
        $models = ModelFinder::all();
        
        if ($models->isEmpty()) {
            $this->info('No models found in the application.');
            return;
        }

        if ($this->option('table-only')) {
            $this->listModelTables($models);
            return;
        }

        $this->info("Found " . $models->count() . " models:");
        
        $headers = ['Model', 'Table', 'File Path'];
        $rows = $models->map(function (string $modelClass) {
            try {
                $modelInfo = ModelInfo::forModel($modelClass);
                return [
                    'model' => class_basename($modelClass),
                    'table' => $modelInfo->tableName ?? 'Unknown',
                    'path' => $modelInfo->fileName ?? 'Unknown',
                ];
            } catch (\Exception $e) {
                return [
                    'model' => class_basename($modelClass),
                    'table' => 'Error',
                    'path' => 'Error: ' . $e->getMessage(),
                ];
            }
        })->toArray();

        $this->table($headers, $rows);
    }

    /**
     * List all model tables in the application.
     *
     * @param Collection $models
     * @return void
     */
    protected function listModelTables(Collection $models): void
    {
        $headers = ['Model', 'Table'];
        $rows = $models->map(function (string $modelClass) {
            try {
                $modelInfo = ModelInfo::forModel($modelClass);
                return [
                    'model' => class_basename($modelClass),
                    'table' => $modelInfo->tableName ?? 'Unknown',
                ];
            } catch (\Exception $e) {
                return [
                    'model' => class_basename($modelClass),
                    'table' => 'Error: ' . $e->getMessage(),
                ];
            }
        })->toArray();

        $this->table($headers, $rows);
    }

    /**
     * Show detailed information for a specific model.
     *
     * @param string $modelName
     * @return void
     */
    protected function showModelInfo(string $modelName): void
    {
        // Try to resolve the full class name
        $modelClass = $this->resolveModelClass($modelName);
        
        if (!$modelClass) {
            $this->error("Model '{$modelName}' not found!");
            return;
        }

        try {
            $modelInfo = ModelInfo::forModel($modelClass);
            
            $this->info("Model Information: {$modelClass}");
            $this->line("Table: {$modelInfo->tableName}");
            $this->line("File Path: {$modelInfo->fileName}");

            // Show attributes if requested or if detailed view is enabled
            if ($this->option('attributes') || $this->option('detail')) {
                $this->showAttributes($modelInfo);
            }

            // Show relations if requested or if detailed view is enabled
            if ($this->option('relations') || $this->option('detail')) {
                $this->showRelations($modelInfo);
            }
        } catch (\Exception $e) {
            $this->error("Error retrieving model information: {$e->getMessage()}");
        }
    }

    /**
     * Show model attributes.
     *
     * @param ModelInfo $modelInfo
     * @return void
     */
    protected function showAttributes(ModelInfo $modelInfo): void
    {
        $this->newLine();
        $this->info('Attributes:');
        
        if ($modelInfo->attributes->isEmpty()) {
            $this->line('  No attributes found');
            return;
        }

        $headers = ['Name', 'Type', 'Cast'];
        $rows = $modelInfo->attributes->map(function ($attribute) {
            return [
                'name' => $attribute->name,
                'type' => $attribute->type ?? 'Unknown',
                'cast' => $attribute->cast ?? 'None',
            ];
        })->toArray();

        $this->table($headers, $rows);
    }

    /**
     * Show model relations.
     *
     * @param ModelInfo $modelInfo
     * @return void
     */
    protected function showRelations(ModelInfo $modelInfo): void
    {
        $this->newLine();
        $this->info('Relations:');
        
        if ($modelInfo->relations->isEmpty()) {
            $this->line('  No relations found');
            return;
        }

        $headers = ['Name', 'Type', 'Related Model'];
        $rows = $modelInfo->relations->map(function ($relation) {
            return [
                'name' => $relation->name,
                'type' => $relation->type,
                'related' => $relation->related,
            ];
        })->toArray();

        $this->table($headers, $rows);
    }

    /**
     * Resolve the full model class name.
     *
     * @param string $name
     * @return string|null
     */
    protected function resolveModelClass(string $name): ?string
    {
        // Check if the name already contains a namespace
        if (class_exists($name)) {
            return $name;
        }

        // Common model locations
        $possibleNamespaces = [
            "App\\Models\\{$name}",
            "App\\{$name}",
        ];

        foreach ($possibleNamespaces as $class) {
            if (class_exists($class)) {
                return $class;
            }
        }

        // Try to find it from all models
        $models = ModelFinder::all();
        
        foreach ($models as $model) {
            if (class_basename($model) === $name) {
                return $model;
            }
        }

        return null;
    }
} 