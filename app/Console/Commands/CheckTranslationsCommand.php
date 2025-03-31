<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TranslationService;

class CheckTranslationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:check 
                           {--locale= : Target locale to check against reference}
                           {--reference=en : Reference locale}
                           {--fix : Generate missing translation files}
                           {--unused : Find unused translations}
                           {--json : Output results as JSON}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for missing or unused translations';

    /**
     * The translation service instance.
     *
     * @var \App\Services\TranslationService
     */
    protected $translationService;

    /**
     * Create a new command instance.
     *
     * @param TranslationService $translationService
     * @return void
     */
    public function __construct(TranslationService $translationService)
    {
        parent::__construct();
        $this->translationService = $translationService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $referenceLocale = $this->option('reference');
        $targetLocale = $this->option('locale');
        $fix = $this->option('fix');
        $findUnused = $this->option('unused');
        $outputJson = $this->option('json');

        // Show available locales
        $locales = $this->translationService->getLocales();
        
        if (empty($locales)) {
            $this->error('No locales found in resources/lang directory.');
            return 1;
        }

        $this->info('Available locales: ' . implode(', ', $locales));

        // Validate reference locale
        if (!in_array($referenceLocale, $locales)) {
            $this->error("Reference locale '{$referenceLocale}' not found.");
            return 1;
        }

        // Validate target locale if specified
        if ($targetLocale && !in_array($targetLocale, $locales)) {
            $this->error("Target locale '{$targetLocale}' not found.");
            return 1;
        }

        // Find missing translations
        $missing = $this->translationService->findMissingTranslations($referenceLocale, $targetLocale);
        
        if (empty($missing)) {
            $this->info('No missing translations found.');
        } else {
            $this->displayMissingTranslations($missing, $outputJson);
            
            if ($fix) {
                $this->fixMissingTranslations($missing);
            }
        }

        // Find unused translations
        if (!$findUnused){

        return 0;
    } 
            $this->info('Scanning for unused translations...');
            $unused = $this->translationService->findUnusedTranslations();
            
            if (empty($unused)) {
                $this->info('No unused translations found.');
            } else {
                $this->displayUnusedTranslations($unused, $outputJson);
            }
        

        return 0;
    }

    /**
     * Display missing translations.
     *
     * @param array $missing
     * @param bool $outputJson
     * @return void
     */
    protected function displayMissingTranslations(array $missing, bool $outputJson): void
    {
        if ($outputJson) {
            $this->line(json_encode($missing, JSON_PRETTY_PRINT));
            return;
        }

        $this->info('Missing translations:');
        
        foreach ($missing as $locale => $files) {
            $this->line("\n<fg=yellow>Locale: {$locale}</>");
            
            foreach ($files as $file => $keys) {
                $this->line("\n  <fg=blue>File: {$file}.php</>");
                
                foreach ($keys as $key => $value) {
                    $this->line("    {$key} => {$value}");
                }
            }
        }
    }

    /**
     * Display unused translations.
     *
     * @param array $unused
     * @param bool $outputJson
     * @return void
     */
    protected function displayUnusedTranslations(array $unused, bool $outputJson): void
    {
        if ($outputJson) {
            $this->line(json_encode($unused, JSON_PRETTY_PRINT));
            return;
        }

        $this->info('Unused translations:');
        
        foreach ($unused as $locale => $files) {
            $this->line("\n<fg=yellow>Locale: {$locale}</>");
            
            foreach ($files as $file => $keys) {
                $this->line("\n  <fg=blue>File: {$file}.php</>");
                $keyCount = count($keys);
                $this->line("  Found {$keyCount} unused key(s)");
                
                if ($keyCount <= 10) {
                    foreach ($keys as $key => $value) {
                        $this->line("    {$key} => {$value}");
                    }
                } else {
                    $this->line("    <fg=red>Too many keys to display. Use --json option for full output.</>");
                }
            }
        }
    }

    /**
     * Fix missing translations.
     *
     * @param array $missing
     * @return void
     */
    protected function fixMissingTranslations(array $missing): void
    {
        if ($this->confirm('Do you want to generate the missing translation files?')) {
            $this->translationService->generateMissingTranslationFiles($missing);
            $this->info('Missing translation files generated successfully.');
        }
    }
} 