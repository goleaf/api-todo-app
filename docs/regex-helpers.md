# Laravel Regex Helpers

This package provides a set of useful regular expression helpers for Laravel applications. The package includes a `RegexHelper` class and a Facade named `Regex` for convenient access to regex validation and extraction methods.

## Installation

The package has been integrated into the application. You can start using it immediately with the `Regex` facade.

## Available Methods

### Validation Methods

- `isEmail(string $email): bool` - Validates if the given string is a valid email address.
- `isUrl(string $url): bool` - Validates if the given string is a valid URL.
- `isIp(string $ip): bool` - Validates if the given string is a valid IP address (IPv4 or IPv6).
- `isIpv4(string $ip): bool` - Validates if the given string is a valid IPv4 address.
- `isIpv6(string $ip): bool` - Validates if the given string is a valid IPv6 address.
- `isPhone(string $phone): bool` - Validates if the given string is a valid phone number.
- `isUuid(string $uuid): bool` - Validates if the given string is a valid UUID.
- `isDate(string $date, string $format = 'Y-m-d'): bool` - Validates if the given string is a valid date in the specified format.
- `isStrongPassword(string $password): bool` - Validates if the given string is a strong password (at least 8 characters, with uppercase, lowercase, number, and special character).
- `isValidUsername(string $username): bool` - Validates if the given string is a valid username (alphanumeric, underscores, hyphens, 3-20 characters).
- `isAlphanumeric(string $string): bool` - Checks if a string contains only alphanumeric characters.
- `isAlpha(string $string): bool` - Checks if a string contains only alphabetic characters.
- `isNumeric(string $string): bool` - Checks if a string contains only numeric characters.
- `isHexColor(string $color): bool` - Validates if the given string is a valid hex color code.

### Extraction Methods

- `extractEmails(string $string): array` - Extracts all email addresses from a string.
- `extractUrls(string $string): array` - Extracts all URLs from a string.
- `extractHashtags(string $string): array` - Extracts all hashtags from a string.
- `extractMentions(string $string): array` - Extracts all mentions (@username) from a string.
- `extractData(string $pattern, string $subject): array` - Extracts data from a string using a regex pattern with named capture groups.

### Transformation Methods

- `stripHtml(string $string): string` - Strips all HTML tags from a string (better than strip_tags).
- `slugify(string $string): string` - Formats a string to be URL-friendly (slug).

## Usage Examples

```php
use App\Facades\Regex;

// Validation examples
if (Regex::isEmail('example@example.com')) {
    // This is a valid email
}

if (Regex::isUrl('https://example.com')) {
    // This is a valid URL
}

if (Regex::isStrongPassword('P@ssw0rd')) {
    // This is a strong password
}

// Extraction examples
$emails = Regex::extractEmails('Contact us at example@example.com or support@example.com');
// ['example@example.com', 'support@example.com']

$hashtags = Regex::extractHashtags('Check out #Laravel and #PHP for web development');
// ['#Laravel', '#PHP']

// Transformation examples
$slug = Regex::slugify('This is a test string!');
// 'this-is-a-test-string'

$cleanText = Regex::stripHtml('<p>This is <strong>HTML</strong> content</p>');
// 'This is HTML content'
```

## Custom Pattern Matching

You can use the `extractData` method to extract data using custom regex patterns with named capture groups:

```php
$text = 'Age: 30, Name: John Doe';
$pattern = '/Age: (?<age>\d+), Name: (?<name>[\w\s]+)/';

$data = Regex::extractData($pattern, $text);
// ['age' => '30', 'name' => 'John Doe']
```

## Integration with Laravel Validation

You can use these helpers in custom validation rules:

```php
use App\Facades\Regex;
use Illuminate\Support\Facades\Validator;

Validator::extend('username', function ($attribute, $value, $parameters, $validator) {
    return Regex::isValidUsername($value);
});

// In a controller
$validator = Validator::make($request->all(), [
    'username' => 'required|username',
]);
``` 