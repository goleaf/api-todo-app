<?php

namespace Tests\Feature;

use App\Services\SoapService;
use App\Soap\Responses\ExampleResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Mockery;

class SoapTest extends TestCase
{
    use WithFaker;

    /**
     * Test the mock SOAP endpoint.
     *
     * @return void
     */
    public function test_mock_soap_endpoint(): void
    {
        $response = $this->get('/api/soap/mock');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'timestamp',
                    'items',
                ],
            ]);
    }

    /**
     * Test the SOAP example endpoint with mocked service.
     *
     * @return void
     */
    public function test_example_soap_endpoint(): void
    {
        // Create mock response data
        $mockResponseData = [
            'Success' => true,
            'Message' => 'Test successful',
            'Data' => [
                'id' => 123,
                'result' => 'success',
            ],
        ];

        // Create a mock of the SOAP service
        $mockService = Mockery::mock(SoapService::class);
        
        // Set up the expectation
        $mockService->shouldReceive('executeExampleRequest')
            ->once()
            ->andReturn(ExampleResponse::parse($mockResponseData));
        
        // Replace the service in the container
        $this->app->instance(SoapService::class, $mockService);

        // Make the request
        $response = $this->postJson('/api/soap/example', [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'message' => $this->faker->paragraph,
        ]);

        // Assert the response
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Test successful',
                'data' => [
                    'id' => 123,
                    'result' => 'success',
                ],
            ]);
    }

    /**
     * Test validation rules for the SOAP example endpoint.
     *
     * @return void
     */
    public function test_soap_endpoint_validation(): void
    {
        $response = $this->postJson('/api/soap/example', [
            // Missing required fields
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'message']);

        $response = $this->postJson('/api/soap/example', [
            'name' => 'Test User',
            'email' => 'not-an-email', // Invalid email
            'message' => 'Test message',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }
} 