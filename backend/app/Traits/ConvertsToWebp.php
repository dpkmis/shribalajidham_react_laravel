<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait ConvertsToWebp
{
    /**
     * Store uploaded image as WebP format.
     * Returns the public path: /storage/folder/filename.webp
     */
    protected function storeAsWebp(UploadedFile $file, string $folder, int $quality = 80): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $baseName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $uniqueName = $baseName . '-' . Str::random(8) . '.webp';

        $image = match ($extension) {
            'jpg', 'jpeg' => @imagecreatefromjpeg($file->getPathname()),
            'png' => $this->createFromPngWithAlpha($file->getPathname()),
            'gif' => @imagecreatefromgif($file->getPathname()),
            'webp' => @imagecreatefromwebp($file->getPathname()),
            'bmp' => @imagecreatefrombmp($file->getPathname()),
            default => null,
        };

        // If GD can't process it (SVG, unsupported format), store as-is
        if (!$image) {
            $path = $file->store($folder, 'public');
            return '/storage/' . $path;
        }

        // Ensure storage directory exists
        $storagePath = storage_path('app/public/' . $folder);
        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        $fullPath = $storagePath . '/' . $uniqueName;
        imagewebp($image, $fullPath, $quality);
        imagedestroy($image);

        return '/storage/' . $folder . '/' . $uniqueName;
    }

    /**
     * Handle PNG with alpha transparency for WebP conversion.
     */
    private function createFromPngWithAlpha(string $path)
    {
        $png = @imagecreatefrompng($path);
        if (!$png) return null;

        $width = imagesx($png);
        $height = imagesy($png);

        // Create true color image with alpha
        $image = imagecreatetruecolor($width, $height);
        imagesavealpha($image, true);
        imagealphablending($image, false);

        $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
        imagefill($image, 0, 0, $transparent);
        imagecopy($image, $png, 0, 0, 0, 0, $width, $height);
        imagedestroy($png);

        return $image;
    }

    /**
     * Delete an old image from storage.
     */
    protected function deleteOldImage(?string $path): void
    {
        if (!$path) return;
        $storagePath = str_replace('/storage/', '', $path);
        Storage::disk('public')->delete($storagePath);
    }
}
