<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Resources\Json\JsonResource;

class ApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register API response macros
        $this->registerResponseMacros();
        
        // Set default headers for all API responses
        $this->setDefaultHeaders();
        
        // Don't wrap API resources with a data key by default
        JsonResource::withoutWrapping();
        
        // Register API routes
        $this->registerRoutes();
    }
    
    /**
     * Register response macros for the API.
     *
     * @return void
     */
    protected function registerResponseMacros(): void
    {
        // Success response macro
        Response::macro('success', function ($data = null, $message = null, $statusCode = 200) {
            $response = [
                'success' => true,
                'data' => $data,
            ];
            
            if ($message) {
                $response['message'] = $message;
            }
            
            return Response::json($response, $statusCode);
        });
        
        // Error response macro
        Response::macro('error', function ($message = 'An error occurred', $statusCode = 400, $errors = null) {
            $response = [
                'success' => false,
                'message' => $message,
            ];
            
            if ($errors) {
                $response['errors'] = $errors;
            }
            
            return Response::json($response, $statusCode);
        });
        
        // Not found response macro
        Response::macro('notFound', function ($message = 'Resource not found') {
            return Response::error($message, 404);
        });
        
        // Validation error response macro
        Response::macro('validationError', function ($errors) {
            return Response::error('Validation failed', 422, $errors);
        });
    }
    
    /**
     * Set default headers for all API responses.
     *
     * @return void
     */
    protected function setDefaultHeaders(): void
    {
        // Add middleware to set default headers for API responses
        $this->app->afterResolving(\Illuminate\Contracts\Http\Kernel::class, function ($kernel) {
            $kernel->prependMiddleware(\App\Http\Middleware\SetApiHeaders::class);
        });
    }
    
    /**
     * Register API routes.
     *
     * @return void
     */
    protected function registerRoutes()
    {
        // If we want custom API route registration beyond RouteServiceProvider
    }
} 