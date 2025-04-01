<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Return a success JSON response.
     *
     * @param  array|string  $data
     */
    protected function successResponse($data, ?string $message = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message ?? __('messages.success'),
            'data' => $data,
        ], $code);
    }

    /**
     * Return an error JSON response.
     */
    protected function errorResponse(string $message, int $code, ?array $data = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($data) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    /**
     * Return a 201 JSON response.
     *
     * @param  array|string  $data
     */
    protected function createdResponse($data, ?string $message = null): JsonResponse
    {
        return $this->successResponse($data, $message ?? __('messages.created'), 201);
    }

    /**
     * Return a 204 JSON response.
     */
    protected function noContentResponse(?string $message = null): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message ?? __('messages.deleted'),
        ], 204);
    }

    /**
     * Return a 422 JSON response.
     */
    protected function validationErrorResponse(array $errors, ?string $message = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message ?? __('validation.failed'),
            'errors' => $errors,
        ], 422);
    }

    /**
     * Return a 403 JSON response.
     */
    protected function forbiddenResponse(?string $message = null): JsonResponse
    {
        return $this->errorResponse($message ?? __('auth.forbidden'), 403);
    }

    /**
     * Return a 401 JSON response.
     */
    protected function unauthorizedResponse(?string $message = null): JsonResponse
    {
        return $this->errorResponse($message ?? __('auth.unauthorized'), 401);
    }

    /**
     * Return a 404 JSON response.
     */
    protected function notFoundResponse(?string $message = null): JsonResponse
    {
        return $this->errorResponse($message ?? __('messages.not_found'), 404);
    }
}
