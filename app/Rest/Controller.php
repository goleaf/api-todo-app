<?php

namespace App\Rest;

use Lomkit\Rest\Http\Controllers\Controller as RestController;
use Illuminate\Http\Request;

abstract class Controller extends RestController
{
    /**
     * Execute a custom operation
     *
     * @param string $operation The operation name
     * @param array $parameters The parameters for the operation
     * @return \Illuminate\Http\JsonResponse
     */
    public function operation($operation, $parameters = [])
    {
        if (method_exists($this, $operation)) {
            return $this->$operation($parameters);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Operation not found',
            'errors' => ['operation' => 'The requested operation is not supported']
        ], 404);
    }
    
    /**
     * Get related resources
     *
     * @param string $relation The relation name
     * @param array $parameters The parameters for the relation query
     * @return \Illuminate\Http\JsonResponse
     */
    public function relation($relation, $parameters = [])
    {
        $modelClass = static::$resource::$model;
        $model = $modelClass::findOrFail($parameters['id'] ?? 0);
        
        if (method_exists($model, $relation)) {
            $related = $model->$relation;
            
            return response()->json([
                'success' => true,
                'data' => $related
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Relation not found',
            'errors' => ['relation' => 'The requested relation is not supported']
        ], 404);
    }
    
    /**
     * Format responses to match the application's standard format
     *
     * @param mixed $data The data to include in the response
     * @param string $message Optional success message
     * @param int $status HTTP status code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse($data, $message = null, $status = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message ?? 'Operation completed successfully',
            'data' => $data
        ], $status);
    }
    
    /**
     * Format error responses to match the application's standard format
     *
     * @param string $message Error message
     * @param array $errors Optional validation errors
     * @param int $status HTTP status code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse($message, $errors = null, $status = 400)
    {
        $response = [
            'success' => false,
            'message' => $message
        ];
        
        if ($errors) {
            $response['errors'] = $errors;
        }
        
        return response()->json($response, $status);
    }
}
