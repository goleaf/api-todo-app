<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use League\CommonMark\CommonMarkConverter;

class TermsController extends Controller
{
    /**
     * Display the terms of service page.
     */
    public function index(): View
    {
        $converter = new CommonMarkConverter();
        $markdownContent = Storage::disk('local')->get('terms.md');
        $htmlContent = $converter->convert($markdownContent);

        return view('pages.terms', compact('htmlContent'));
    }

    /**
     * Display the privacy policy page.
     */
    public function privacy(): View
    {
        $converter = new CommonMarkConverter();
        $markdownContent = Storage::disk('local')->get('privacy.md');
        $htmlContent = $converter->convert($markdownContent);

        return view('pages.privacy', compact('htmlContent'));
    }
} 