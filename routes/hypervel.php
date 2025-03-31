<?php

use App\Livewire\Dashboard;
use App\Livewire\Dashboard\HypervelDashboard;
use App\Livewire\TodoBulkProcessor;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Hypervel Routes
|--------------------------------------------------------------------------
|
| Routes for Hypervel-powered components and features
|
*/

// Dashboard route
Route::middleware(['auth'])->prefix('dashboard')->group(function () {
    Route::get('/', Dashboard::class)->name('dashboard');
    
    Route::get('/hypervel', function () {
        return view('dashboard.hypervel');
    })->name('dashboard.hypervel');
});

// Bulk processing route
Route::middleware(['auth'])->prefix('todos')->group(function () {
    Route::get('/bulk', function () {
        return view('todos.bulk');
    })->name('todos.bulk');
});

// Register Livewire components
Livewire::component('dashboard.hypervel-dashboard', HypervelDashboard::class);
Livewire::component('todo-bulk-processor', TodoBulkProcessor::class); 