<?php

use App\Http\Controllers\Api\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Api\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Api\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Api\Admin\TaskController as AdminTaskController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\Admin\TagController as AdminTagController;
use App\Http\Controllers\Api\Admin\TimeEntryController as AdminTimeEntryController;
use App\Http\Controllers\Api\Admin\SmartTagController as AdminSmartTagController;
use App\Http\Controllers\Api\Admin\AttachmentController as AdminAttachmentController;
use App\Http\Controllers\Api\Frontend\AuthController;
use App\Http\Controllers\Api\Frontend\CategoryController;
use App\Http\Controllers\Api\Frontend\DashboardController;
use App\Http\Controllers\Api\Frontend\TaskController;
use App\Http\Controllers\Api\Frontend\TagController;
use App\Http\Controllers\Api\Frontend\TimeEntryController;
use App\Http\Controllers\Api\Frontend\ProfileController;
use App\Http\Controllers\Api\Frontend\SmartTagController;
use App\Http\Controllers\Api\Frontend\AttachmentController;
use App\Http\Controllers\Api\Frontend\StatisticsController;
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

// Frontend API Routes
Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        // Auth
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('user', [AuthController::class, 'user']);

        // Profile
        Route::get('profile', [ProfileController::class, 'show']);
        Route::put('profile', [ProfileController::class, 'update']);
        Route::delete('profile', [ProfileController::class, 'destroy']);
        Route::post('profile/update-password', [ProfileController::class, 'updatePassword']);
        Route::post('profile/update-avatar', [ProfileController::class, 'updateAvatar']);

        // Dashboard
        Route::get('dashboard', [DashboardController::class, 'index']);
        Route::get('statistics', [StatisticsController::class, 'index']);
        Route::get('statistics/tasks-by-status', [StatisticsController::class, 'tasksByStatus']);
        Route::get('statistics/tasks-by-priority', [StatisticsController::class, 'tasksByPriority']);
        Route::get('statistics/tasks-by-category', [StatisticsController::class, 'tasksByCategory']);
        Route::get('statistics/time-spent', [StatisticsController::class, 'timeSpent']);

        // Tasks
        Route::apiResource('tasks', TaskController::class);
        Route::post('tasks/{task}/toggle-complete', [TaskController::class, 'toggleComplete']);
        Route::get('tasks/due-today', [TaskController::class, 'dueToday']);
        Route::get('tasks/due-this-week', [TaskController::class, 'dueThisWeek']);
        Route::get('tasks/overdue', [TaskController::class, 'overdue']);
        Route::get('tasks/completed', [TaskController::class, 'completed']);
        
        // Task Attachments
        Route::get('tasks/{task}/attachments', [AttachmentController::class, 'index']);
        Route::post('tasks/{task}/attachments', [AttachmentController::class, 'store']);
        Route::get('attachments/{attachment}', [AttachmentController::class, 'show']);
        Route::get('attachments/{attachment}/download', [AttachmentController::class, 'download']);
        Route::delete('attachments/{attachment}', [AttachmentController::class, 'destroy']);

        // Categories
        Route::apiResource('categories', CategoryController::class);
        Route::get('categories/{category}/tasks', [CategoryController::class, 'tasks']);

        // Tags
        Route::get('tags/suggest', [TagController::class, 'suggest']);
        Route::apiResource('tags', TagController::class);
        Route::get('tags/{tag}/tasks', [TagController::class, 'tasks']);

        // Smart Tags
        Route::apiResource('smart-tags', SmartTagController::class);
        Route::get('smart-tags/{smartTag}/tasks', [SmartTagController::class, 'tasks']);
        
        // Time Entries
        Route::apiResource('time-entries', TimeEntryController::class);
        Route::post('tasks/{task}/start-timer', [TimeEntryController::class, 'start']);
        Route::post('time-entries/{timeEntry}/stop', [TimeEntryController::class, 'stop']);
        Route::get('time-entries/summary/daily', [TimeEntryController::class, 'dailySummary']);
        Route::get('time-entries/summary/weekly', [TimeEntryController::class, 'weeklySummary']);
        Route::get('time-entries/summary/monthly', [TimeEntryController::class, 'monthlySummary']);
    });
});

