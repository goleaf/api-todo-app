<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateAndSanitizeRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Sanitize input
        $input = $request->all();
        array_walk_recursive($input, function(&$input) {
            if (is_string($input)) {
                $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
            }
        });
        $request->merge($input);

        // Validate request size
        $maxSize = config('request.max_request_size', 10485760);
        if ($request->header('Content-Length') > $maxSize) {
            abort(413, 'Request entity too large');
        }

        // Validate content type for POST/PUT requests
        if (in_array($request->method(), ['POST', 'PUT']) && !$request->isJson()) {
            $contentType = $request->header('Content-Type');
            $allowedTypes = config('request.allowed_content_types', [
                'application/x-www-form-urlencoded',
                'multipart/form-data',
            ]);

            if (!collect($allowedTypes)->contains(function ($type) use ($contentType) {
                return str_starts_with($contentType, $type);
            })) {
                abort(415, 'Unsupported media type');
            }
        }

        return $next($request);
    }
} 