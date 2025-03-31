<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ReflectionClass;

class GenerateModelScopes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:scopes 
                            {model : The model class name}
                            {--all : Generate scopes for all properties}
                            {--fields= : Comma-separated list of fields to create scopes for}
                            {--force : Overwrite existing scopes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate scopes for a Laravel model';

    /**
     * Supported data types and their corresponding scope templates
     */
    protected $typeScopes = [
        'string' => [
            'like' => "/**\n     * Filter by {field} that matches the given pattern.\n     *\n     * @param \\Illuminate\\Database\\Eloquent\\Builder \$query\n     * @param string \$pattern\n     * @return \\Illuminate\\Database\\Eloquent\\Builder\n     */\n    public function scope{ScopeName}Like(\$query, \$pattern)\n    {\n        return \$query->where('{field}', 'like', \$pattern);\n    }",
            'exact' => "/**\n     * Filter by exact {field}.\n     *\n     * @param \\Illuminate\\Database\\Eloquent\\Builder \$query\n     * @param string \$value\n     * @return \\Illuminate\\Database\\Eloquent\\Builder\n     */\n    public function scope{ScopeName}(\$query, \$value)\n    {\n        return \$query->where('{field}', \$value);\n    }",
            'startsWith' => "/**\n     * Filter by {field} that starts with the given value.\n     *\n     * @param \\Illuminate\\Database\\Eloquent\\Builder \$query\n     * @param string \$value\n     * @return \\Illuminate\\Database\\Eloquent\\Builder\n     */\n    public function scope{ScopeName}StartsWith(\$query, \$value)\n    {\n        return \$query->where('{field}', 'like', \$value . '%');\n    }",
            'endsWith' => "/**\n     * Filter by {field} that ends with the given value.\n     *\n     * @param \\Illuminate\\Database\\Eloquent\\Builder \$query\n     * @param string \$value\n     * @return \\Illuminate\\Database\\Eloquent\\Builder\n     */\n    public function scope{ScopeName}EndsWith(\$query, \$value)\n    {\n        return \$query->where('{field}', 'like', '%' . \$value);\n    }",
            'contains' => "/**\n     * Filter by {field} that contains the given value.\n     *\n     * @param \\Illuminate\\Database\\Eloquent\\Builder \$query\n     * @param string \$value\n     * @return \\Illuminate\\Database\\Eloquent\\Builder\n     */\n    public function scope{ScopeName}Contains(\$query, \$value)\n    {\n        return \$query->where('{field}', 'like', '%' . \$value . '%');\n    }"
        ],
        'integer' => [
            'exact' => "/**\n     * Filter by exact {field}.\n     *\n     * @param \\Illuminate\\Database\\Eloquent\\Builder \$query\n     * @param int \$value\n     * @return \\Illuminate\\Database\\Eloquent\\Builder\n     */\n    public function scope{ScopeName}(\$query, \$value)\n    {\n        return \$query->where('{field}', \$value);\n    }",
            'greaterThan' => "/**\n     * Filter by {field} greater than the given value.\n     *\n     * @param \\Illuminate\\Database\\Eloquent\\Builder \$query\n     * @param int \$value\n     * @return \\Illuminate\\Database\\Eloquent\\Builder\n     */\n    public function scope{ScopeName}GreaterThan(\$query, \$value)\n    {\n        return \$query->where('{field}', '>', \$value);\n    }",
            'lessThan' => "/**\n     * Filter by {field} less than the given value.\n     *\n     * @param \\Illuminate\\Database\\Eloquent\\Builder \$query\n     * @param int \$value\n     * @return \\Illuminate\\Database\\Eloquent\\Builder\n     */\n    public function scope{ScopeName}LessThan(\$query, \$value)\n    {\n        return \$query->where('{field}', '<', \$value);\n    }",
            'between' => "/**\n     * Filter by {field} between the given values.\n     *\n     * @param \\Illuminate\\Database\\Eloquent\\Builder \$query\n     * @param int \$min\n     * @param int \$max\n     * @return \\Illuminate\\Database\\Eloquent\\Builder\n     */\n    public function scope{ScopeName}Between(\$query, \$min, \$max)\n    {\n        return \$query->whereBetween('{field}', [\$min, \$max]);\n    }"
        ],
        'boolean' => [
            'isTrue' => "/**\n     * Filter where {field} is true.\n     *\n     * @param \\Illuminate\\Database\\Eloquent\\Builder \$query\n     * @return \\Illuminate\\Database\\Eloquent\\Builder\n     */\n    public function scope{ScopeName}(\$query)\n    {\n        return \$query->where('{field}', true);\n    }",
            'isFalse' => "/**\n     * Filter where {field} is false.\n     *\n     * @param \\Illuminate\\Database\\Eloquent\\Builder \$query\n     * @return \\Illuminate\\Database\\Eloquent\\Builder\n     */\n    public function scopeNot{ScopeName}(\$query)\n    {\n        return \$query->where('{field}', false);\n    }"
        ],
        'dateTime' => [
            'before' => "/**\n     * Filter by {field} before the given date.\n     *\n     * @param \\Illuminate\\Database\\Eloquent\\Builder \$query\n     * @param string|\Carbon\Carbon \$date\n     * @return \\Illuminate\\Database\\Eloquent\\Builder\n     */\n    public function scope{ScopeName}Before(\$query, \$date)\n    {\n        return \$query->where('{field}', '<', \$date);\n    }",
            'after' => "/**\n     * Filter by {field} after the given date.\n     *\n     * @param \\Illuminate\\Database\\Eloquent\\Builder \$query\n     * @param string|\Carbon\Carbon \$date\n     * @return \\Illuminate\\Database\\Eloquent\\Builder\n     */\n    public function scope{ScopeName}After(\$query, \$date)\n    {\n        return \$query->where('{field}', '>', \$date);\n    }",
            'between' => "/**\n     * Filter by {field} between the given dates.\n     *\n     * @param \\Illuminate\\Database\\Eloquent\\Builder \$query\n     * @param string|\Carbon\Carbon \$start\n     * @param string|\Carbon\Carbon \$end\n     * @return \\Illuminate\\Database\\Eloquent\\Builder\n     */\n    public function scope{ScopeName}Between(\$query, \$start, \$end)\n    {\n        return \$query->whereBetween('{field}', [\$start, \$end]);\n    }",
            'date' => "/**\n     * Filter by {field} date (ignoring time).\n     *\n     * @param \\Illuminate\\Database\\Eloquent\\Builder \$query\n     * @param string|\Carbon\Carbon \$date\n     * @return \\Illuminate\\Database\\Eloquent\\Builder\n     */\n    public function scope{ScopeName}Date(\$query, \$date)\n    {\n        return \$query->whereDate('{field}', \$date);\n    }"
        ],
        'json' => [
            'contains' => "/**\n     * Filter by {field} that contains the given key or key/value pair.\n     *\n     * @param \\Illuminate\\Database\\Eloquent\\Builder \$query\n     * @param string \$key\n     * @param mixed|null \$value\n     * @return \\Illuminate\\Database\\Eloquent\\Builder\n     */\n    public function scope{ScopeName}Contains(\$query, \$key, \$value = null)\n    {\n        if (func_num_args() === 2) {\n            return \$query->whereJsonContains('{field}', \$key);\n        }\n        return \$query->whereJsonContains('{field}', [\$key => \$value]);\n    }"
        ],
        'default' => [
            'equal' => "/**\n     * Filter by exact {field}.\n     *\n     * @param \\Illuminate\\Database\\Eloquent\\Builder \$query\n     * @param mixed \$value\n     * @return \\Illuminate\\Database\\Eloquent\\Builder\n     */\n    public function scope{ScopeName}(\$query, \$value)\n    {\n        return \$query->where('{field}', \$value);\n    }",
            'notEqual' => "/**\n     * Filter by {field} not equal to the given value.\n     *\n     * @param \\Illuminate\\Database\\Eloquent\\Builder \$query\n     * @param mixed \$value\n     * @return \\Illuminate\\Database\\Eloquent\\Builder\n     */\n    public function scope{ScopeName}Not(\$query, \$value)\n    {\n        return \$query->where('{field}', '!=', \$value);\n    }",
            'in' => "/**\n     * Filter by {field} in the given values.\n     *\n     * @param \\Illuminate\\Database\\Eloquent\\Builder \$query\n     * @param array \$values\n     * @return \\Illuminate\\Database\\Eloquent\\Builder\n     */\n    public function scope{ScopeName}In(\$query, array \$values)\n    {\n        return \$query->whereIn('{field}', \$values);\n    }",
            'notIn' => "/**\n     * Filter by {field} not in the given values.\n     *\n     * @param \\Illuminate\\Database\\Eloquent\\Builder \$query\n     * @param array \$values\n     * @return \\Illuminate\\Database\\Eloquent\\Builder\n     */\n    public function scope{ScopeName}NotIn(\$query, array \$values)\n    {\n        return \$query->whereNotIn('{field}', \$values);\n    }",
            'null' => "/**\n     * Filter where {field} is null.\n     *\n     * @param \\Illuminate\\Database\\Eloquent\\Builder \$query\n     * @return \\Illuminate\\Database\\Eloquent\\Builder\n     */\n    public function scope{ScopeName}Null(\$query)\n    {\n        return \$query->whereNull('{field}');\n    }",
            'notNull' => "/**\n     * Filter where {field} is not null.\n     *\n     * @param \\Illuminate\\Database\\Eloquent\\Builder \$query\n     * @return \\Illuminate\\Database\\Eloquent\\Builder\n     */\n    public function scope{ScopeName}NotNull(\$query)\n    {\n        return \$query->whereNotNull('{field}');\n    }"
        ]
    ];

    /**
     * Common fields to filter out if not explicitly specified
     */
    protected $commonFields = [
        'id', 'created_at', 'updated_at', 'deleted_at', 'remember_token', 'password'
    ];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $modelName = $this->argument('model');
        $force = $this->option('force');
        $all = $this->option('all');
        $customFields = $this->option('fields');

        // Get full model class name
        $modelClass = $this->getModelClass($modelName);
        if (!$modelClass) {
            $this->error("Model {$modelName} not found!");
            return 1;
        }

        // Get model fields
        $fields = $this->getModelFields($modelClass);
        
        // Filter fields if needed
        if (!$all && !$customFields) {
            $fields = array_diff($fields, $this->commonFields);
        } elseif ($customFields) {
            $customFields = array_map('trim', explode(',', $customFields));
            $fields = array_intersect($fields, $customFields);
        }

        // If we have no fields after filtering, warn the user
        if (empty($fields)) {
            $this->warn("No fields to generate scopes for. Use --all to include common fields or --fields to specify custom fields.");
            return 1;
        }

        // Create a ScopeTrait for the model
        $this->createScopeTrait($modelClass, $fields, $force);

        return 0;
    }

    /**
     * Get the model class with namespace
     *
     * @param string $modelName
     * @return string|null
     */
    protected function getModelClass($modelName)
    {
        // Try to find the model in common locations
        $possibleLocations = [
            "App\\Models\\{$modelName}",
            "App\\{$modelName}",
        ];

        foreach ($possibleLocations as $class) {
            if (class_exists($class)) {
                return $class;
            }
        }

        return null;
    }

    /**
     * Get the model's database fields
     *
     * @param string $modelClass
     * @return array
     */
    protected function getModelFields($modelClass)
    {
        try {
            // Create an instance of the model
            $model = new $modelClass;
            
            // Try to get the fillable fields first
            if (property_exists($model, 'fillable') && is_array($model->getFillable())) {
                $fields = $model->getFillable();
                // Add timestamps if they exist
                if ($model->timestamps) {
                    $fields = array_merge($fields, ['created_at', 'updated_at']);
                }
                // Add soft deletes if the model uses it
                $traits = class_uses_recursive($model);
                if (in_array('Illuminate\\Database\\Eloquent\\SoftDeletes', $traits)) {
                    $fields[] = 'deleted_at';
                }
                if (property_exists($model, 'primaryKey')) {
                    $fields[] = $model->getKeyName();
                } else {
                    $fields[] = 'id';
                }
                return array_unique($fields);
            }
            
            // If fillable is not available, try to get fields from database
            if (method_exists($model, 'getConnection')) {
                $table = $model->getTable();
                $schema = $model->getConnection()->getSchemaBuilder();
                if (method_exists($schema, 'getColumnListing')) {
                    return $schema->getColumnListing($table);
                }
            }
        } catch (\Exception $e) {
            $this->error("Error getting model fields: " . $e->getMessage());
        }
        
        // If all else fails, just return common fields
        return ['id', 'name', 'title', 'description', 'created_at', 'updated_at', 'deleted_at'];
    }

    /**
     * Determine the data type of a field
     *
     * @param string $field
     * @return string
     */
    protected function determineFieldType($field)
    {
        // Check field name to guess type
        if (in_array($field, ['created_at', 'updated_at', 'deleted_at', 'published_at'])) {
            return 'dateTime';
        }
        
        if (Str::endsWith($field, ['_at', '_date'])) {
            return 'dateTime';
        }
        
        if (in_array($field, ['is_active', 'is_published', 'active', 'published', 'enabled', 'verified'])) {
            return 'boolean';
        }
        
        if (in_array($field, ['id', 'count', 'position', 'order', 'price', 'amount', 'quantity']) || 
            Str::endsWith($field, ['_id', '_count', '_position', '_order', '_price', '_amount', '_quantity'])) {
            return 'integer';
        }
        
        if (Str::contains($field, ['json', 'data', 'meta', 'properties', 'attributes', 'options', 'settings'])) {
            return 'json';
        }
        
        // Default to string for most fields
        return 'string';
    }

    /**
     * Create a scopable trait for the model
     *
     * @param string $modelClass
     * @param array $fields
     * @param bool $force
     * @return void
     */
    protected function createScopeTrait($modelClass, $fields, $force)
    {
        // Get model basename
        $reflection = new ReflectionClass($modelClass);
        $modelBaseName = $reflection->getShortName();
        
        // Create trait name and file path
        $traitName = "{$modelBaseName}Scopes";
        $namespace = $reflection->getNamespaceName() . "\\Scopes";
        $path = app_path(str_replace('\\', '/', str_replace('App\\', '', $namespace)));
        $filePath = "{$path}/{$traitName}.php";
        
        // Check if file exists and force option is not used
        if (File::exists($filePath) && !$force && !$this->confirm("The scope trait {$traitName} already exists. Do you want to overwrite it?")) {
                $this->info("Operation cancelled.");
                return;
            
        }
        
        // Make sure the directory exists
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }
        
        // Generate scope methods
        $scopeMethods = $this->generateScopeMethods($fields);
        
        // Build trait content
        $content = "<?php\n\nnamespace {$namespace};\n\ntrait {$traitName}\n{\n";
        $content .= $scopeMethods;
        $content .= "}\n";
        
        // Write the file
        File::put($filePath, $content);
        
        $this->info("Created scope trait: {$namespace}\\{$traitName}");
        
        // Remind user to add the trait to the model
        $this->comment("Don't forget to add the trait to your model:");
        $this->comment("use {$namespace}\\{$traitName};");
        $this->comment("class {$modelBaseName} extends Model");
        $this->comment("{");
        $this->comment("    use {$traitName};");
        $this->comment("    // ...");
        $this->comment("}");
    }

    /**
     * Generate scope methods for fields
     *
     * @param array $fields
     * @return string
     */
    protected function generateScopeMethods($fields)
    {
        $methods = [];
        
        foreach ($fields as $field) {
            $fieldType = $this->determineFieldType($field);
            $scopeName = Str::studly($field);
            
            // Get scopes for this field type
            $scopes = $this->typeScopes[$fieldType] ?? $this->typeScopes['default'];
            
            foreach ($scopes as $scopeMethod) {
                $method = str_replace(
                    ['{field}', '{ScopeName}'],
                    [$field, $scopeName],
                    $scopeMethod
                );
                
                $methods[] = "    " . $method;
            }
        }
        
        return implode("\n\n", $methods);
    }
} 