// Admin API Routes
Route::prefix('v1/admin')->group(function () {
    // Public routes
    Route::post('login', [AdminAuthController::class, 'login']);
    Route::post('forgot-password', [AdminAuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AdminAuthController::class, 'resetPassword']);

    // Protected routes
    Route::middleware(['auth:sanctum', 'admin'])->group(function () {
        // Auth
        Route::post('logout', [AdminAuthController::class, 'logout']);
        Route::get('user', [AdminAuthController::class, 'user']);

        // Dashboard
        Route::get('dashboard', [AdminDashboardController::class, 'index']);
        Route::get('dashboard/activity-log', [AdminDashboardController::class, 'activityLog']);
        Route::get('dashboard/system-info', [AdminDashboardController::class, 'systemInfo']);

        // Users
        Route::apiResource('users', AdminUserController::class);
        Route::post('users/{user}/toggle-active', [AdminUserController::class, 'toggleActive']);
        Route::get('users/{user}/tasks', [AdminUserController::class, 'userTasks']);
        Route::get('users/{user}/time-entries', [AdminUserController::class, 'userTimeEntries']);

        // Tasks
        Route::apiResource('tasks', AdminTaskController::class);
        Route::post('tasks/{task}/toggle-complete', [AdminTaskController::class, 'toggleComplete']);
        Route::get('tasks/statistics', [AdminTaskController::class, 'statistics']);
        
        // Task Attachments
        Route::get('tasks/{task}/attachments', [AdminAttachmentController::class, 'index']);
        Route::post('tasks/{task}/attachments', [AdminAttachmentController::class, 'store']);
        Route::get('attachments/{attachment}', [AdminAttachmentController::class, 'show']);
        Route::get('attachments/{attachment}/download', [AdminAttachmentController::class, 'download']);
        Route::delete('attachments/{attachment}', [AdminAttachmentController::class, 'destroy']);

        // Categories
        Route::apiResource('categories', AdminCategoryController::class);
        Route::get('categories/{category}/tasks', [AdminCategoryController::class, 'categoryTasks']);

        // Tags
        Route::apiResource('tags', AdminTagController::class);
        Route::get('tags/{tag}/tasks', [AdminTagController::class, 'tagTasks']);

        // Smart Tags
        Route::apiResource('smart-tags', AdminSmartTagController::class);
        Route::get('smart-tags/{smartTag}/tasks', [AdminSmartTagController::class, 'smartTagTasks']);

        // Time Entries
        Route::apiResource('time-entries', AdminTimeEntryController::class);
        Route::get('users/{user}/time-entries', [AdminTimeEntryController::class, 'userTimeEntries']);
        Route::get('tasks/{task}/time-entries', [AdminTimeEntryController::class, 'taskTimeEntries']);
        Route::get('time-entries/date-range', [AdminTimeEntryController::class, 'dateRange']);
        Route::get('time-entries/statistics', [AdminTimeEntryController::class, 'statistics']);

        // Settings
        Route::get('settings', [AdminSettingsController::class, 'index']);
        Route::put('settings', [AdminSettingsController::class, 'update']);
        Route::post('settings/maintenance-mode', [AdminSettingsController::class, 'toggleMaintenanceMode']);
        Route::get('settings/backups', [AdminSettingsController::class, 'listBackups']);
        Route::post('settings/backups/create', [AdminSettingsController::class, 'createBackup']);
        Route::post('settings/backups/restore/{filename}', [AdminSettingsController::class, 'restoreBackup']);
        Route::delete('settings/backups/{filename}', [AdminSettingsController::class, 'deleteBackup']);
    });
});
