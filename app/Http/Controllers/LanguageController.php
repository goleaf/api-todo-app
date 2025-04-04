<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class LanguageController extends Controller
{
    /**
     * Switch the application language
     *
     * @param Request $request
     * @param string $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switchLang(Request $request, $locale)
    {
        // Validate the locale parameter
        $validator = Validator::make(['locale' => $locale], [
            'locale' => 'required|string|in:en,ru,lt,fr,de,es,it,ja',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', __('Invalid language selection.'));
        }

        // Set the application locale
        app()->setLocale($locale);
        
        // Store the locale in session
        Session::put('locale', $locale);
        
        // Redirect back to the previous page
        return redirect()->back()->with('success', __('Language changed successfully.'));
    }
} 