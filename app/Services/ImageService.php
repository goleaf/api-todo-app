<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageService
{
    protected $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * Process and store an uploaded image.
     *
     * @param UploadedFile $file
     * @param string $path
     * @param int $width
     * @param int $height
     * @param bool $preserveAspectRatio
     * @return string The path to the stored image
     */
    public function processAndStoreImage(
        UploadedFile $file,
        string $path = 'uploads',
        int $width = 800,
        int $height = 600,
        bool $preserveAspectRatio = true
    ): string {
        // Generate a unique filename
        $filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
        
        // Create an Intervention Image instance from the uploaded file
        $image = $this->manager->read($file);
        
        // Resize the image
        if ($preserveAspectRatio) {
            $image->scaleDown(width: $width, height: $height);
        } else {
            $image->resize(width: $width, height: $height);
        }
        
        // Create full path
        $fullPath = $path . '/' . $filename;
        
        // Store the processed image
        Storage::put('public/' . $fullPath, $image->toJpeg());
        
        return $fullPath;
    }
    
    /**
     * Generate a thumbnail from an existing image.
     *
     * @param string $imagePath
     * @param string $thumbnailPath
     * @param int $width
     * @param int $height
     * @return string The path to the thumbnail
     */
    public function generateThumbnail(
        string $imagePath, 
        string $thumbnailPath = 'thumbnails',
        int $width = 200, 
        int $height = 200
    ): string {
        // Check if file exists
        if (!Storage::exists('public/' . $imagePath)) {
            throw new \Exception('Image does not exist.');
        }
        
        // Get the file contents
        $fileContents = Storage::get('public/' . $imagePath);
        
        // Create an Intervention Image instance
        $image = $this->manager->read($fileContents);
        
        // Get the original filename
        $filename = basename($imagePath);
        
        // Resize to thumbnail
        $image->cover(width: $width, height: $height);
        
        // Create full thumbnail path
        $fullPath = $thumbnailPath . '/' . $filename;
        
        // Store the thumbnail
        Storage::put('public/' . $fullPath, $image->toJpeg());
        
        return $fullPath;
    }
    
    /**
     * Apply a watermark to an image.
     *
     * @param string $imagePath
     * @param string $watermarkPath
     * @param string $position
     * @param int $opacity
     * @return string The path to the watermarked image
     */
    public function applyWatermark(
        string $imagePath,
        string $watermarkPath,
        string $position = 'bottom-right',
        int $opacity = 50
    ): string {
        // Check if files exist
        if (!Storage::exists('public/' . $imagePath)) {
            throw new \Exception('Image does not exist.');
        }
        
        if (!Storage::exists('public/' . $watermarkPath)) {
            throw new \Exception('Watermark does not exist.');
        }
        
        // Get the file contents
        $imageContents = Storage::get('public/' . $imagePath);
        $watermarkContents = Storage::get('public/' . $watermarkPath);
        
        // Create Intervention Image instances
        $image = $this->manager->read($imageContents);
        $watermark = $this->manager->read($watermarkContents);
        
        // Adjust opacity of watermark
        $watermark->opacity($opacity);
        
        // Position the watermark
        $x = 10;
        $y = 10;
        
        if ($position === 'top-right') {
            $x = $image->width() - $watermark->width() - 10;
            $y = 10;
        } elseif ($position === 'bottom-left') {
            $x = 10;
            $y = $image->height() - $watermark->height() - 10;
        } elseif ($position === 'bottom-right') {
            $x = $image->width() - $watermark->width() - 10;
            $y = $image->height() - $watermark->height() - 10;
        } elseif ($position === 'center') {
            $x = ($image->width() - $watermark->width()) / 2;
            $y = ($image->height() - $watermark->height()) / 2;
        }
        
        // Apply the watermark
        $image->place($watermark, 'top-left', x: $x, y: $y);
        
        // Create watermarked path
        $watermarkedPath = 'watermarked/' . basename($imagePath);
        
        // Store the watermarked image
        Storage::put('public/' . $watermarkedPath, $image->toJpeg());
        
        return $watermarkedPath;
    }
} 