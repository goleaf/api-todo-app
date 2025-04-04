<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetApiHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Always return JSON
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        
        // Cache control for API responses
        $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
        
        // Add API version header
        $response->headers->set('X-API-Version', config('app.api_version', '1.0'));
        
        return $response;
    }
} 