<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     title="Todo API",
 *     version="1.0.0",
 *     description="API documentation for Todo application",
 *
 *     @OA\Contact(
 *         email="admin@todo.prus.dev",
 *         name="API Support"
 *     ),
 *
 *     @OA\License(
 *         name="Apache 2.0",
 *         url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *     )
 * )
 *
 * @OA\Server(
 *     url="/",
 *     description="API Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="apiKey",
 *     name="Authorization",
 *     in="header",
 *     description="Enter token in format: Bearer {your-token}"
 * )
 *
 * @OA\Tag(
 *    name="Authentication",
 *    description="API Endpoints for user authentication"
 * )
 * @OA\Tag(
 *    name="Tasks",
 *    description="API Endpoints for managing tasks"
 * )
 * @OA\Tag(
 *    name="Categories",
 *    description="API Endpoints for managing categories"
 * )
 * @OA\Tag(
 *    name="Users",
 *    description="API Endpoints for user management"
 * )
 * @OA\Tag(
 *    name="Profile",
 *    description="API Endpoints for user profile management"
 * )
 * @OA\Tag(
 *    name="Statistics",
 *    description="API Endpoints for task statistics"
 * )
 * @OA\Tag(
 *    name="Documentation",
 *    description="API Documentation endpoints"
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
