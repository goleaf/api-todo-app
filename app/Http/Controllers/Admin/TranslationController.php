<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class TranslationController extends Controller
{
    /**
     * The translation service instance.
     *
     * @var \App\Services\TranslationService
     */
    protected $translationService;
    
    /**
     * Create a new controller instance.
     *
     * @param TranslationService $translationService
     * @return void
     */
    public function __construct(TranslationService $translationService)
    {
        $this->translationService = $translationService;
    }
    
    /**
     * Display a listing of the translations.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $locales = $this->translationService->getLocales();
        
        if (empty($locales)) {
            return view('admin.translations.index', [
                'locales' => [],
                'message' => 'No locales found in resources/lang directory.'
            ]);
        }
        
        return view('admin.translations.index', [
            'locales' => $locales,
        ]);
    }
    
    /**
     * Show the missing translations.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function missing(Request $request)
    {
        $referenceLocale = $request->input('reference', 'en');
        $targetLocale = $request->input('locale');
        
        $locales = $this->translationService->getLocales();
        
        if (!in_array($referenceLocale, $locales)) {
            return redirect()->route('admin.translations.index')
                ->with('error', "Reference locale '{$referenceLocale}' not found.");
        }
        
        if ($targetLocale && !in_array($targetLocale, $locales)) {
            return redirect()->route('admin.translations.index')
                ->with('error', "Target locale '{$targetLocale}' not found.");
        }
        
        $missing = $this->translationService->findMissingTranslations($referenceLocale, $targetLocale);
        
        return view('admin.translations.missing', [
            'missing' => $missing,
            'referenceLocale' => $referenceLocale,
            'targetLocale' => $targetLocale,
            'locales' => $locales,
        ]);
    }
    
    /**
     * Show the unused translations.
     *
     * @return \Illuminate\Http\Response
     */
    public function unused()
    {
        $unused = $this->translationService->findUnusedTranslations();
        
        return view('admin.translations.unused', [
            'unused' => $unused,
        ]);
    }
    
    /**
     * Fix missing translations.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function fix(Request $request)
    {
        $missing = $request->session()->get('missing_translations');
        
        if (empty($missing)) {
            $referenceLocale = $request->input('reference', 'en');
            $targetLocale = $request->input('locale');
            
            $missing = $this->translationService->findMissingTranslations($referenceLocale, $targetLocale);
        }
        
        if (empty($missing)) {
            return redirect()->route('admin.translations.index')
                ->with('info', 'No missing translations to fix.');
        }
        
        $this->translationService->generateMissingTranslationFiles($missing);
        
        return redirect()->route('admin.translations.index')
            ->with('success', 'Missing translation files generated successfully.');
    }
    
    /**
     * Show translation editor for a specific locale and file.
     *
     * @param string $locale
     * @param string|null $file
     * @return \Illuminate\Http\Response
     */
    public function edit($locale, $file = null)
    {
        $locales = $this->translationService->getLocales();
        
        if (!in_array($locale, $locales)) {
            return redirect()->route('admin.translations.index')
                ->with('error', "Locale '{$locale}' not found.");
        }
        
        $files = $this->translationService->getTranslationFiles($locale);
        $fileList = collect($files)->map(function ($file) {
            return pathinfo($file, PATHINFO_FILENAME);
        })->toArray();
        
        if ($file === null && !empty($fileList)) {
            $file = $fileList[0];
        }
        
        if ($file === null) {
            return view('admin.translations.form', [
                'locale' => $locale,
                'fileList' => $fileList,
                'currentFile' => null,
                'translations' => [],
            ]);
        }
        
        $translations = $this->translationService->getTranslations($locale);
        
        if (!isset($translations[$file])) {
            return redirect()->route('admin.translations.index')
                ->with('error', "Translation file '{$file}' not found for locale '{$locale}'.");
        }
        
        // Flatten translations for easier editing
        $flatTranslations = $this->flattenTranslations($translations[$file]);
        
        return view('admin.translations.form', [
            'locale' => $locale,
            'fileList' => $fileList,
            'currentFile' => $file,
            'translations' => $flatTranslations,
        ]);
    }
    
    /**
     * Show form to create a new translation.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.translations.form');
    }
    
    /**
     * Store a newly created translation.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'locale' => 'required|string|min:2|max:5',
            'file' => 'required|string|min:1|max:50',
            'content' => 'required|json',
        ]);
        
        $locale = $request->input('locale');
        $file = $request->input('file');
        $content = json_decode($request->input('content'), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Invalid JSON format in content field.');
        }
        
        $directory = resource_path("lang/{$locale}");
        
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }
        
        $filePath = "{$directory}/{$file}.php";
        
        // Check if file already exists
        if (File::exists($filePath)) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Translation file '{$file}.php' already exists for locale '{$locale}'.");
        }
        
        // Generate PHP content
        $phpContent = "<?php\n\nreturn " . var_export($content, true) . ";\n";
        
        // Save file
        File::put($filePath, $phpContent);
        
        return redirect()->route('admin.translations.edit', ['locale' => $locale, 'file' => $file])
            ->with('success', "Translation file '{$file}.php' created successfully for locale '{$locale}'.");
    }
    
    /**
     * Update translations.
     *
     * @param Request $request
     * @param string $locale
     * @param string $file
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $locale, $file)
    {
        $translations = $request->input('translations');
        
        if (empty($translations)) {
            return redirect()->route('admin.translations.edit', ['locale' => $locale, 'file' => $file])
                ->with('error', 'No translations to update.');
        }
        
        $existingTranslations = $this->translationService->getTranslations($locale);
        
        if (!isset($existingTranslations[$file])) {
            return redirect()->route('admin.translations.index')
                ->with('error', "Translation file '{$file}' not found for locale '{$locale}'.");
        }
        
        // Reconstruct nested array from flattened keys
        $updatedTranslations = [];
        
        foreach ($translations as $key => $value) {
            $this->setNestedValue($updatedTranslations, $key, $value);
        }
        
        // Generate PHP file content
        $content = "<?php\n\nreturn " . $this->translationService->varExport($updatedTranslations) . ";\n";
        
        // Write to file
        $path = resource_path("lang/{$locale}/{$file}.php");
        File::put($path, $content);
        
        return redirect()->route('admin.translations.edit', ['locale' => $locale, 'file' => $file])
            ->with('success', 'Translations updated successfully.');
    }
    
    /**
     * Flatten a nested translations array.
     *
     * @param array $translations
     * @param string $prefix
     * @return array
     */
    protected function flattenTranslations(array $translations, string $prefix = ''): array
    {
        $result = [];
        
        foreach ($translations as $key => $value) {
            $newKey = $prefix ? "{$prefix}.{$key}" : $key;
            
            if (is_array($value)) {
                $result = array_merge($result, $this->flattenTranslations($value, $newKey));
            } else {
                $result[$newKey] = $value;
            }
        }
        
        return $result;
    }
    
    /**
     * Set a nested value in an array using dot notation.
     *
     * @param array &$array
     * @param string $key
     * @param mixed $value
     * @return void
     */
    protected function setNestedValue(array &$array, string $key, $value): void
    {
        if (Str::contains($key, '.')) {
            $parts = explode('.', $key);
            $lastKey = array_pop($parts);
            $current = &$array;
            
            foreach ($parts as $part) {
                if (!isset($current[$part]) || !is_array($current[$part])) {
                    $current[$part] = [];
                }
                
                $current = &$current[$part];
            }
            
            $current[$lastKey] = $value;
        } else {
            $array[$key] = $value;
        }
    }
} 