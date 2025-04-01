<?php

namespace App\Facades;

use App\Helpers\RegexHelper;
use Illuminate\Support\Facades\Facade;

/**
 * @method static bool isEmail(string $email)
 * @method static bool isUrl(string $url)
 * @method static bool isIp(string $ip)
 * @method static bool isIpv4(string $ip)
 * @method static bool isIpv6(string $ip)
 * @method static bool isPhone(string $phone)
 * @method static bool isUuid(string $uuid)
 * @method static bool isDate(string $date, string $format = 'Y-m-d')
 * @method static array extractEmails(string $string)
 * @method static array extractUrls(string $string)
 * @method static array extractHashtags(string $string)
 * @method static array extractMentions(string $string)
 * @method static string stripHtml(string $string)
 * @method static bool isStrongPassword(string $password)
 * @method static bool isValidUsername(string $username)
 * @method static bool isAlphanumeric(string $string)
 * @method static bool isAlpha(string $string)
 * @method static bool isNumeric(string $string)
 * @method static string slugify(string $string)
 * @method static array extractData(string $pattern, string $subject)
 * @method static bool isHexColor(string $color)
 * 
 * @see \App\Helpers\RegexHelper
 */
class Regex extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'regex';
    }
} 