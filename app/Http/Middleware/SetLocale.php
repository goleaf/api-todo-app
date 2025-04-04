<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * List of supported languages
     * 
     * @var array
     */
    protected $supportedLocales = [
        'en', 'ru', 'lt', 'fr', 'de', 'es', 'it', 'ja'
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Set locale from session if it exists
        if (Session::has('locale') && in_array(Session::get('locale'), $this->supportedLocales)) {
            App::setLocale(Session::get('locale'));
        } else {
            // Try to detect browser language
            $browserLocale = substr($request->server('HTTP_ACCEPT_LANGUAGE') ?? 'en', 0, 2);
            
            // Check if browser locale is supported
            if (in_array($browserLocale, $this->supportedLocales)) {
                App::setLocale($browserLocale);
                Session::put('locale', $browserLocale);
            } else {
                // Default to English
                App::setLocale('en');
                Session::put('locale', 'en');
            }
        }
        
        return $next($request);
    }
} 