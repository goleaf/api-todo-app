<?php

use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Calendar;
use App\Livewire\Categories\CategoryCreate;
use App\Livewire\Categories\CategoryEdit;
use App\Livewire\Categories\CategoryList;
use App\Livewire\Dashboard;
use App\Livewire\Profile;
use App\Livewire\Tasks\Todo; // This is a Livewire component, not the Todo model
use App\Livewire\Dashboard\HypervelDashboard;
use Illuminate\Support\Facades\Route;

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

// API Documentation route
Route::get('/api/documentation', function () {
    $baseUrl = url('/');
    $urlToDocs = $baseUrl.'/api/docs/api-docs.json';
    $documentation = 'default';
    $useAbsolutePath = true;
    $operationsSorter = null;
    $configUrl = null;
    $validatorUrl = null;

    return view('vendor.l5-swagger.index', compact(
        'documentation',
        'urlToDocs',
        'useAbsolutePath',
        'operationsSorter',
        'configUrl',
        'validatorUrl'
    ));
})->name('l5-swagger.default.api');

// Main dashboard route
Route::get('/', Dashboard::class)->middleware(['auth'])->name('dashboard');

// Auth routes
Route::get('/login', Login::class)->name('login');
Route::get('/register', Register::class)->name('register');
Route::post('/logout', function () {
    auth()->logout();
    session()->invalidate();
    session()->regenerateToken();

    return redirect()->route('login');
})->middleware('auth')->name('logout');

// Password Reset Routes
Route::get('/forgot-password', function() {
    return view('auth.forgot-password');
})->middleware('guest')->name('password.request');

Route::post('/forgot-password', function() {
    // This would normally use the Laravel password reset functionality
    // For now, just redirect back to login
    return redirect()->route('login')->with('status', 'Password reset link would be sent in a real application');
})->middleware('guest')->name('password.email');

Route::get('/reset-password/{token}', function() {
    return view('auth.reset-password');
})->middleware('guest')->name('password.reset');

Route::post('/reset-password', function() {
    // This would normally handle the password reset logic
    // For now, just redirect back to login
    return redirect()->route('login')->with('status', 'Password has been reset successfully');
})->middleware('guest')->name('password.update');

// Include task routes
require __DIR__.'/tasks.php';

// Todo route
Route::get('/todo', Todo::class)->middleware(['auth'])->name('todo');

// Category routes
Route::prefix('categories')->middleware(['auth'])->group(function () {
    Route::get('/', CategoryList::class)->name('categories.index');
    Route::get('/create', CategoryCreate::class)->name('categories.create');
    Route::get('/{category}/edit', CategoryEdit::class)->name('categories.edit');
});

// Profile route
Route::get('/profile', Profile::class)->middleware(['auth'])->name('profile');

// Calendar route
Route::get('/calendar', App\Livewire\Calendar\Calendar::class)->middleware(['auth'])->name('calendar');

// Test route for CSS verification
Route::get('/css-test', function () {
    return view('test');
})->name('css.test');

// 404 fallback route
Route::fallback(function () {
    return view('errors.404');
});

// Include Hypervel routes
require __DIR__.'/hypervel.php';

// Add this route to the existing routes
Route::get('/hypervel-demo', HypervelDashboard::class)->middleware(['auth'])->name('hypervel.demo');

// TaskMvc routes (formerly TodoMvc)
Route::get('/taskmvc', App\Livewire\TaskMvc::class)->middleware(['auth'])->name('taskmvc');
Route::get('/taskmvc/{filter?}', App\Livewire\TaskMvc::class)->middleware(['auth'])->name('taskmvc.filter');

// Keep the old routes for backward compatibility but redirect to new ones
Route::get('/todomvc', function() {
    return redirect()->route('taskmvc');
})->middleware(['auth'])->name('todomvc');
Route::get('/todomvc/{filter?}', function($filter = null) {
    return redirect()->route('taskmvc.filter', ['filter' => $filter]);
})->middleware(['auth'])->name('todomvc.filter');

// Add route for bulk task processing
Route::get('/tasks/bulk', function () {
    return view('tasks.bulk', [
        'title' => 'Bulk Task Processing'
    ]);
})->middleware(['auth'])->name('tasks.bulk');

// Redirect old /todos/bulk route to new one for backward compatibility
Route::get('/todos/bulk', function () {
    return redirect()->route('tasks.bulk');
})->middleware(['auth'])->name('todos.bulk');
