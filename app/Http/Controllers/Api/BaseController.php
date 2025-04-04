<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    /**
     * Success response method.
     *
     * @param  mixed  $data
     * @param  string|null  $message
     * @param  int  $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse($data, ?string $message = null, int $statusCode = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'data' => $data,
        ];
        
        if ($message) {
            $response['message'] = $message;
        }
        
        return response()->json($response, $statusCode);
    }

    /**
     * Error response method.
     *
     * @param  string  $message
     * @param  int  $statusCode
     * @param  mixed  $errors
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse(string $message, int $statusCode = 400, $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];
        
        if ($errors) {
            $response['errors'] = $errors;
        }
        
        return response()->json($response, $statusCode);
    }

    /**
     * Validation error response method.
     *
     * @param  array  $errors
     * @return \Illuminate\Http\JsonResponse
     */
    protected function validationErrorResponse(array $errors): JsonResponse
    {
        return $this->errorResponse('Validation failed', 422, $errors);
    }

    /**
     * Return a not found response.
     *
     * @param  string  $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function notFoundResponse(string $message = 'Resource not found'): JsonResponse
    {
        return $this->errorResponse($message, 404);
    }
    
    /**
     * Return an unauthorized response.
     *
     * @param  string  $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function unauthorizedResponse(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->errorResponse($message, 401);
    }
    
    /**
     * Return a forbidden response.
     *
     * @param  string  $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function forbiddenResponse(string $message = 'Forbidden'): JsonResponse
    {
        return $this->errorResponse($message, 403);
    }
} 