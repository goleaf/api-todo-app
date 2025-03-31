<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class TranslationService
{
    /**
     * Get all translation files for a locale.
     *
     * @param string $locale
     * @return array
     */
    public function getTranslationFiles(string $locale): array
    {
        $path = resource_path("lang/{$locale}");
        
        if (!File::exists($path)) {
            return [];
        }
        
        return File::files($path);
    }
    
    /**
     * Get all locales in the application.
     *
     * @return array
     */
    public function getLocales(): array
    {
        $path = resource_path('lang');
        
        return collect(File::directories($path))
            ->map(function ($directory) {
                return basename($directory);
            })
            ->toArray();
    }
    
    /**
     * Get all translations for a locale.
     *
     * @param string $locale
     * @return array
     */
    public function getTranslations(string $locale): array
    {
        $translations = [];
        $files = $this->getTranslationFiles($locale);
        
        foreach ($files as $file) {
            $filename = pathinfo($file, PATHINFO_FILENAME);
            $translations[$filename] = include $file;
        }
        
        return $translations;
    }
    
    /**
     * Find missing translations compared to a reference locale.
     *
     * @param string $referenceLocale
     * @param string|null $targetLocale
     * @return array
     */
    public function findMissingTranslations(string $referenceLocale = 'en', ?string $targetLocale = null): array
    {
        $locales = $targetLocale ? [$targetLocale] : $this->getLocales();
        $referenceTranslations = $this->getTranslations($referenceLocale);
        $missing = [];
        
        // Remove reference locale from locales to check
        $locales = array_filter($locales, function ($locale) use ($referenceLocale) {
            return $locale !== $referenceLocale;
        });
        
        foreach ($locales as $locale) {
            $translations = $this->getTranslations($locale);
            $missingInLocale = [];
            
            foreach ($referenceTranslations as $file => $keys) {
                if (!isset($translations[$file])) {
                    $missingInLocale[$file] = $this->flattenArray($keys);
                    continue;
                }
                
                $localeKeys = $translations[$file];
                $missingKeys = $this->findMissingKeys($keys, $localeKeys);
                
                if (!empty($missingKeys)) {
                    $missingInLocale[$file] = $missingKeys;
                }
            }
            
            if (!empty($missingInLocale)) {
                $missing[$locale] = $missingInLocale;
            }
        }
        
        return $missing;
    }
    
    /**
     * Find missing translation keys.
     *
     * @param array $reference
     * @param array $target
     * @param string $prefix
     * @return array
     */
    protected function findMissingKeys(array $reference, array $target, string $prefix = ''): array
    {
        $missing = [];
        
        foreach ($reference as $key => $value) {
            $currentKey = $prefix ? "{$prefix}.{$key}" : $key;
            
            if (is_array($value)) {
                if (!isset($target[$key]) || !is_array($target[$key])) {
                    $missing[$currentKey] = $this->flattenArray($value, $currentKey);
                } else {
                    $missingNested = $this->findMissingKeys($value, $target[$key], $currentKey);
                    $missing = array_merge($missing, $missingNested);
                }
            } elseif (!isset($target[$key])) {
                $missing[$currentKey] = $value;
            }
        }
        
        return $missing;
    }
    
    /**
     * Flatten a multi-dimensional array into a single level array.
     *
     * @param array $array
     * @param string $prefix
     * @return array
     */
    protected function flattenArray(array $array, string $prefix = ''): array
    {
        $result = [];
        
        foreach ($array as $key => $value) {
            $newKey = $prefix ? "{$prefix}.{$key}" : $key;
            
            if (is_array($value)) {
                $result = array_merge($result, $this->flattenArray($value, $newKey));
            } else {
                $result[$newKey] = $value;
            }
        }
        
        return $result;
    }
    
    /**
     * Generate missing translation files.
     *
     * @param array $missing
     * @return bool
     */
    public function generateMissingTranslationFiles(array $missing): bool
    {
        foreach ($missing as $locale => $files) {
            foreach ($files as $file => $keys) {
                $path = resource_path("lang/{$locale}/{$file}.php");
                $existing = [];
                
                if (File::exists($path)) {
                    $existing = include $path;
                }
                
                $newTranslations = $this->mergeKeysIntoArray($existing, $keys);
                $content = "<?php\n\nreturn " . $this->varExport($newTranslations) . ";\n";
                
                File::put($path, $content);
            }
        }
        
        return true;
    }
    
    /**
     * Merge keys into a multi-dimensional array.
     *
     * @param array $array
     * @param array $keys
     * @return array
     */
    protected function mergeKeysIntoArray(array $array, array $keys): array
    {
        foreach ($keys as $key => $value) {
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
        
        return $array;
    }
    
    /**
     * Export a variable as a string representation.
     *
     * @param mixed $var
     * @return string
     */
    protected function varExport($var): string
    {
        if (is_array($var)) {
            $indexed = array_keys($var) === range(0, count($var) - 1);
            $r = [];
            
            foreach ($var as $key => $value) {
                $r[] = $indexed
                    ? $this->varExport($value)
                    : "'" . addslashes($key) . "' => " . $this->varExport($value);
            }
            
            return "[\n    " . implode(",\n    ", $r) . "\n]";
        }
        
        if (is_string($var)) {
            return "'" . addslashes($var) . "'";
        }
        
        if (is_null($var)) {
            return 'null';
        }
        
        if (is_bool($var)) {
            return $var ? 'true' : 'false';
        }
        
        return $var;
    }
    
    /**
     * Find unused translations in all locales.
     *
     * @param array $usedKeys
     * @return array
     */
    public function findUnusedTranslations(array $usedKeys = []): array
    {
        $unused = [];
        $locales = $this->getLocales();
        
        if (empty($usedKeys)) {
            $usedKeys = $this->scanForUsedTranslationKeys();
        }
        
        foreach ($locales as $locale) {
            $translations = $this->getTranslations($locale);
            $unusedInLocale = [];
            
            foreach ($translations as $file => $keys) {
                $flatKeys = $this->flattenArray($keys);
                $unusedKeys = [];
                
                foreach ($flatKeys as $key => $value) {
                    $fullKey = "{$file}.{$key}";
                    
                    if (!in_array($fullKey, $usedKeys)) {
                        $unusedKeys[$key] = $value;
                    }
                }
                
                if (!empty($unusedKeys)) {
                    $unusedInLocale[$file] = $unusedKeys;
                }
            }
            
            if (!empty($unusedInLocale)) {
                $unused[$locale] = $unusedInLocale;
            }
        }
        
        return $unused;
    }
    
    /**
     * Scan project files for used translation keys.
     *
     * @return array
     */
    public function scanForUsedTranslationKeys(): array
    {
        $usedKeys = [];
        $directories = [
            app_path(),
            resource_path('views'),
        ];
        
        foreach ($directories as $directory) {
            $this->scanDirectory($directory, $usedKeys);
        }
        
        return $usedKeys;
    }
    
    /**
     * Scan a directory for translation keys.
     *
     * @param string $directory
     * @param array &$usedKeys
     * @return void
     */
    protected function scanDirectory(string $directory, array &$usedKeys): void
    {
        $files = File::allFiles($directory);
        
        foreach ($files as $file) {
            $content = file_get_contents($file);
            
            // Look for trans() and __() calls
            $this->findTransCalls($content, $usedKeys);
            $this->findTransShorthandCalls($content, $usedKeys);
            
            // Look for @lang() directive in Blade files
            if (Str::endsWith($file, '.blade.php')) {
                $this->findLangDirectives($content, $usedKeys);
            }
        }
    }
    
    /**
     * Find trans() calls in content.
     *
     * @param string $content
     * @param array &$usedKeys
     * @return void
     */
    protected function findTransCalls(string $content, array &$usedKeys): void
    {
        preg_match_all('/trans\(\s*[\'"]([^\'"]+)[\'"]\s*[\),]/', $content, $matches);
        
        if (!empty($matches[1])) {
            $usedKeys = array_merge($usedKeys, $matches[1]);
        }
    }
    
    /**
     * Find __() calls in content.
     *
     * @param string $content
     * @param array &$usedKeys
     * @return void
     */
    protected function findTransShorthandCalls(string $content, array &$usedKeys): void
    {
        preg_match_all('/__\(\s*[\'"]([^\'"]+)[\'"]\s*[\),]/', $content, $matches);
        
        if (!empty($matches[1])) {
            $usedKeys = array_merge($usedKeys, $matches[1]);
        }
    }
    
    /**
     * Find @lang() directives in Blade content.
     *
     * @param string $content
     * @param array &$usedKeys
     * @return void
     */
    protected function findLangDirectives(string $content, array &$usedKeys): void
    {
        preg_match_all('/@lang\(\s*[\'"]([^\'"]+)[\'"]\s*[\),]/', $content, $matches);
        
        if (!empty($matches[1])) {
            $usedKeys = array_merge($usedKeys, $matches[1]);
        }
    }
}