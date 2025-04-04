<?php

use App\Http\Controllers\Frontend\ProfileController;
use App\Http\Controllers\Frontend\TaskController;
use App\Http\Controllers\Frontend\CategoryController;
use App\Http\Controllers\Frontend\TagController;
use App\Http\Controllers\Frontend\TimeEntryController;
use App\Http\Controllers\Frontend\SmartTagController;
use App\Http\Controllers\Frontend\AttachmentController;
use App\Http\Controllers\Frontend\TermsController;
use App\Http\Controllers\Frontend\DashboardController;
use App\Http\Controllers\Frontend\SettingsController;
use App\Http\Controllers\Frontend\LanguageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Frontend Routes
|--------------------------------------------------------------------------
|
| Here is where you can register frontend specific routes for your application.
| These routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "frontend" middleware group.
|
*/

// Welcome page
Route::get('/', function () {
    return view('welcome');
});

// Language switcher
Route::get('language/{locale}', [LanguageController::class, 'switch'])
    ->name('language.switch');

// Public pages
Route::get('terms', [TermsController::class, 'index'])->name('terms');
Route::get('privacy', [TermsController::class, 'privacy'])->name('privacy');

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/statistics', [DashboardController::class, 'statistics'])->name('statistics');

    // Tasks
    Route::resource('tasks', TaskController::class);
    Route::post('tasks/{task}/toggle-complete', [TaskController::class, 'toggleComplete'])->name('tasks.toggle-complete');
    Route::get('tasks/due-today', [TaskController::class, 'dueToday'])->name('tasks.due-today');
    Route::get('tasks/due-this-week', [TaskController::class, 'dueThisWeek'])->name('tasks.due-this-week');
    Route::get('tasks/overdue', [TaskController::class, 'overdue'])->name('tasks.overdue');
    Route::get('tasks/completed', [TaskController::class, 'completed'])->name('tasks.completed');
    
    // Task Attachments
    Route::get('tasks/{task}/attachments', [AttachmentController::class, 'index'])->name('tasks.attachments.index');
    Route::post('tasks/{task}/attachments', [AttachmentController::class, 'store'])->name('tasks.attachments.store');
    Route::get('attachments/{attachment}/download', [AttachmentController::class, 'download'])->name('attachments.download');
    Route::delete('attachments/{attachment}', [AttachmentController::class, 'destroy'])->name('attachments.destroy');

    // Categories
    Route::resource('categories', CategoryController::class);

    // Tags
    Route::resource('tags', TagController::class);
    
    // Smart Tags
    Route::resource('smart-tags', SmartTagController::class);
    Route::get('smart-tags/{smartTag}/tasks', [SmartTagController::class, 'tasks'])->name('smart-tags.tasks');
    
    // Time Entries
    Route::resource('time-entries', TimeEntryController::class);
    Route::post('tasks/{task}/start-timer', [TimeEntryController::class, 'start'])->name('time-entries.start');
    Route::post('time-entries/{timeEntry}/stop', [TimeEntryController::class, 'stop'])->name('time-entries.stop');
    Route::get('time-entries/summary', [TimeEntryController::class, 'summary'])->name('time-entries.summary');
    
    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        // General settings
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::put('/general', [SettingsController::class, 'updateGeneral'])->name('general.update');
        
        // Profile settings
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
        Route::post('/profile/update-avatar', [ProfileController::class, 'updateAvatar'])->name('profile.update-avatar');
        
        // Notification settings
        Route::get('/notifications', [SettingsController::class, 'notifications'])->name('notifications');
        Route::put('/notifications', [SettingsController::class, 'updateNotifications'])->name('notifications.update');
        
        // Appearance settings
        Route::get('/appearance', [SettingsController::class, 'appearance'])->name('appearance');
        Route::put('/appearance', [SettingsController::class, 'updateAppearance'])->name('appearance.update');
    });
}); 