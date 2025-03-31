<?php

use App\Livewire\Tasks\TaskCreate;
use App\Livewire\Tasks\TaskEdit;
use App\Livewire\Tasks\TaskList;
use Illuminate\Support\Facades\Route;

// Routes for task management
Route::middleware(['auth'])->prefix('tasks')->name('tasks.')->group(function () {
    Route::get('/', TaskList::class)->name('index');
    Route::get('/create', TaskCreate::class)->name('create');
    Route::get('/{task}/edit', TaskEdit::class)->name('edit');
}); 