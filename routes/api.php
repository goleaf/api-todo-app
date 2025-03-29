<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DeviceTokenController;
use App\Http\Controllers\Api\StatsController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
use App\Services\OneSignalNotificationService;
use Illuminate\Http\Request;
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

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Routes that require authentication
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh-token', [AuthController::class, 'refresh']);
    Route::get('/user', [AuthController::class, 'me']);
    
    // Dashboard routes
    Route::get('/dashboard', [DashboardController::class, 'index']);
    
    // User routes
    Route::apiResource('users', UserController::class);
    
    // Tasks routes
    Route::apiResource('tasks', TaskController::class);
    Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggleComplete']);
    
    // Category routes
    Route::apiResource('categories', CategoryController::class);
    
    // Stats routes
    Route::get('/stats', [StatsController::class, 'index']);
    Route::get('/stats/summary', [StatsController::class, 'summary']);
    Route::get('/stats/daily', [StatsController::class, 'daily']);
    Route::get('/stats/weekly', [StatsController::class, 'weekly']);
    Route::get('/stats/monthly', [StatsController::class, 'monthly']);
    
    // Device token for push notifications
    Route::post('/device-token', [DeviceTokenController::class, 'store']);
    
    // Test push notification
    Route::post('/test-notification', function (Request $request) {
        $notificationService = new OneSignalNotificationService();
        return $notificationService->sendToUser(
            auth()->id(),
            'Test Notification',
            'This is a test notification from the Todo App',
            ['type' => 'test']
        );
    });
});
