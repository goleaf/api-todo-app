# Laravel SOAP Integration

This documentation covers the integration of SOAP services within our Laravel application using the `artisaninweb/laravel-soap` package.

## Overview

The SOAP (Simple Object Access Protocol) integration allows our application to communicate with external SOAP web services. This implementation provides a structured approach to handle SOAP requests and responses, with features like request validation, error handling, and response parsing.

## Table of Contents

- [Installation](#installation)
- [Configuration](#configuration)
- [Architecture](#architecture)
- [Usage Examples](#usage-examples)
- [Testing](#testing)
- [Troubleshooting](#troubleshooting)

## Installation

The SOAP integration is implemented using the `artisaninweb/laravel-soap` package:

```bash
composer require artisaninweb/laravel-soap
```

## Configuration

### Service Provider

The package's service provider is automatically registered through Laravel's package auto-discovery feature. Additionally, we've created a custom `SoapServiceProvider` in `app/Providers/SoapServiceProvider.php` to handle our specific SOAP service configurations.

### Configuration File

SOAP services are configured in `config/soap.php`:

```php
<?php

return [
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
```

Add your SOAP service credentials to your `.env` file:

```
EXAMPLE_SOAP_WSDL=https://your-soap-service.com/endpoint?wsdl
EXAMPLE_SOAP_USERNAME=your_username
EXAMPLE_SOAP_PASSWORD=your_password
```

## Architecture

Our SOAP implementation follows a clean architecture pattern:

### Components

1. **SOAP Service Provider** (`app/Providers/SoapServiceProvider.php`)
   - Registers SOAP services from configuration
   - Sets up client options and authentication

2. **Request Classes** (`app/Soap/Requests/`)
   - Encapsulate request data
   - Implement the `Arrayable` interface for easy conversion

3. **Response Classes** (`app/Soap/Responses/`)
   - Parse SOAP responses
   - Provide accessor methods for response data

4. **Service Class** (`app/Services/SoapService.php`)
   - Handles SOAP client interaction
   - Implements error handling and logging
   - Provides a clean API for controllers

5. **Controller** (`app/Http/Controllers/SoapController.php`)
   - Handles HTTP requests
   - Performs validation
   - Uses the SoapService to execute requests

6. **Routes** (`routes/api.php`)
   - Defines endpoints for SOAP operations

## Usage Examples

### Basic Usage

To make a SOAP request from a controller:

```php
use App\Services\SoapService;
use App\Soap\Requests\ExampleRequest;

class YourController extends Controller
{
    protected $soapService;

    public function __construct(SoapService $soapService)
    {
        $this->soapService = $soapService;
    }

    public function someAction(Request $request)
    {
        // Create a request object
        $soapRequest = new ExampleRequest(
            $request->input('name'),
            $request->input('email'),
            $request->input('message')
        );

        // Execute the request
        $response = $this->soapService->executeExampleRequest($soapRequest);

        // Use the response
        if ($response->isSuccess()) {
            return response()->json([
                'success' => true,
                'data' => $response->getData(),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $response->getMessage(),
        ]);
    }
}
```

### Making API Requests

You can test the SOAP API using curl or Postman:

```bash
curl -X POST \
  http://your-app.test/api/soap/example \
  -H 'Content-Type: application/json' \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "message": "Test message"
}'
```

## Testing

The SOAP integration includes dedicated tests in `tests/Feature/SoapTest.php`. These tests cover:

1. Testing the mock endpoint
2. Testing the example endpoint with a mocked service
3. Testing input validation

To run the tests:

```bash
php artisan test --filter=SoapTest
```

## Troubleshooting

### Common Issues

1. **WSDL Access Issues**
   
   If you're having trouble accessing the WSDL, check:
   - Network connectivity
   - Firewall settings
   - Certificate issues
   
   Solution: Try using `stream_context` options:
   
   ```php
   'options' => [
       'stream_context' => stream_context_create([
           'ssl' => [
               'verify_peer' => false,
               'verify_peer_name' => false,
           ],
       ]),
   ]
   ```

2. **Authentication Failures**
   
   Check that credentials are correctly set in your .env file and that they have the correct permissions.

3. **Timeout Issues**
   
   For slow SOAP services, increase the timeout:
   
   ```php
   'options' => [
       'connection_timeout' => 30, // seconds
   ]
   ```

### Debugging

To debug SOAP requests and responses, enable tracing in your service configuration:

```php
'options' => [
    'trace' => true,
]
```

Then you can get the request and response XML:

```php
$lastRequest = $this->soapWrapper->client('example')->__getLastRequest();
$lastResponse = $this->soapWrapper->client('example')->__getLastResponse();
```

## Further Resources

- [artisaninweb/laravel-soap GitHub Repository](https://github.com/artisaninweb/laravel-soap)
- [PHP SOAP Extension Documentation](https://www.php.net/manual/en/book.soap.php)
- [SOAP 1.1 Specification](https://www.w3.org/TR/2000/NOTE-SOAP-20000508/)
- [SOAP 1.2 Specification](https://www.w3.org/TR/soap12-part1/) 