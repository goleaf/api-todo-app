<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Spatie\ModelInfo\ModelFinder;
use Spatie\ModelInfo\ModelInfo;

class ModelInfoController extends Controller
{
    /**
     * List all models in the application.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $models = ModelFinder::all();
            
            $data = $models->map(function (string $modelClass) {
                try {
                    $modelInfo = ModelInfo::forModel($modelClass);
                    return [
                        'name' => class_basename($modelClass),
                        'class' => $modelClass,
                        'table' => $modelInfo->tableName ?? null,
                        'file_path' => $modelInfo->fileName ?? null,
                        'attribute_count' => $modelInfo->attributes->count(),
                        'relation_count' => $modelInfo->relations->count(),
                    ];
                } catch (\Throwable $e) {
                    return [
                        'name' => class_basename($modelClass),
                        'class' => $modelClass,
                        'error' => $e->getMessage(),
                    ];
                }
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Models retrieved successfully',
                'data' => [
                    'count' => $data->count(),
                    'models' => $data,
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve models',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get detailed information about a specific model.
     *
     * @param string $model
     * @return JsonResponse
     */
    public function show(string $model): JsonResponse
    {
        try {
            // Try to resolve the model class from the provided name
            $modelClass = $this->resolveModelClass($model);
            
            if (!$modelClass) {
                return response()->json([
                    'success' => false,
                    'message' => "Model '{$model}' not found",
                ], 404);
            }
            
            $modelInfo = ModelInfo::forModel($modelClass);
            
            $data = [
                'name' => class_basename($modelClass),
                'class' => $modelClass,
                'table' => $modelInfo->tableName,
                'file_path' => $modelInfo->fileName,
                'attributes' => $modelInfo->attributes->map(function ($attribute) {
                    return [
                        'name' => $attribute->name,
                        'type' => $attribute->type,
                        'cast' => $attribute->cast,
                    ];
                })->values(),
                'relations' => $modelInfo->relations->map(function ($relation) {
                    return [
                        'name' => $relation->name,
                        'type' => $relation->type,
                        'related' => $relation->related,
                    ];
                })->values(),
            ];
            
            return response()->json([
                'success' => true,
                'message' => 'Model information retrieved successfully',
                'data' => $data,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve model information',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all model attributes.
     *
     * @param string $model
     * @return JsonResponse
     */
    public function attributes(string $model): JsonResponse
    {
        try {
            $modelClass = $this->resolveModelClass($model);
            
            if (!$modelClass) {
                return response()->json([
                    'success' => false,
                    'message' => "Model '{$model}' not found",
                ], 404);
            }
            
            $modelInfo = ModelInfo::forModel($modelClass);
            
            $data = $modelInfo->attributes->map(function ($attribute) {
                return [
                    'name' => $attribute->name,
                    'type' => $attribute->type,
                    'cast' => $attribute->cast,
                ];
            })->values();
            
            return response()->json([
                'success' => true,
                'message' => 'Model attributes retrieved successfully',
                'data' => [
                    'model' => class_basename($modelClass),
                    'attributes' => $data,
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve model attributes',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all model relations.
     *
     * @param string $model
     * @return JsonResponse
     */
    public function relations(string $model): JsonResponse
    {
        try {
            $modelClass = $this->resolveModelClass($model);
            
            if (!$modelClass) {
                return response()->json([
                    'success' => false,
                    'message' => "Model '{$model}' not found",
                ], 404);
            }
            
            $modelInfo = ModelInfo::forModel($modelClass);
            
            $data = $modelInfo->relations->map(function ($relation) {
                return [
                    'name' => $relation->name,
                    'type' => $relation->type,
                    'related' => $relation->related,
                ];
            })->values();
            
            return response()->json([
                'success' => true,
                'message' => 'Model relations retrieved successfully',
                'data' => [
                    'model' => class_basename($modelClass),
                    'relations' => $data,
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve model relations',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Resolve the model class from a name.
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