<?php

use App\Http\Controllers\Api\V1\AuthApiController;
use App\Http\Controllers\Api\V1\CategoryApiController;
use App\Http\Controllers\Api\V1\DashboardApiController;
use App\Http\Controllers\Api\V1\DeviceTokenController;
use App\Http\Controllers\Api\V1\DocumentationController;
use App\Http\Controllers\Api\V1\ProfileApiController;
use App\Http\Controllers\Api\V1\TaskApiController;
use App\Http\Controllers\Api\V1\UserApiController;
use App\Services\OneSignalNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Swagger Documentation JSON
Route::get('/docs/api-docs.json', function () {
    $filePath = storage_path('api-docs/api-docs.json');
    if (File::exists($filePath)) {
        return response()->file($filePath, ['Content-Type' => 'application/json']);
    }

    return response()->json(['error' => 'Documentation not found'], 404);
});

// Swagger OAuth2 callback
Route::get('/oauth2-callback', function () {
    return view('vendor.l5-swagger.oauth2-callback', ['documentation' => 'default']);
})->name('l5-swagger.default.oauth2_callback');

// Root API routes that match the test expectations
Route::post('/register', [AuthApiController::class, 'register']);
Route::post('/login', [AuthApiController::class, 'login']);
Route::post('/logout', [AuthApiController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/user', [AuthApiController::class, 'me'])->middleware('auth:sanctum');

// Test API routes that match direct endpoints expected by tests
Route::middleware('auth:sanctum')->group(function () {
    // Dashboard endpoint
    Route::get('/dashboard', [DashboardApiController::class, 'index']);

    // Tasks routes
    Route::apiResource('tasks', TaskApiController::class);
    Route::patch('/tasks/{task}/toggle', [TaskApiController::class, 'toggleComplete']);
    Route::get('/tasks/statistics', [TaskApiController::class, 'statistics']);

    // Categories routes
    Route::apiResource('categories', CategoryApiController::class);
    Route::get('/categories/task-counts', [CategoryApiController::class, 'taskCounts']);

    // User routes 
    Route::get('/users', [UserApiController::class, 'index']);
    Route::get('/users/{user}', [UserApiController::class, 'show']);
    Route::get('/users/statistics', [UserApiController::class, 'statistics']);
    
    // Profile routes
    Route::get('/profile', [ProfileApiController::class, 'show']);
    Route::put('/profile', [ProfileApiController::class, 'update']);
    Route::put('/profile/password', [ProfileApiController::class, 'updatePassword']);
    Route::post('/profile/photo', [ProfileApiController::class, 'uploadPhoto']);
    Route::delete('/profile/photo', [ProfileApiController::class, 'deletePhoto']);
});

// API Version 1
Route::prefix('v1')->middleware(['throttle:api'])->group(function () {
    // Documentation routes (public)
    Route::get('/docs', [DocumentationController::class, 'index']);
    Route::get('/docs/info', [DocumentationController::class, 'info']);

    // Public routes
    Route::post('/auth/login', [AuthApiController::class, 'login']);
    Route::post('/auth/register', [AuthApiController::class, 'register']);

    // Routes that require authentication
    Route::middleware('auth:sanctum')->group(function () {
        // Auth routes
        Route::post('/auth/logout', [AuthApiController::class, 'logout']);
        Route::post('/auth/refresh', [AuthApiController::class, 'refresh']);
        Route::get('/auth/me', [AuthApiController::class, 'me']);

        // Dashboard routes
        Route::get('/dashboard', [DashboardApiController::class, 'index']);

        // User and Profile routes
        Route::get('/users', [UserApiController::class, 'index']);
        Route::get('/users/{user}', [UserApiController::class, 'show']);
        Route::get('/users/statistics', [UserApiController::class, 'statistics']);
        
        Route::get('/profile', [ProfileApiController::class, 'show']);
        Route::put('/profile', [ProfileApiController::class, 'update']);
        Route::put('/profile/password', [ProfileApiController::class, 'updatePassword']);
        Route::post('/profile/photo', [ProfileApiController::class, 'uploadPhoto']);
        Route::delete('/profile/photo', [ProfileApiController::class, 'deletePhoto']);

        // Tasks routes
        Route::apiResource('tasks', TaskApiController::class);
        Route::patch('/tasks/{task}/toggle', [TaskApiController::class, 'toggleComplete']);
        Route::get('/tasks/statistics', [TaskApiController::class, 'statistics']);

        // Category routes
        Route::apiResource('categories', CategoryApiController::class);
        Route::get('/categories/task-counts', [CategoryApiController::class, 'taskCounts']);

        // Device token for push notifications
        Route::post('/device-token', [DeviceTokenController::class, 'store']);

        // Test push notification
        Route::post('/test-notification', function (Request $request) {
            $notificationService = new OneSignalNotificationService;

            return $notificationService->sendToUser(
                auth()->id(),
                'Test Notification',
                'This is a test notification from the Todo App',
                ['type' => 'test']
            );
        });
    });
});

// Redirect routes for documentation
Route::get('/documentation', function () {
    return redirect('/api/documentation');
});

// Default to latest version for backward compatibility
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'status_code' => 404,
        'message' => 'API endpoint not found. Please check the documentation at /api/documentation',
        'data' => null,
    ], 404);
});

/*
|--------------------------------------------------------------------------
| Hypervel Async API Routes
|--------------------------------------------------------------------------
|
| These routes demonstrate the use of Hypervel for asynchronous API operations.
| They use the AsyncApiController which leverages coroutines for concurrent
| processing and improved performance.
|
*/

Route::middleware('auth:sanctum')->prefix('async')->group(function () {
    Route::get('/dashboard-stats', [App\Http\Controllers\AsyncApiController::class, 'getDashboardStats']);
    Route::get('/external-apis', [App\Http\Controllers\AsyncApiController::class, 'fetchExternalApis']);
    Route::post('/process-tasks', [App\Http\Controllers\AsyncApiController::class, 'bulkProcessTasks']);
});
