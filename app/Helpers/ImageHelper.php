<?php

namespace App\Helpers;

class ImageHelper
{
    /**
     * Resize and optimize image
     * 
     * @param string $sourcePath Full path to source image
     * @param string $destinationPath Full path to destination image
     * @param int $maxWidth Maximum width (default: 800)
     * @param int $maxHeight Maximum height (default: 800)
     * @param int $quality JPEG quality 0-100 (default: 85)
     * @return bool
     */
    public static function resizeImage($sourcePath, $destinationPath, $maxWidth = 800, $maxHeight = 800, $quality = 85)
    {
        if (!file_exists($sourcePath)) {
            return false;
        }

        // Get image info
        $imageInfo = getimagesize($sourcePath);
        if (!$imageInfo) {
            return false;
        }

        $originalWidth = $imageInfo[0];
        $originalHeight = $imageInfo[1];
        $mimeType = $imageInfo['mime'];

        // Calculate new dimensions
        $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
        $newWidth = (int)($originalWidth * $ratio);
        $newHeight = (int)($originalHeight * $ratio);

        // If image is smaller than max, just copy it
        if ($originalWidth <= $maxWidth && $originalHeight <= $maxHeight) {
            return copy($sourcePath, $destinationPath);
        }

        // Create image resource based on mime type
        switch ($mimeType) {
            case 'image/jpeg':
                $sourceImage = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $sourceImage = imagecreatefrompng($sourcePath);
                break;
            case 'image/gif':
                $sourceImage = imagecreatefromgif($sourcePath);
                break;
            case 'image/webp':
                if (function_exists('imagecreatefromwebp')) {
                    $sourceImage = imagecreatefromwebp($sourcePath);
                } else {
                    return copy($sourcePath, $destinationPath);
                }
                break;
            default:
                return copy($sourcePath, $destinationPath);
        }

        if (!$sourceImage) {
            return false;
        }

        // Create new image
        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve transparency for PNG and GIF
        if ($mimeType == 'image/png' || $mimeType == 'image/gif') {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
            imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
        }

        // Resize image
        imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

        // Create destination directory if not exists
        $destinationDir = dirname($destinationPath);
        if (!is_dir($destinationDir)) {
            mkdir($destinationDir, 0755, true);
        }

        // Save image
        $result = false;
        switch ($mimeType) {
            case 'image/jpeg':
                $result = imagejpeg($newImage, $destinationPath, $quality);
                break;
            case 'image/png':
                // PNG quality is 0-9, so convert from 0-100
                $pngQuality = (int)(9 - ($quality / 100) * 9);
                $result = imagepng($newImage, $destinationPath, $pngQuality);
                break;
            case 'image/gif':
                $result = imagegif($newImage, $destinationPath);
                break;
            case 'image/webp':
                if (function_exists('imagewebp')) {
                    $result = imagewebp($newImage, $destinationPath, $quality);
                }
                break;
        }

        // Clean up
        imagedestroy($sourceImage);
        imagedestroy($newImage);

        return $result;
    }
}

