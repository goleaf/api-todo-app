<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UniqueValueService
{
    /**
     * Generate a unique slug for a model.
     *
     * @param Model $model The model instance to generate a slug for
     * @param string $field The source field to base the slug on
     * @param string $slugField The field to store the slug in
     * @param int $maxLength Maximum length for the slug
     * @param string $separator Separator to use in the slug
     * @return string The generated unique slug
     */
    public static function generateUniqueSlug(
        Model $model,
        string $field,
        string $slugField = 'slug',
        int $maxLength = 100,
        string $separator = '-'
    ): string {
        // Get the base slug from the source field
        $slug = Str::slug($model->$field, $separator);
        
        // Trim to max length, accounting for potential suffix
        $slug = Str::limit($slug, $maxLength - 10, '');
        
        // Make sure the slug isn't empty
        if (empty($slug)) {
            $slug = Str::slug(class_basename($model), $separator);
        }
        
        // Check for uniqueness
        $originalSlug = $slug;
        $i = 1;
        
        // Get model class name
        $modelClass = get_class($model);
        
        // Check if the slug is unique
        while ($modelClass::where($slugField, $slug)
                ->where($model->getKeyName(), '!=', $model->getKey())
                ->exists()) {
            $slug = $originalSlug . $separator . $i++;
            
            // If we're getting too long, truncate the original
            if (strlen($slug) > $maxLength) {
                $originalSlug = Str::limit($originalSlug, $maxLength - strlen($i) - 1, '');
                $slug = $originalSlug . $separator . $i;
            }
        }
        
        return $slug;
    }
    
    /**
     * Generate a unique reference code for a model.
     *
     * @param Model $model The model instance
     * @param string $field The field to store the reference in
     * @param string $prefix Optional prefix for the reference
     * @param int $length Length of the reference (excluding prefix)
     * @param bool $uppercase Whether to use uppercase letters
     * @return string The generated unique reference
     */
    public static function generateUniqueReference(
        Model $model,
        string $field = 'reference',
        string $prefix = '',
        int $length = 8,
        bool $uppercase = true
    ): string {
        $modelClass = get_class($model);
        $attempts = 0;
        $maxAttempts = 10;
        
        do {
            // Generate a random string
            $random = Str::random($length);
            
            // Convert to uppercase if needed
            if ($uppercase) {
                $random = strtoupper($random);
            }
            
            // Add prefix
            $reference = $prefix . $random;
            
            // Check if reference exists
            $exists = $modelClass::where($field, $reference)->exists();
            
            $attempts++;
            
            // If we've tried too many times, increase length
            if ($attempts >= $maxAttempts) {
                $length++;
                $attempts = 0;
            }
        } while ($exists);
        
        return $reference;
    }
    
    /**
     * Generate a unique incrementing number for a model with optional scope and prefix.
     *
     * @param Model $model The model instance
     * @param string $field The field to store the number in
     * @param array $scope Additional scope conditions (field => value)
     * @param string $prefix Optional prefix
     * @param int $startFrom Start numbering from
     * @param int $padLength Pad the number to this length
     * @return string The generated unique number
     */
    public static function generateUniqueIncrementingNumber(
        Model $model,
        string $field = 'number',
        array $scope = [],
        string $prefix = '',
        int $startFrom = 1,
        int $padLength = 4
    ): string {
        $modelClass = get_class($model);
        $query = $modelClass::query();
        
        // Add scope conditions
        foreach ($scope as $scopeField => $value) {
            $query->where($scopeField, $value);
        }
        
        // Find the highest current number
        $highestRecord = $query->orderByRaw("CAST(REGEXP_REPLACE($field, '[^0-9]', '') AS UNSIGNED) DESC")
            ->first();
        
        $nextNumber = $startFrom;
        
        if ($highestRecord) {
            // Extract the number part from the highest record
            preg_match('/(\d+)$/', $highestRecord->$field, $matches);
            if (isset($matches[1])) {
                $nextNumber = (int)$matches[1] + 1;
            }
        }
        
        // Pad the number
        $paddedNumber = str_pad($nextNumber, $padLength, '0', STR_PAD_LEFT);
        
        return $prefix . $paddedNumber;
    }
} 