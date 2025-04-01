<?php

use App\Http\Controllers\Api\AttachmentController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SmartTagController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\TimeEntryController;
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
Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // User info
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
    
    // Tasks
    Route::apiResource('tasks', TaskController::class);
    Route::patch('tasks/{task}/toggle', [TaskController::class, 'toggle']);
    
    // Categories
    Route::apiResource('categories', CategoryController::class);
    
    // Tags
    Route::apiResource('tags', TagController::class);
    
    // Smart Tags
    Route::apiResource('smart-tags', SmartTagController::class);
    Route::get('smart-tags/{smartTag}/tasks', [SmartTagController::class, 'tasks']);
    
    // Time Entries
    Route::apiResource('time-entries', TimeEntryController::class);
    Route::post('tasks/{task}/time-entries/start', [TimeEntryController::class, 'start']);
    Route::patch('time-entries/{timeEntry}/stop', [TimeEntryController::class, 'stop']);
    
    // Attachments
    Route::apiResource('attachments', AttachmentController::class);
    Route::post('tasks/{task}/attachments', [AttachmentController::class, 'storeForTask']);
    
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Api\DashboardController::class, 'index']);
});
