<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\StatsController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
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
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // User routes
    Route::get('/user', [UserController::class, 'show']);
    Route::put('/user/profile', [UserController::class, 'updateProfile']);
    Route::put('/user/password', [UserController::class, 'updatePassword']);
    Route::post('/user/photo', [UserController::class, 'uploadPhoto']);
    Route::delete('/user/photo', [UserController::class, 'deletePhoto']);
    Route::get('/user/statistics', [UserController::class, 'statistics']);
    
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    
    // Tasks routes
    Route::get('/tasks/search', [TaskController::class, 'search']);
    Route::post('/tasks/{task}/toggle-complete', [TaskController::class, 'toggleComplete']);
    Route::apiResource('tasks', TaskController::class);
    
    // Categories routes
    Route::apiResource('categories', CategoryController::class);
    
    // Stats routes
    Route::get('/stats/overview', [StatsController::class, 'overview']);
    Route::get('/stats/completion-rate', [StatsController::class, 'completionRate']);
    Route::get('/stats/by-category', [StatsController::class, 'byCategory']);
    Route::get('/stats/by-priority', [StatsController::class, 'byPriority']);
    Route::get('/stats/by-date', [StatsController::class, 'byDate']);
    Route::get('/stats/completion-time', [StatsController::class, 'completionTime']);
});
