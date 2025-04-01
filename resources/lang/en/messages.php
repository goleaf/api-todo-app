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
    'success' => 'Success!',
    'error' => 'Error!',
    'not_found' => 'Resource not found.',
    'unauthorized' => 'Unauthorized action.',
    'forbidden' => 'Forbidden action.',
    'validation_error' => 'Validation error.',
    'bad_request' => 'Bad request.',
    'server_error' => 'Server error.',
    'created' => 'Resource created successfully.',
    'updated' => 'Resource updated successfully.',
    'deleted' => 'Resource deleted successfully.',
    'no_content' => 'No content.',
    'welcome' => 'Welcome to our API!',
    'goodbye' => 'Goodbye!',
    'thank_you' => 'Thank you!',
    'please_try_again' => 'Please try again.',
    'invalid_credentials' => 'Invalid credentials.',
    'token_invalid' => 'Invalid token.',
    'token_expired' => 'Token expired.',
    'token_blacklisted' => 'Token blacklisted.',
    'token_not_provided' => 'Token not provided.',
    'login_successful' => 'Login successful.',
    'logout_successful' => 'Logout successful.',
    'registration_successful' => 'Registration successful.',
    'email_verification_sent' => 'Email verification sent.',
    'email_verified' => 'Email verified.',
    'password_reset_sent' => 'Password reset sent.',
    'password_reset' => 'Password reset.',
    'password_changed' => 'Password changed.',
    'account_created' => 'Account created.',
    'account_deleted' => 'Account deleted.',
    'account_updated' => 'Account updated.',
    'account_inactive' => 'Account inactive.',
    'account_active' => 'Account active.',
    'account_suspended' => 'Account suspended.',
    'account_banned' => 'Account banned.',
    'account_reinstated' => 'Account reinstated.',
    'account_verified' => 'Account verified.',

    // Task specific messages
    'task' => [
        'created' => 'Task created successfully.',
        'updated' => 'Task updated successfully.',
        'deleted' => 'Task deleted successfully.',
        'not_found' => 'Task not found.',
        'toggled' => 'Task completion status toggled successfully.',
        'invalid_priority' => 'Invalid task priority.',
        'invalid_date' => 'Invalid due date.',
        'duplicate' => 'A task with this title already exists.',
        'permission_denied' => 'You do not have permission to manage this task.',
    ],

    // Category specific messages
    'category' => [
        'created' => 'Category created successfully.',
        'updated' => 'Category updated successfully.',
        'deleted' => 'Category deleted successfully.',
        'not_found' => 'Category not found.',
        'in_use' => 'Cannot delete category because it has associated tasks.',
        'default' => 'Default category cannot be deleted.',
        'duplicate' => 'A category with this name already exists.',
        'permission_denied' => 'You do not have permission to manage this category.',
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
