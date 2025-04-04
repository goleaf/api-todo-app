<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [TaskController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Tasks
    Route::resource('tasks', TaskController::class);
    Route::post('tasks/{task}/toggle-complete', [TaskController::class, 'toggleComplete'])->name('tasks.toggle-complete');

    // Categories
    Route::resource('categories', CategoryController::class);

    // Tags
    Route::resource('tags', TagController::class);
});

require __DIR__.'/auth.php'; 