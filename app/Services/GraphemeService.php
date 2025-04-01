<?php

namespace App\Services;

use PHLAK\Twine\Str;

class GraphemeService
{
    /**
     * Get the length of a string in graphemes.
     *
     * @param string $input The input string
     * @return int
     */
    public static function length(string $input): int
    {
        return Str::make($input)->length();
    }

    /**
     * Truncate a string to a specified length, adding an ellipsis if truncated.
     *
     * @param string $input The input string
     * @param int $length The maximum length
     * @param string $suffix The suffix to add if truncated
     * @return string
     */
    public static function truncate(string $input, int $length, string $suffix = '...'): string
    {
        return Str::make($input)->truncate($length, $suffix);
    }

    /**
     * Get a substring from a string.
     *
     * @param string $input The input string
     * @param int $start The start position
     * @param int|null $length The length of the substring
     * @return string
     */
    public static function substring(string $input, int $start, ?int $length = null): string
    {
        $str = Str::make($input);
        
        if ($length !== null) {
            return $str->substring($start, $length);
        }
        
        return $str->substring($start);
    }

    /**
     * Pad a string to a certain length.
     *
     * @param string $input The input string
     * @param int $length The desired length
     * @param string $pad The string to pad with
     * @param int $type The pad type (STR_PAD_RIGHT, STR_PAD_LEFT, or STR_PAD_BOTH)
     * @return string
     */
    public static function pad(string $input, int $length, string $pad = ' ', int $type = STR_PAD_RIGHT): string
    {
        $str = Str::make($input);
        
        if ($type === STR_PAD_LEFT) {
            return $str->padLeft($length, $pad);
        } elseif ($type === STR_PAD_BOTH) {
            return $str->padBoth($length, $pad);
        }
        
        return $str->padRight($length, $pad);
    }

    /**
     * Convert a string to uppercase.
     *
     * @param string $input The input string
     * @return string
     */
    public static function upper(string $input): string
    {
        return Str::make($input)->uppercase();
    }

    /**
     * Convert a string to lowercase.
     *
     * @param string $input The input string
     * @return string
     */
    public static function lower(string $input): string
    {
        return Str::make($input)->lowercase();
    }

    /**
     * Convert a string to title case.
     *
     * @param string $input The input string
     * @return string
     */
    public static function title(string $input): string
    {
        return Str::make($input)->title();
    }

    /**
     * Limit a string to a specified number of words.
     *
     * @param string $input The input string
     * @param int $words The number of words to limit to
     * @param string $suffix The suffix to add if truncated
     * @return string
     */
    public static function words(string $input, int $words, string $suffix = '...'): string
    {
        return Str::make($input)->words($words, $suffix);
    }

    /**
     * Check if a string starts with a substring.
     *
     * @param string $input The input string
     * @param string $substring The substring to check for
     * @param bool $caseSensitive Whether the check should be case-sensitive
     * @return bool
     */
    public static function startsWith(string $input, string $substring, bool $caseSensitive = true): bool
    {
        $str = Str::make($input);
        
        if ($caseSensitive) {
            return $str->startsWith($substring);
        }
        
        return $str->lowercased()->startsWith(Str::make($substring)->lowercased());
    }

    /**
     * Check if a string ends with a substring.
     *
     * @param string $input The input string
     * @param string $substring The substring to check for
     * @param bool $caseSensitive Whether the check should be case-sensitive
     * @return bool
     */
    public static function endsWith(string $input, string $substring, bool $caseSensitive = true): bool
    {
        $str = Str::make($input);
        
        if ($caseSensitive) {
            return $str->endsWith($substring);
        }
        
        return $str->lowercased()->endsWith(Str::make($substring)->lowercased());
    }
} 