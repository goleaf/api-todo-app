<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Request Validation Settings
    |--------------------------------------------------------------------------
    |
    | Here you can configure various settings related to request validation
    | and sanitization.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Maximum Request Size
    |--------------------------------------------------------------------------
    |
    | The maximum size of a request in bytes. This is used to prevent
    | large file uploads or malicious requests that could overwhelm
    | the server.
    |
    */
    'max_request_size' => env('MAX_REQUEST_SIZE', 10485760), // 10MB

    /*
    |--------------------------------------------------------------------------
    | Allowed Content Types
    |--------------------------------------------------------------------------
    |
    | The content types that are allowed for POST and PUT requests.
    | This helps prevent malicious content type attacks.
    |
    */
    'allowed_content_types' => [
        'application/x-www-form-urlencoded',
        'multipart/form-data',
        'application/json',
        'application/xml',
    ],

    /*
    |--------------------------------------------------------------------------
    | Input Sanitization
    |--------------------------------------------------------------------------
    |
    | Configure how input data should be sanitized before processing.
    |
    */
    'sanitize' => [
        'enabled' => true,
        'html_entities' => true,
        'strip_tags' => false,
        'trim' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Configure rate limiting settings for different types of requests.
    |
    */
    'rate_limit' => [
        'api' => [
            'enabled' => true,
            'max_attempts' => 60,
            'decay_minutes' => 1,
        ],
        'web' => [
            'enabled' => true,
            'max_attempts' => 60,
            'decay_minutes' => 1,
        ],
        'auth' => [
            'enabled' => true,
            'max_attempts' => 5,
            'decay_minutes' => 1,
        ],
    ],
]; 