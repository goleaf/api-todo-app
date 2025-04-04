<?php

namespace App\Helpers;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;

class DateHelper
{
    /**
     * Format a date according to the current locale
     *
     * @param mixed $date The date to format (string, Carbon instance, or timestamp)
     * @param string|null $format Custom format (defaults to locale-specific short date format)
     * @return string
     */
    public static function format($date, ?string $format = null): string
    {
        if (empty($date)) {
            return '';
        }

        $carbon = self::carbonize($date);
        $locale = App::getLocale();
        
        // If no custom format, use the locale's short date format
        if (empty($format)) {
            $format = Lang::get('time.short_date');
        }
        
        // Set the locale for Carbon
        $carbon->locale($locale);
        
        return $carbon->format($format);
    }
    
    /**
     * Format a date and time according to the current locale
     *
     * @param mixed $date The date to format (string, Carbon instance, or timestamp)
     * @param string|null $format Custom format (defaults to locale-specific short datetime format)
     * @return string
     */
    public static function formatDateTime($date, ?string $format = null): string
    {
        if (empty($date)) {
            return '';
        }

        $carbon = self::carbonize($date);
        $locale = App::getLocale();
        
        // If no custom format, use the locale's short datetime format
        if (empty($format)) {
            $format = Lang::get('time.short_datetime');
        }
        
        // Set the locale for Carbon
        $carbon->locale($locale);
        
        return $carbon->format($format);
    }
    
    /**
     * Format a date as a human-readable relative time (e.g., "2 days ago")
     *
     * @param mixed $date The date to format (string, Carbon instance, or timestamp)
     * @return string
     */
    public static function diffForHumans($date): string
    {
        if (empty($date)) {
            return '';
        }

        $carbon = self::carbonize($date);
        $locale = App::getLocale();
        
        // Set the locale for Carbon
        $carbon->locale($locale);
        
        return $carbon->diffForHumans();
    }
    
    /**
     * Format a time duration in a human-readable format using locale-specific pluralization
     *
     * @param int $seconds Duration in seconds
     * @return string
     */
    public static function formatDuration(int $seconds): string
    {
        if ($seconds < 60) {
            return self::pluralizeTimeUnit('seconds', $seconds);
        }
        
        if ($seconds < 3600) {
            $minutes = floor($seconds / 60);
            return self::pluralizeTimeUnit('minutes', $minutes);
        }
        
        if ($seconds < 86400) {
            $hours = floor($seconds / 3600);
            return self::pluralizeTimeUnit('hours', $hours);
        }
        
        $days = floor($seconds / 86400);
        return self::pluralizeTimeUnit('days', $days);
    }
    
    /**
     * Helper method to apply pluralization rules based on locale
     *
     * @param string $unit Time unit (seconds, minutes, hours, days)
     * @param int $value Numeric value
     * @return string
     */
    protected static function pluralizeTimeUnit(string $unit, int $value): string
    {
        if ($value === 1) {
            return Lang::get("time.{$unit}", ['value' => $value]);
        }
        
        return Lang::choice("time.{$unit}", $value, ['value' => $value]);
    }
    
    /**
     * Convert various date formats to Carbon instance
     *
     * @param mixed $date Date in various formats
     * @return Carbon
     */
    protected static function carbonize($date): Carbon
    {
        if ($date instanceof Carbon) {
            return $date;
        }
        
        if (is_numeric($date)) {
            return Carbon::createFromTimestamp($date);
        }
        
        try {
            return Carbon::parse($date);
        } catch (\Exception $e) {
            return Carbon::now();
        }
    }

    /**
     * Format a date relative to now (e.g., "2 days ago")
     *
     * @param  mixed  $date  The date to format
     * @param  Carbon|null  $now  The reference date
     * @param  array  $options  Options to pass to Carbon's diffForHumans
     * @return string
     */
    public static function diffForHumans($date, $now = null, $options = [])
    {
        if (!$date) {
            return '';
        }

        if (!$date instanceof Carbon) {
            $date = Carbon::parse($date);
        }

        // Set the locale for the Carbon instance
        $date = $date->locale(App::getLocale());

        // Default options for diffForHumans
        $defaultOptions = [
            'parts' => 1,
            'join' => true,
            'short' => false,
            'syntax' => CarbonInterface::DIFF_ABSOLUTE
        ];

        $mergedOptions = array_merge($defaultOptions, $options);

        return $date->diffForHumans($now, $mergedOptions);
    }

