<?php

namespace App\Http\Controllers;

use App\Services\SoapService;
use App\Soap\Requests\ExampleRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SoapController extends Controller
{
    /**
     * @var SoapService
     */
    protected $soapService;

    /**
     * SoapController constructor.
     *
     * @param SoapService $soapService
     */
    public function __construct(SoapService $soapService)
    {
        $this->soapService = $soapService;
    }

    /**
     * Example method to demonstrate fetching data from a SOAP service.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function exampleMethod(Request $request): JsonResponse
    {
        // Validate request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string',
        ]);

        try {
            // Create a request object
            $soapRequest = new ExampleRequest(
                $validated['name'],
                $validated['email'],
                $validated['message']
            );

            // Execute the request and get the response
            $response = $this->soapService->executeExampleRequest($soapRequest);

            // Return the response
            return response()->json([
                'success' => $response->isSuccess(),
                'message' => $response->getMessage(),
                'data' => $response->getData(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'SOAP error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Returns a mock response for demonstration purposes.
     *
     * @return JsonResponse
     */
    public function mockResponse(): JsonResponse
    {
        // Get a mock response
        $response = $this->soapService->getMockResponse();

        // Return the response
        return response()->json([
            'success' => $response->isSuccess(),
            'message' => $response->getMessage(),
            'data' => $response->getData(),
        ]);
    }
} 