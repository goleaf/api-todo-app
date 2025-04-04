<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Frontend routes
require __DIR__.'/frontend.php';

// Admin routes
require __DIR__.'/admin.php';

// Auth routes
require __DIR__.'/auth.php';

// API routes
require __DIR__.'/api.php';

// Language switcher route
Route::get('language/{locale}', [App\Http\Controllers\LanguageController::class, 'switchLang'])
    ->name('language.switch');

// Redirect any remaining root routes to frontend
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function() {
        return redirect()->route('frontend.tasks.index');
    })->name('dashboard');
});
