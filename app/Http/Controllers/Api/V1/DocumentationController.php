<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Documentation",
 *     description="API Documentation endpoints"
 * )
 */
class DocumentationController extends Controller
{
    use ApiResponses;

    /**
     * Redirect to the API documentation.
     *
     * @OA\Get(
     *     path="/api/v1/docs",
     *     summary="API Documentation",
     *     description="Redirects to the Swagger UI documentation",
     *     operationId="getApiDocs",
     *     tags={"Documentation"},
     *
     *     @OA\Response(
     *         response=302,
     *         description="Redirect to documentation"
     *     )
     * )
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        return redirect('/api/documentation');
    }

    /**
     * Get basic information about the API.
     *
     * @OA\Get(
     *     path="/api/v1/docs/info",
     *     summary="API Information",
     *     description="Returns basic information about the API",
     *     operationId="getApiInfo",
     *     tags={"Documentation"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="status_code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="API information retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="name", type="string", example="Todo API"),
     *                 @OA\Property(property="version", type="string", example="1.0.0"),
     *                 @OA\Property(property="description", type="string", example="RESTful API for the Todo application"),
     *                 @OA\Property(property="base_url", type="string", example="/api/v1"),
     *                 @OA\Property(property="documentation_url", type="string", example="/api/documentation"),
     *                 @OA\Property(
     *                     property="endpoints",
     *                     type="object",
     *                     @OA\Property(property="auth", type="string", example="/api/v1/auth"),
     *                     @OA\Property(property="tasks", type="string", example="/api/v1/tasks"),
     *                     @OA\Property(property="categories", type="string", example="/api/v1/categories"),
     *                     @OA\Property(property="stats", type="string", example="/api/v1/stats")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function info(Request $request): JsonResponse
    {
        return $this->successResponse([
            'name' => 'Todo API',
            'version' => '1.0.0',
            'description' => 'RESTful API for the Todo application',
            'base_url' => '/api/v1',
            'documentation_url' => '/api/documentation',
            'endpoints' => [
                'auth' => '/api/v1/auth',
                'tasks' => '/api/v1/tasks',
                'categories' => '/api/v1/categories',
                'stats' => '/api/v1/stats',
            ],
        ], 'API information retrieved successfully');
    }
}
