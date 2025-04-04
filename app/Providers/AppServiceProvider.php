<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Add Blade directive for localized date
        Blade::directive('localizedDate', function ($expression) {
            return "<?php echo \App\Helpers\DateHelper::format($expression); ?>";
        });
        
        // Add Blade directive for localized date and time
        Blade::directive('localizedDateTime', function ($expression) {
            return "<?php echo \App\Helpers\DateHelper::formatDateTime($expression); ?>";
        });
        
        // Add Blade directive for relative time
        Blade::directive('diffForHumans', function ($expression) {
            return "<?php echo \App\Helpers\DateHelper::diffForHumans($expression); ?>";
        });
        
        // Add Blade directive for localized number
        Blade::directive('number', function ($expression) {
            return "<?php echo \App\Helpers\NumberHelper::format($expression); ?>";
        });
        
        // Add Blade directive for localized currency
        Blade::directive('currency', function ($expression) {
            return "<?php echo \App\Helpers\NumberHelper::formatCurrency($expression); ?>";
        });
        
        // Add Blade directive for localized percentage
        Blade::directive('percent', function ($expression) {
            return "<?php echo \App\Helpers\NumberHelper::formatPercent($expression); ?>";
        });
    }
}
