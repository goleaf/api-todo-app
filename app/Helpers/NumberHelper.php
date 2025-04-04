<?php

namespace App\Helpers;

use Illuminate\Support\Facades\App;

class NumberHelper
{
    /**
     * Format a number based on the current locale
     *
     * @param  mixed  $number  The number to format
     * @param  int  $decimals  The number of decimal places
     * @param  bool  $useGrouping  Whether to use thousands separator
     * @return string
     */
    public static function format($number, $decimals = 2, $useGrouping = true)
    {
        $locale = App::getLocale();
        
        // Get locale-specific formatting info
        $decimalPoint = self::getDecimalPoint($locale);
        $thousandsSep = self::getThousandsSeparator($locale);
        
        return number_format($number, $decimals, $decimalPoint, $useGrouping ? $thousandsSep : '');
    }

    /**
     * Format a currency value based on the current locale
     *
     * @param  mixed  $amount  The currency amount to format
     * @param  string  $currency  The ISO currency code (USD, EUR, etc.)
     * @param  int  $decimals  The number of decimal places
     * @return string
     */
    public static function formatCurrency($amount, $currency = null, $decimals = 2)
    {
        $locale = App::getLocale();
        
        // Get the currency symbol and format pattern
        $currencyCode = $currency ?: self::getDefaultCurrency($locale);
        $symbol = self::getCurrencySymbol($currencyCode);
        $pattern = self::getCurrencyPattern($locale);
        
        // Format the number
        $formattedNumber = self::format($amount, $decimals);
        
        // Replace placeholders in the pattern
        return str_replace(['¤', 'n'], [$symbol, $formattedNumber], $pattern);
    }

    /**
     * Format a percentage value based on the current locale
     *
     * @param  mixed  $value  The percentage value to format (0.1 for 10%)
     * @param  int  $decimals  The number of decimal places
     * @return string
     */
    public static function formatPercent($value, $decimals = 1)
    {
        $locale = App::getLocale();
        
        // Convert decimal to percentage (0.1 -> 10)
        $percentage = $value * 100;
        
        // Format the number
        $formattedNumber = self::format($percentage, $decimals);
        
        // Get the percentage symbol position
        $pattern = self::getPercentPattern($locale);
        
        // Replace placeholders in the pattern
        return str_replace('n', $formattedNumber, $pattern);
    }

    /**
     * Get the decimal point character for the given locale
     *
     * @param  string  $locale  The locale
     * @return string
     */
    protected static function getDecimalPoint($locale)
    {
        switch ($locale) {
            case 'ru':
            case 'de':
            case 'lt':
                return ',';
            default:
                return '.';
        }
    }

    /**
     * Get the thousands separator character for the given locale
     *
     * @param  string  $locale  The locale
     * @return string
     */
    protected static function getThousandsSeparator($locale)
    {
        switch ($locale) {
            case 'ru':
            case 'de':
            case 'lt':
                return ' ';
            case 'fr':
            case 'es':
            case 'it':
                return ' ';
            default:
                return ',';
        }
    }

    /**
     * Get the default currency for the given locale
     *
     * @param  string  $locale  The locale
     * @return string
     */
    protected static function getDefaultCurrency($locale)
    {
        switch ($locale) {
            case 'ru':
                return 'RUB';
            case 'lt':
                return 'EUR';
            case 'fr':
            case 'de':
            case 'es':
            case 'it':
                return 'EUR';
            case 'ja':
                return 'JPY';
            default:
                return 'USD';
        }
    }

    /**
     * Get the currency symbol for the given currency code
     *
     * @param  string  $currencyCode  The ISO currency code
     * @return string
     */
    protected static function getCurrencySymbol($currencyCode)
    {
        $symbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'JPY' => '¥',
            'RUB' => '₽',
        ];
        
        return $symbols[$currencyCode] ?? $currencyCode;
    }

    /**
     * Get the currency format pattern for the given locale
     *
     * @param  string  $locale  The locale
     * @return string
     */
    protected static function getCurrencyPattern($locale)
    {
        switch ($locale) {
            case 'ru':
            case 'lt':
                return 'n ¤';
            case 'de':
                return 'n ¤';
            case 'ja':
                return '¤n';
            default:
                return '¤n';
        }
    }

    /**
     * Get the percentage format pattern for the given locale
     *
     * @param  string  $locale  The locale
     * @return string
     */
    protected static function getPercentPattern($locale)
    {
        switch ($locale) {
            case 'fr':
                return 'n %';
            default:
                return 'n%';
        }
    }
} 