<?php

namespace App\Rest;

use Lomkit\Rest\Http\Controllers\Controller as RestController;
use Illuminate\Http\Request;
use Lomkit\Rest\Http\Requests\DetailsRequest;
use Lomkit\Rest\Http\Requests\DestroyRequest;
use Lomkit\Rest\Http\Requests\MutateRequest;
use Lomkit\Rest\Http\Requests\RelationRequest;
use Lomkit\Rest\Http\Requests\SearchRequest;

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

    /**
     * Convert a standard Laravel request to a DetailsRequest
     *
     * @param Request $request
     * @return array
     */
    public function handleDetails(Request $request)
    {
        // Create a DetailsRequest instance with parameters from Request
        $detailsRequest = new DetailsRequest();
        $detailsRequest->merge($request->all());

        // Call the parent details method with the proper request type
        return $this->details($detailsRequest);
    }

    /**
     * Convert a standard Laravel request to a DestroyRequest
     *
     * @param Request $request
     * @return array
     */
    public function handleDestroy(Request $request)
    {
        // Create a DestroyRequest instance with parameters from Request
        $destroyRequest = new DestroyRequest();
        $destroyRequest->merge($request->all());

        // Call the parent destroy method with the proper request type
        return $this->destroy($destroyRequest);
    }

    /**
     * Convert a standard Laravel request to a MutateRequest
     *
     * @param Request $request
     * @return array
     */
    public function handleMutate(Request $request)
    {
        // Create a MutateRequest instance with parameters from Request
        $mutateRequest = new MutateRequest();
        $mutateRequest->merge($request->all());

        // Call the parent mutate method with the proper request type
        return $this->mutate($mutateRequest);
    }

    /**
     * Convert a standard Laravel request to a SearchRequest
     *
     * @param Request $request
     * @return array
     */
    public function handleSearch(Request $request)
    {
        // Create a SearchRequest instance with parameters from Request
        $searchRequest = new SearchRequest();
        $searchRequest->merge($request->all());

        // Call the parent search method with the proper request type
        return $this->search($searchRequest);
    }

    /**
     * Convert a standard Laravel request to a RelationRequest
     *
     * @param string $relation
     * @param Request $request
     * @return array
     */
    public function handleRelation(string $relation, Request $request)
    {
        // Create a RelationRequest instance with parameters from Request
        $relationRequest = new RelationRequest();
        $relationRequest->merge($request->all());

        // Call the parent relation method with the proper request type
        return $this->relation($relation, $relationRequest);
    }

    /**
     * Handle custom operations with standard Laravel request
     *
     * @param string $operation
     * @param Request $request
     * @return array
     */
    public function handleOperation(string $operation, Request $request)
    {
        // Call the parent operation method with the data from the request
        return $this->operation($operation, $request->all());
    }
}
