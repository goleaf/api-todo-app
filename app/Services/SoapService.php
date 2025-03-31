<?php

namespace App\Services;

use App\Soap\Requests\ExampleRequest;
use App\Soap\Responses\ExampleResponse;
use Artisaninweb\SoapWrapper\SoapWrapper;
use Exception;
use Illuminate\Support\Facades\Log;

class SoapService
{
    /**
     * @var SoapWrapper
     */
    protected $soapWrapper;

    /**
     * SoapService constructor.
     *
     * @param SoapWrapper $soapWrapper
     */
    public function __construct(SoapWrapper $soapWrapper)
    {
        $this->soapWrapper = $soapWrapper;
    }

    /**
     * Execute an example SOAP request.
     *
     * @param ExampleRequest $request
     * @return ExampleResponse
     * @throws Exception
     */
    public function executeExampleRequest(ExampleRequest $request): ExampleResponse
    {
        try {
            // Execute the SOAP request
            $response = $this->soapWrapper->client('example')
                ->call('SomeFunction', $request->toArray());

            // Parse the response
            return ExampleResponse::parse($response);
        } catch (Exception $e) {
            // Log the error
            Log::error('SOAP Request Error', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->toArray(),
            ]);

            // Re-throw the exception or handle it as needed
            throw $e;
        }
    }

    /**
     * Generate a mock response for testing.
     *
     * @return ExampleResponse
     */
    public function getMockResponse(): ExampleResponse
    {
        // Create a mock response
        $mockResponse = [
            'Success' => true,
            'Message' => 'Mock response generated successfully',
            'Data' => [
                'id' => random_int(1, 1000),
                'timestamp' => now()->toIso8601String(),
                'items' => [
                    [
                        'name' => 'Product A',
                        'price' => 29.99,
                        'available' => true,
                    ],
                    [
                        'name' => 'Product B',
                        'price' => 19.99,
                        'available' => false,
                    ],
                ],
            ],
        ];

        return ExampleResponse::parse($mockResponse);
    }
} 