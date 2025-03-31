<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This application is API-only. All web routes are redirected to the API
| documentation. For actual functionality, use the API endpoints.
|
*/

// Redirect all web routes to API documentation
Route::get('/', function () {
    return redirect('/api/documentation');
});

// Fallback route
Route::fallback(function () {
    return redirect('/api/documentation');
});
