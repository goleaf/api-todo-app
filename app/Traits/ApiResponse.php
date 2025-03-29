<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Send success response with data
     *
     * @param mixed $data
     * @param string|null $message
     * @param int $code
     * @param array|null $meta
     * @return JsonResponse
     */
    protected function successResponse($data = null, string $message = null, int $code = 200, array $meta = null): JsonResponse
    {
        $response = [
            'success' => true,
            'status_code' => $code
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        if ($message !== null) {
            $response['message'] = $message;
        }
        
        if ($meta !== null) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $code);
    }

    /**
     * Send error response
     *
     * @param string $message
     * @param int $code
     * @param array|null $errors
     * @param mixed $data
     * @return JsonResponse
     */
    protected function errorResponse(string $message, int $code, array $errors = null, $data = null): JsonResponse
    {
        $response = [
            'success' => false,
            'status_code' => $code,
            'message' => $message
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }
        
        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    /**
     * Send validation error response
     *
     * @param array $errors
     * @param string|null $message
     * @return JsonResponse
     */
    protected function validationErrorResponse(array $errors, string $message = null): JsonResponse
    {
        return $this->errorResponse(
            message: $message ?? 'The given data was invalid.',
            code: 422,
            errors: $errors
        );
    }

    /**
     * Send unauthorized response
     *
     * @param string $message
     * @param array|null $errors
     * @return JsonResponse
     */
    protected function unauthorizedResponse(string $message = 'Unauthorized', array $errors = null): JsonResponse
    {
        return $this->errorResponse($message, 401, $errors);
    }

    /**
     * Send forbidden response
     *
     * @param string $message
     * @param array|null $errors
     * @return JsonResponse
     */
    protected function forbiddenResponse(string $message = 'Forbidden', array $errors = null): JsonResponse
    {
        return $this->errorResponse($message, 403, $errors);
    }

    /**
     * Send not found response
     *
     * @param string $message
     * @param array|null $errors
     * @return JsonResponse
     */
    protected function notFoundResponse(string $message = 'Resource not found', array $errors = null): JsonResponse
    {
        return $this->errorResponse($message, 404, $errors);
    }

    /**
     * Send server error response
     *
     * @param string $message
     * @param array|null $errors
     * @param \Exception|null $exception
     * @return JsonResponse
     */
    protected function serverErrorResponse(string $message = 'Server error', array $errors = null, \Exception $exception = null): JsonResponse
    {
        if ($exception && app()->environment('local', 'development', 'testing')) {
            $errors = $errors ?? [];
            $errors['exception'] = [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString()
            ];
        }
        
        return $this->errorResponse($message, 500, $errors);
    }
    
    /**
     * Send a paginated response with data
     *
     * @param \Illuminate\Pagination\LengthAwarePaginator $paginator
     * @param string|null $message
     * @param int $code
     * @return JsonResponse
     */
    protected function paginatedResponse($paginator, string $message = null, int $code = 200): JsonResponse
    {
        $meta = [
            'pagination' => [
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
                'path' => $paginator->path(),
            ],
        ];
        
        if ($paginator->hasPages()) {
            $meta['pagination']['links'] = [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ];
        }
        
        return $this->successResponse(
            data: $paginator->items(),
            message: $message,
            code: $code,
            meta: $meta
        );
    }
} 