    /**
     * Format a time span (e.g., "2 hours 30 minutes")
     *
     * @param  int  $seconds  Duration in seconds
     * @return string
     */
    public static function formatTimeSpan($seconds)
    {
        if ($seconds < 0) {
            return '';
        }

        // Create a Carbon instance with the current locale
        $carbon = Carbon::now()->locale(App::getLocale());
        
        // For durations less than 1 minute
        if ($seconds < 60) {
            return trans_choice('time.seconds', $seconds, ['value' => $seconds]);
        }
        
        // For durations less than 1 hour
        if ($seconds < 3600) {
            $minutes = floor($seconds / 60);
            $remainingSeconds = $seconds % 60;
            
            $result = trans_choice('time.minutes', $minutes, ['value' => $minutes]);
            
            if ($remainingSeconds > 0) {
                $result .= ' ' . trans_choice('time.seconds', $remainingSeconds, ['value' => $remainingSeconds]);
            }
            
            return $result;
        }
        
        // For durations of 1 hour or more
        $hours = floor($seconds / 3600);
        $remainingMinutes = floor(($seconds % 3600) / 60);
        
        $result = trans_choice('time.hours', $hours, ['value' => $hours]);
        
        if ($remainingMinutes > 0) {
            $result .= ' ' . trans_choice('time.minutes', $remainingMinutes, ['value' => $remainingMinutes]);
        }
        
        return $result;
    }

    /**
     * Get the localized date format based on the current locale
     *
     * @param  Carbon  $date  The date to format
     * @return string
     */
    protected static function getLocalizedDate(Carbon $date)
    {
        $locale = App::getLocale();
        
        // Use Carbon's built-in localization
        $date = $date->locale($locale);
        
        switch ($locale) {
            case 'ru':
                return $date->isoFormat('DD.MM.YYYY');
            case 'lt':
                return $date->isoFormat('YYYY-MM-DD');
            case 'fr':
                return $date->isoFormat('DD/MM/YYYY');
            case 'de':
                return $date->isoFormat('DD.MM.YYYY');
            case 'es':
                return $date->isoFormat('DD/MM/YYYY');
            case 'it':
                return $date->isoFormat('DD/MM/YYYY');
            case 'ja':
                return $date->isoFormat('YYYY年MM月DD日');
            default:
                return $date->isoFormat('MMM D, YYYY');
        }
    }

    /**
     * Get the localized date and time format based on the current locale
     *
     * @param  Carbon  $datetime  The datetime to format
     * @return string
     */
    protected static function getLocalizedDateTime(Carbon $datetime)
    {
        $locale = App::getLocale();
        
        // Use Carbon's built-in localization
        $datetime = $datetime->locale($locale);
        
        switch ($locale) {
            case 'ru':
                return $datetime->isoFormat('DD.MM.YYYY HH:mm');
            case 'lt':
                return $datetime->isoFormat('YYYY-MM-DD HH:mm');
            case 'fr':
                return $datetime->isoFormat('DD/MM/YYYY HH:mm');
            case 'de':
                return $datetime->isoFormat('DD.MM.YYYY HH:mm');
            case 'es':
                return $datetime->isoFormat('DD/MM/YYYY HH:mm');
            case 'it':
                return $datetime->isoFormat('DD/MM/YYYY HH:mm');
            case 'ja':
                return $datetime->isoFormat('YYYY年MM月DD日 HH:mm');
            default:
                return $datetime->isoFormat('MMM D, YYYY h:mm A');
        }
    }

    /**
     * Get the array of translated month names for the current locale
     *
     * @param bool $short Whether to get short month names
     * @return array
     */
    public static function getMonthNames($short = false)
    {
        $locale = app()->getLocale();
        $key = $short ? 'short_months' : 'months';
        
        // Check if translations exist for the current locale
        if (trans()->has("time.$key")) {
            return trans("time.$key");
        }
        
        // Fallback month names for English
        if ($short) {
            return [
                1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun',
                7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
            ];
        }
        
        return [
} 