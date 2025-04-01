<?php

namespace App\Helpers;

class RegexHelper
{
    /**
     * Check if a string is a valid email address
     *
     * @param string $email
     * @return bool
     */
    public static function isEmail(string $email): bool
    {
        return (bool) preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email);
    }

    /**
     * Check if a string is a valid URL
     *
     * @param string $url
     * @return bool
     */
    public static function isUrl(string $url): bool
    {
        return (bool) preg_match('/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/', $url);
    }

    /**
     * Check if a string is a valid IP address (IPv4 or IPv6)
     *
     * @param string $ip
     * @return bool
     */
    public static function isIp(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * Check if a string is a valid IPv4 address
     *
     * @param string $ip
     * @return bool
     */
    public static function isIpv4(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false;
    }

    /**
     * Check if a string is a valid IPv6 address
     *
     * @param string $ip
     * @return bool
     */
    public static function isIpv6(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false;
    }

    /**
     * Check if a string is a valid phone number
     * This is a basic implementation; adjust the regex pattern for your needs
     *
     * @param string $phone
     * @return bool
     */
    public static function isPhone(string $phone): bool
    {
        return (bool) preg_match('/^\+?[0-9]{10,15}$/', $phone);
    }

    /**
     * Check if a string is a valid UUID
     *
     * @param string $uuid
     * @return bool
     */
    public static function isUuid(string $uuid): bool
    {
        return (bool) preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $uuid);
    }

    /**
     * Check if a string is a valid date in the given format
     *
     * @param string $date
     * @param string $format
     * @return bool
     */
    public static function isDate(string $date, string $format = 'Y-m-d'): bool
    {
        $dt = \DateTime::createFromFormat($format, $date);
        return $dt && $dt->format($format) === $date;
    }

    /**
     * Extract all emails from a string
     *
     * @param string $string
     * @return array
     */
    public static function extractEmails(string $string): array
    {
        preg_match_all('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $string, $matches);
        return $matches[0] ?? [];
    }

    /**
     * Extract all URLs from a string
     *
     * @param string $string
     * @return array
     */
    public static function extractUrls(string $string): array
    {
        preg_match_all('/(https?:\/\/[^\s]+)/', $string, $matches);
        return $matches[0] ?? [];
    }

    /**
     * Extract all hashtags from a string
     *
     * @param string $string
     * @return array
     */
    public static function extractHashtags(string $string): array
    {
        preg_match_all('/#([a-zA-Z0-9_]+)/', $string, $matches);
        return $matches[0] ?? [];
    }

    /**
     * Extract all mentions (@username) from a string
     *
     * @param string $string
     * @return array
     */
    public static function extractMentions(string $string): array
    {
        preg_match_all('/@([a-zA-Z0-9_]+)/', $string, $matches);
        return $matches[0] ?? [];
    }

    /**
     * Strip all HTML tags from a string (better than strip_tags)
     *
     * @param string $string
     * @return string
     */
    public static function stripHtml(string $string): string
    {
        return preg_replace('/<.*?>/', '', $string);
    }

    /**
     * Validate password strength
     * Requires at least 8 characters, one uppercase, one lowercase, one number, and one special character
     *
     * @param string $password
     * @return bool
     */
    public static function isStrongPassword(string $password): bool
    {
        return (bool) preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password);
    }

    /**
     * Check if a string is a valid username
     * Alphanumeric, underscores, hyphens, 3-20 characters
     *
     * @param string $username
     * @return bool
     */
    public static function isValidUsername(string $username): bool
    {
        return (bool) preg_match('/^[a-zA-Z0-9_-]{3,20}$/', $username);
    }

    /**
     * Check if a string contains only alphanumeric characters
     *
     * @param string $string
     * @return bool
     */
    public static function isAlphanumeric(string $string): bool
    {
        return (bool) preg_match('/^[a-zA-Z0-9]+$/', $string);
    }

    /**
     * Check if a string contains only alphabetic characters
     *
     * @param string $string
     * @return bool
     */
    public static function isAlpha(string $string): bool
    {
        return (bool) preg_match('/^[a-zA-Z]+$/', $string);
    }

    /**
     * Check if a string contains only numeric characters
     *
     * @param string $string
     * @return bool
     */
    public static function isNumeric(string $string): bool
    {
        return (bool) preg_match('/^[0-9]+$/', $string);
    }

    /**
     * Format a string to be URL-friendly (slug)
     *
     * @param string $string
     * @return string
     */
    public static function slugify(string $string): string
    {
        // Convert to lowercase
        $string = strtolower($string);
        
        // Replace non-alphanumeric characters with hyphens
        $string = preg_replace('/[^a-z0-9]+/', '-', $string);
        
        // Remove leading and trailing hyphens
        return trim($string, '-');
    }

    /**
     * Extract data from a string using a regex pattern with named capture groups
     *
     * @param string $pattern
     * @param string $subject
     * @return array
     */
    public static function extractData(string $pattern, string $subject): array
    {
        if (preg_match($pattern, $subject, $matches)) {
            return array_filter($matches, fn($key) => !is_numeric($key), ARRAY_FILTER_USE_KEY);
        }
        
        return [];
    }

    /**
     * Check if a string is a valid hex color code
     *
     * @param string $color
     * @return bool
     */
    public static function isHexColor(string $color): bool
    {
        return (bool) preg_match('/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/', $color);
    }
} 