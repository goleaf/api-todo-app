<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponses
{
    /**
     * Success response with data
     */
    public function successResponse(mixed $data = null, ?string $message = null, int $statusCode = 200, ?array $meta = null): JsonResponse
    {
        $response = [
            'success' => true,
            'status_code' => $statusCode,
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

        return response()->json($response, $statusCode);
    }

    /**
     * Success response with paginated data
     */
    public function paginatedResponse(
        LengthAwarePaginator $paginator,
        ?string $message = null,
        int $statusCode = 200
    ): JsonResponse {
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
            statusCode: $statusCode,
            meta: $meta
        );
    }

    /**
     * Error response
     */
    public function errorResponse(string $message, int $statusCode, ?array $errors = null, mixed $data = null): JsonResponse
    {
        $response = [
            'success' => false,
            'status_code' => $statusCode,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Not found response
     */
    public function notFoundResponse(string $message = 'Resource not found', ?array $errors = null): JsonResponse
    {
        return $this->errorResponse($message, 404, $errors);
    }

    /**
     * Unauthorized response
     */
    public function unauthorizedResponse(string $message = 'Unauthorized', ?array $errors = null): JsonResponse
    {
        return $this->errorResponse($message, 401, $errors);
    }

    /**
     * Forbidden response
     */
    public function forbiddenResponse(string $message = 'Forbidden', ?array $errors = null): JsonResponse
    {
        return $this->errorResponse($message, 403, $errors);
    }

    /**
     * Bad request response
     */
    public function badRequestResponse(string $message = 'Bad request', ?array $errors = null): JsonResponse
    {
        return $this->errorResponse($message, 400, $errors);
    }

    /**
     * Validation error response
     */
    public function validationErrorResponse(array $errors, ?string $message = null): JsonResponse
    {
        return $this->errorResponse(
            message: $message ?? 'Validation failed',
            statusCode: 422, 
            errors: $errors
        );
    }

    /**
     * Server error response
     */
    public function serverErrorResponse(string $message = 'Server error', ?array $errors = null, ?\Exception $exception = null): JsonResponse
    {
        if ($exception && app()->environment('local', 'development', 'testing')) {
            $errors = $errors ?? [];
            $errors['exception'] = [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ];
        }

        return $this->errorResponse($message, 500, $errors);
    }

    /**
     * Created success response
     */
    public function createdResponse(mixed $data = null, string $message = 'Resource created successfully'): JsonResponse
    {
        return $this->successResponse($data, $message, 201);
    }

    /**
     * No content response
     */
    public function noContentResponse(string $message = 'No content'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'status_code' => 204,
            'message' => $message,
            'data' => null,
        ], 204);
    }
}
