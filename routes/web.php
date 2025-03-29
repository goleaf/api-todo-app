<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Main dashboard route
Route::get('/', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');

// Auth routes
Route::get('/login', function () {
    return Inertia::render('Auth/Login');
})->name('login');

Route::get('/register', function () {
    return Inertia::render('Auth/Register');
})->name('register');

// Task routes
Route::get('/tasks', function () {
    return Inertia::render('Tasks/Index');
})->middleware(['auth'])->name('tasks.index');

Route::get('/tasks/{id}', function ($id) {
    return Inertia::render('Tasks/Show', ['id' => $id]);
})->middleware(['auth'])->name('tasks.show');

// Profile route
Route::get('/profile', function () {
    return Inertia::render('Profile');
})->middleware(['auth'])->name('profile');

// Stats route
Route::get('/stats', function () {
    return Inertia::render('Stats');
})->middleware(['auth'])->name('stats');

// Calendar route
Route::get('/calendar', function () {
    return Inertia::render('Calendar');
})->middleware(['auth'])->name('calendar');

// Fallback route for SPA
Route::get('/{any}', function () {
    return Inertia::render('Dashboard');
})->where('any', '.*')->name('fallback');
