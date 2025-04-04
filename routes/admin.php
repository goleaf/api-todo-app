<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\Auth\ResetPasswordController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\TimeEntryController;
use App\Http\Controllers\Admin\SmartTagController;
use App\Http\Controllers\Admin\AttachmentController;
use Illuminate\Support\Facades\Route;

// Admin Guest Routes
Route::middleware(['web', 'guest:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);

    // Password Reset Routes
    Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])
        ->name('password.request');
    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])
        ->name('password.email');
    Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])
        ->name('password.reset');
    Route::post('password/reset', [ResetPasswordController::class, 'reset'])
        ->name('password.update');
});

// Admin Authenticated Routes
Route::middleware(['web', 'auth:admin', 'can:access-admin'])->prefix('admin')->name('admin.')->group(function () {
    // Logout
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Tasks
    Route::resource('tasks', TaskController::class);
    Route::post('tasks/{task}/toggle-complete', [TaskController::class, 'toggleComplete'])->name('tasks.toggle-complete');
    
    // Categories
    Route::resource('categories', CategoryController::class);
    Route::get('categories/{category}/tasks', [CategoryController::class, 'categoryTasks'])->name('categories.tasks');

    // Tags
    Route::resource('tags', TagController::class);
    Route::get('tags/{tag}/tasks', [TagController::class, 'tagTasks'])->name('tags.tasks');

    // Smart Tags
    Route::resource('smart-tags', SmartTagController::class);
    Route::get('smart-tags/{smartTag}/tasks', [SmartTagController::class, 'smartTagTasks'])->name('smart-tags.tasks');

    // Time Entries
    Route::resource('time-entries', TimeEntryController::class);
    Route::get('tasks/{task}/time-entries', [TimeEntryController::class, 'taskTimeEntries'])->name('tasks.time-entries');
    Route::get('users/{user}/time-entries', [TimeEntryController::class, 'userTimeEntries'])->name('users.time-entries');

    // Users
    Route::resource('users', UserController::class);
    Route::post('users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');

    // Attachments
    Route::get('tasks/{task}/attachments', [AttachmentController::class, 'index'])->name('tasks.attachments.index');
    Route::post('tasks/{task}/attachments', [AttachmentController::class, 'store'])->name('tasks.attachments.store');
    Route::get('attachments/{attachment}', [AttachmentController::class, 'show'])->name('attachments.show');
    Route::get('attachments/{attachment}/download', [AttachmentController::class, 'download'])->name('attachments.download');
    Route::delete('attachments/{attachment}', [AttachmentController::class, 'destroy'])->name('attachments.destroy');

    // Settings
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('settings/general', [SettingsController::class, 'updateGeneral'])->name('settings.general.update');
    Route::put('settings/security', [SettingsController::class, 'updateSecurity'])->name('settings.security.update');
    Route::post('settings/maintenance-mode', [SettingsController::class, 'toggleMaintenanceMode'])->name('settings.maintenance-mode.toggle');

    // Statistics
    Route::get('statistics/users', [DashboardController::class, 'userStatistics'])->name('statistics.users');
    Route::get('statistics/tasks', [DashboardController::class, 'taskStatistics'])->name('statistics.tasks');
    Route::get('statistics/time-entries', [DashboardController::class, 'timeEntryStatistics'])->name('statistics.time-entries');
});
