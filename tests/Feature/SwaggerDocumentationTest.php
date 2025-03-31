<?php

namespace Tests\Feature;

use Tests\TestCase;

class SwaggerDocumentationTest extends TestCase
{
    /**
     * Test if the Swagger UI route returns a successful response.
     *
     * @return void
     */
    public function test_swagger_ui_route_returns_successful_response()
    {
        $response = $this->get('/api/documentation');
        $response->assertStatus(200);
        $response->assertViewIs('vendor.l5-swagger.index');
    }

    /**
     * Test if the JSON documentation is accessible.
     *
     * @return void
     */
    public function test_swagger_json_is_accessible()
    {
        $response = $this->get('/api/docs/api-docs.json');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');
    }

    /**
     * Test if the documentation contains expected API information.
     *
     * @return void
     */
    public function test_api_info_endpoint_returns_correct_data()
    {
        $response = $this->getJson('/api/v1/docs/info');
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'name' => 'Todo API',
                'version' => '1.0.0',
                'base_url' => '/api/v1',
            ],
        ]);
    }
}
