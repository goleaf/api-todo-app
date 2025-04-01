<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SOAP Service Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can define various SOAP services that your application will use.
    | Each service should have a name, wsdl URL, and optional configuration.
    |
    */

    'services' => [
        'example' => [
            'wsdl'     => env('EXAMPLE_SOAP_WSDL', 'https://example.com/service?wsdl'),
            'username' => env('EXAMPLE_SOAP_USERNAME', ''),
            'password' => env('EXAMPLE_SOAP_PASSWORD', ''),
            'options'  => [
                'cache_wsdl'   => WSDL_CACHE_MEMORY,
                'trace'        => true,
                'exceptions'   => true,
                'soap_version' => SOAP_1_2,
            ],
        ],
        
        // Add more SOAP services as needed
    ],
]; 