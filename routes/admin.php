<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\TaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application.
|
*/

// Redirect root to admin login
Route::redirect('/', '/admin/login');

// Admin Authentication Routes
Route::group([], function () {
    // Redirect admin root to login
    Route::redirect('/', '/admin/login');

    // Guest routes
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    });

    // Protected admin routes
    Route::middleware('auth:admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/chart-data', [DashboardController::class, 'getChartData'])->name('dashboard.chart-data');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        
        // Users management
        Route::resource('users', UserController::class);
        
        // Categories management
        Route::resource('categories', CategoryController::class);
        
        // Tags management
        Route::resource('tags', TagController::class);
        
        // Tasks management
        Route::resource('tasks', TaskController::class);
        Route::post('tasks/{task}/toggle', [TaskController::class, 'toggleCompletion'])->name('tasks.toggle');
        Route::get('users/{user}/categories', [TaskController::class, 'getCategoriesForUser'])->name('users.categories');
        Route::get('users/{user}/tags', [TaskController::class, 'getTagsForUser'])->name('users.tags');
    });
}); 