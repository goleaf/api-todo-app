<?php

// This is an API-only application
// No web routes are defined here
// All functionality is available through API routes in api.php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin/login');

Route::prefix('admin')->name('admin.')->group(function() {
    Route::redirect('/', '/admin/login');
});
