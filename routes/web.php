<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file now only includes redirects to the appropriate API or admin routes.
| All functionality has been moved to api.php or admin.php.
|
*/

// Redirect root to admin login
Route::redirect('/', '/admin/login');

// Redirect old dashboard path to admin dashboard
Route::redirect('/dashboard', '/admin/user-dashboard');

// Include auth routes
require __DIR__.'/auth.php'; 