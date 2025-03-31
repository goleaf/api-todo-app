<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Message Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used in API responses to provide
    | consistent and localized messages to the client.
    |
    */

    // General messages
    'success' => 'Operation completed successfully.',
    'created' => 'Resource created successfully.',
    'updated' => 'Resource updated successfully.',
    'deleted' => 'Resource deleted successfully.',
    'not_found' => 'Resource not found.',
    'unauthorized' => 'You are not authorized to access this resource.',
    'forbidden' => 'You do not have permission to perform this action.',
    'validation_failed' => 'Validation failed. Please check your input.',
    'server_error' => 'An unexpected error occurred. Please try again later.',

    // Task specific messages
    'task' => [
        'created' => 'Task created successfully.',
        'updated' => 'Task updated successfully.',
        'deleted' => 'Task deleted successfully.',
        'not_found' => 'Task not found.',
        'status_updated' => 'Task status updated successfully.',
    ],

    // Category specific messages
    'category' => [
        'created' => 'Category created successfully.',
        'updated' => 'Category updated successfully.',
        'deleted' => 'Category deleted successfully.',
        'not_found' => 'Category not found.',
        'has_tasks' => 'Cannot delete category that contains tasks.',
    ],

    // Auth specific messages
    'auth' => [
        'registered' => 'User registered successfully.',
        'login_success' => 'Login successful.',
        'logout_success' => 'Logout successful.',
        'token_refreshed' => 'Token refreshed successfully.',
        'invalid_credentials' => 'Invalid login credentials.',
        'token_invalid' => 'Invalid token.',
        'token_expired' => 'Token has expired.',
        'account_inactive' => 'Your account is inactive.',
    ],

    // User specific messages
    'user' => [
        'profile_updated' => 'Profile updated successfully.',
        'password_updated' => 'Password updated successfully.',
        'photo_uploaded' => 'Profile photo uploaded successfully.',
        'photo_deleted' => 'Profile photo deleted successfully.',
        'not_found' => 'User not found.',
    ],
]; 