<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;
// Intervention validation rules commented out temporarily
// use Intervention\Validation\Rules\Base64;
// use Intervention\Validation\Rules\Creditcard;
// use Intervention\Validation\Rules\ColorHex;
// use Intervention\Validation\Rules\Hexadecimal;
// use Intervention\Validation\Rules\Iban;
// use Intervention\Validation\Rules\Isbn;
// use Intervention\Validation\Rules\Username;
// use Intervention\Validation\Rules\Bic;
// use Intervention\Validation\Rules\MacAddress;
// use Intervention\Validation\Rules\Domainname;
// use Intervention\Validation\Rules\HtmlClean;
// use Intervention\Validation\Rules\TaxId;

class ValidationService
{
    /**
     * Get additional validation rules for forms.
     *
     * @return array
     */
    public static function getCustomRules(): array
    {
        return [
            // Intervention validation rules commented out temporarily
            // 'base64' => new Base64(),
            // 'credit_card' => new Creditcard(),
            // 'color_hex' => new ColorHex(),
            // 'hexadecimal' => new Hexadecimal(),
            // 'iban' => new Iban(),
            // 'isbn' => new Isbn(),
            // 'username' => new Username(),
            // 'bic' => new Bic(),
            // 'mac_address' => new MacAddress(),
            // 'domain_name' => new Domainname(),
            // 'html_clean' => new HtmlClean(),
            // 'tax_id' => new TaxId(),
        ];
    }

    /**
     * Register custom validation rules globally.
     *
     * @return void
     */
    public static function registerCustomValidationRules(): void
    {
        $rules = self::getCustomRules();
        
        foreach ($rules as $name => $rule) {
            Validator::extend($name, function ($attribute, $value) use ($rule) {
                return $rule->passes($attribute, $value);
            });
        }
    }
    
    /**
     * Validate image dimensions.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param int $minWidth
     * @param int $minHeight
     * @param int|null $maxWidth
     * @param int|null $maxHeight
     * @param float|null $aspectRatio
     * @return bool
     */
    public static function validateImageDimensions(
        $file, 
        int $minWidth = 0, 
        int $minHeight = 0, 
        ?int $maxWidth = null, 
        ?int $maxHeight = null, 
        ?float $aspectRatio = null
    ): bool {
        if (!$file->isValid()) {
            return false;
        }
        
        // Get image dimensions
        list($width, $height) = getimagesize($file->getPathname());
        
        // Check minimum dimensions
        if ($width < $minWidth || $height < $minHeight) {
            return false;
        }
        
        // Check maximum dimensions if specified
        if (($maxWidth !== null && $width > $maxWidth) || 
            ($maxHeight !== null && $height > $maxHeight)) {
            return false;
        }
        
        // Check aspect ratio if specified
        if ($aspectRatio === null){
        
        return true;
    } 
            $currentRatio = $width / $height;
            $tolerance = 0.01; // 1% tolerance
            
            if (abs($currentRatio - $aspectRatio) > $tolerance) {
                return false;
            }
        
        
        return true;
    }
    
    /**
     * Validate file size.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param int $maxSizeInKb
     * @return bool
     */
    public static function validateFileSize($file, int $maxSizeInKb): bool
    {
        if (!$file->isValid()) {
            return false;
        }
        
        return $file->getSize() <= ($maxSizeInKb * 1024);
    }
} 