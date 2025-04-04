<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecureHeaders
{
    /**
     * The security headers to be added to the response.
     *
     * @var array<string, string>
     */
    protected $headers = [
        // Prevent clickjacking
        'X-Frame-Options' => 'SAMEORIGIN',
        
        // Enable XSS protection
        'X-XSS-Protection' => '1; mode=block',
        
        // Prevent MIME type sniffing
        'X-Content-Type-Options' => 'nosniff',
        
        // Control referrer information
        'Referrer-Policy' => 'strict-origin-when-cross-origin',
        
        // Content Security Policy
        'Content-Security-Policy' => "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' data: https:;",
        
        // Permissions Policy
        'Permissions-Policy' => 'geolocation=(), microphone=(), camera=(), payment=(), usb=(), fullscreen=(self), display-capture=()',
        
        // Force HTTPS
        'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains; preload',
        
        // Prevent browser from detecting the media type of a resource
        'X-Download-Options' => 'noopen',
        
        // Control browser features
        'X-Permitted-Cross-Domain-Policies' => 'none',
        
        // Cache control
        'Cache-Control' => 'no-store, no-cache, must-revalidate, proxy-revalidate, max-age=0',
        
        // Pragma
        'Pragma' => 'no-cache',
        
        // Expires
        'Expires' => '0',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Add security headers
        foreach ($this->headers as $key => $value) {
            $response->headers->set($key, $value);
        }

        // Remove headers that might expose server information
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        return $response;
    }

    /**
     * Get the security headers.
     *
     * @return array<string, string>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Set a security header.
     *
     * @param  string  $key
     * @param  string  $value
     * @return void
     */
    public function setHeader(string $key, string $value): void
    {
        $this->headers[$key] = $value;
    }
} 