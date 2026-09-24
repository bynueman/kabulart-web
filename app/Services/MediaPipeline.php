<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaPipeline
{
    public const MAX_FILE_SIZE = 15728640; // 15 MB
    public const MAX_DIMENSION = 6000;
    public const DISPLAY_MAX_WIDTH = 1920;
    public const DISPLAY_MAX_HEIGHT = 1920;
    public const WEBP_QUALITY = 82;
    public const JPEG_QUALITY = 85;
    public const AVIF_QUALITY = 65;

    public const SIZES = [
        'sm' => 360,
        'md' => 720,
        'lg' => 1280,
    ];

    public const ALLOWED_MIMES = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
        'image/avif' => 'avif',
    ];

    /**
     * Process an uploaded file, validate it, compress, resize, generate WebP/AVIF variants,
     * store to public disk, and return file details.
     *
     * @param UploadedFile $file
     * @param string $subfolder
     * @return array
     * @throws \InvalidArgumentException
     */
    public static function processUpload(UploadedFile $file, string $subfolder = 'postsimg'): array
    {
        if (!$file->isValid()) {
            throw new \InvalidArgumentException('Uploaded file is corrupted or incomplete.');
        }

        if ($file->getSize() > self::MAX_FILE_SIZE) {
            throw new \InvalidArgumentException('File exceeds maximum allowed size of 15MB.');
        }

        $realPath = $file->getRealPath();
        return self::processSourceFile($realPath, $file->getClientOriginalExtension(), $subfolder);
    }

    /**
     * Process a local image file (e.g. for batch migrations of existing assets).
     *
     * @param string $absolutePath
     * @param string $subfolder
     * @return array
     * @throws \InvalidArgumentException
     */
    public static function processFile(string $absolutePath, string $subfolder = 'postsimg'): array
    {
        if (!file_exists($absolutePath)) {
            throw new \InvalidArgumentException("Source file does not exist: {$absolutePath}");
        }

        $ext = pathinfo($absolutePath, PATHINFO_EXTENSION);
        return self::processSourceFile($absolutePath, $ext, $subfolder);
    }

    /**
     * Core processing method: validation, orientation fix, resizing, variant generation, storage.
     */
    protected static function processSourceFile(string $sourcePath, string $originalExt, string $subfolder): array
    {
        // 1. Validate MIME type using magic bytes (finfo)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $sourcePath);
        finfo_close($finfo);

        if (!array_key_exists($mime, self::ALLOWED_MIMES)) {
            throw new \InvalidArgumentException("Unsupported file type: {$mime}. Only JPEG, PNG, WebP, GIF, and AVIF are allowed.");
        }

        // 2. Read dimensions and sanity check
        $imageInfo = @getimagesize($sourcePath);
        if (!$imageInfo) {
            throw new \InvalidArgumentException('Failed to decode image data.');
        }

        $origW = $imageInfo[0];
        $origH = $imageInfo[1];

        if ($origW > self::MAX_DIMENSION || $origH > self::MAX_DIMENSION) {
            throw new \InvalidArgumentException("Image dimensions ({$origW}x{$origH}) exceed maximum limit of " . self::MAX_DIMENSION . "px.");
        }

        // 3. Create GD resource safely
        $sourceGd = self::createGdResource($sourcePath, $mime);
        if (!$sourceGd) {
            throw new \InvalidArgumentException('Could not create image resource from file.');
        }

        // 4. Fix EXIF orientation if JPEG
        if ($mime === 'image/jpeg' && function_exists('exif_read_data')) {
            $sourceGd = self::correctExifOrientation($sourcePath, $sourceGd);
            $origW = imagesx($sourceGd);
            $origH = imagesy($sourceGd);
        }

        // 5. Calculate display dimensions (downscale if > 1920px, never upscale)
        [$displayWidth, $displayHeight] = self::calculateTargetDimensions($origW, $origH, self::DISPLAY_MAX_WIDTH, self::DISPLAY_MAX_HEIGHT);

        $displayGd = self::resizeImage($sourceGd, $origW, $origH, $displayWidth, $displayHeight);

        // 6. Generate unique base filename
        $uniqueBase = Str::random(40);
        $hasAlpha = self::hasAlphaChannel($sourceGd, $mime);

        $disk = Storage::disk('public');
        $variants = [];
        $subfolder = trim($subfolder, '/\\');

        try {
            // Save original master file
            $safeOrigExt = strtolower($originalExt) ?: self::ALLOWED_MIMES[$mime];
            $origFilename = "{$uniqueBase}_orig.{$safeOrigExt}";
            $disk->put("{$subfolder}/{$origFilename}", file_get_contents($sourcePath));
            $variants['orig'] = $origFilename;

            // Generate main WebP (display resolution)
            $mainWebpFilename = "{$uniqueBase}.webp";
            $webpData = self::gdToBlob($displayGd, 'webp', self::WEBP_QUALITY);
            $disk->put("{$subfolder}/{$mainWebpFilename}", $webpData);
            $variants['webp_main'] = $mainWebpFilename;

            // Generate AVIF if supported
            if (function_exists('imageavif')) {
                $mainAvifFilename = "{$uniqueBase}.avif";
                $avifData = self::gdToBlob($displayGd, 'avif', self::AVIF_QUALITY);
                if ($avifData !== null) {
                    $disk->put("{$subfolder}/{$mainAvifFilename}", $avifData);
                    $variants['avif_main'] = $mainAvifFilename;
                }
            }

            // Generate universal fallback (JPEG or PNG if transparent)
            if ($hasAlpha) {
                $fallbackFilename = "{$uniqueBase}.png";
                $pngData = self::gdToBlob($displayGd, 'png');
                $disk->put("{$subfolder}/{$fallbackFilename}", $pngData);
            } else {
                $fallbackFilename = "{$uniqueBase}.jpg";
                $jpgData = self::gdToBlob($displayGd, 'jpeg', self::JPEG_QUALITY);
                $disk->put("{$subfolder}/{$fallbackFilename}", $jpgData);
            }
            $variants['fallback'] = $fallbackFilename;

            // Generate responsive downscaled WebP variants (sm: 360px, md: 720px, lg: 1280px)
            foreach (self::SIZES as $sizeKey => $maxDim) {
                if ($origW > $maxDim || $origH > $maxDim) {
                    [$varW, $varH] = self::calculateTargetDimensions($origW, $origH, $maxDim, $maxDim);
                    $varGd = self::resizeImage($sourceGd, $origW, $origH, $varW, $varH);
                    $varWebpName = "{$uniqueBase}_{$sizeKey}.webp";
                    $varData = self::gdToBlob($varGd, 'webp', self::WEBP_QUALITY);
                    $disk->put("{$subfolder}/{$varWebpName}", $varData);
                    $variants[$sizeKey] = $varWebpName;
                    imagedestroy($varGd);
                } else {
                    // Small enough already; use main display webp as that variant
                    $variants[$sizeKey] = $mainWebpFilename;
                }
            }

        } finally {
            if (is_resource($sourceGd) || $sourceGd instanceof \GdImage) {
                imagedestroy($sourceGd);
            }
            if (is_resource($displayGd) || $displayGd instanceof \GdImage) {
                imagedestroy($displayGd);
            }
        }

        $storedSize = strlen($webpData);

        return [
            'filename' => $mainWebpFilename,
            'base'     => $uniqueBase,
            'fallback' => $fallbackFilename,
            'variants' => $variants,
            'width'    => $displayWidth,
            'height'   => $displayHeight,
            'filesize' => $storedSize,
        ];
    }

    /**
     * Delete all variants associated with a given image filename.
     */
    public static function deleteVariants(string $filename, string $subfolder = 'postsimg'): void
    {
        $baseName = pathinfo($filename, PATHINFO_FILENAME);
        // Strip any existing suffix like _sm, _md, _lg, _orig
        $cleanBase = preg_replace('/_(sm|md|lg|orig)$/', '', $baseName);

        $disk = Storage::disk('public');
        $subfolder = trim($subfolder, '/\\');

        $allFiles = $disk->files($subfolder);
        foreach ($allFiles as $file) {
            $fName = basename($file);
            if (str_starts_with($fName, $cleanBase)) {
                $disk->delete($file);
            }
        }

        // Also clean up in public_path if stored directly
        $pubDir = public_path("storage/{$subfolder}");
        if (is_dir($pubDir)) {
            $scanned = @scandir($pubDir) ?: [];
            foreach ($scanned as $f) {
                if ($f === '.' || $f === '..') continue;
                if (str_starts_with($f, $cleanBase)) {
                    @unlink("{$pubDir}/{$f}");
                }
            }
        }
    }

    /**
     * Get picture metadata for rendering <picture> and <img> in Blade templates.
     */
    public static function getPictureData(?string $filename, string $subfolder = 'postsimg'): array
    {
        if (empty($filename)) {
            return [
                'has_image'    => false,
                'main_url'     => asset('img/desain.png'),
                'webp_url'     => null,
                'srcset_webp'  => null,
                'fallback_url' => asset('img/desain.png'),
                'width'        => 800,
                'height'       => 600,
            ];
        }

        $baseName = pathinfo($filename, PATHINFO_FILENAME);
        $cleanBase = preg_replace('/_(sm|md|lg|orig)$/', '', $baseName);
        $subfolder = trim($subfolder, '/\\');

        $baseUrl = asset("storage/{$subfolder}");
        $diskPath = public_path("storage/{$subfolder}");

        // Check if WebP variants exist
        $smExists = file_exists("{$diskPath}/{$cleanBase}_sm.webp");
        $mdExists = file_exists("{$diskPath}/{$cleanBase}_md.webp");
        $lgExists = file_exists("{$diskPath}/{$cleanBase}_lg.webp");
        $mainWebpExists = file_exists("{$diskPath}/{$cleanBase}.webp");

        $srcsetWebp = [];
        if ($smExists) $srcsetWebp[] = "{$baseUrl}/{$cleanBase}_sm.webp 360w";
        if ($mdExists) $srcsetWebp[] = "{$baseUrl}/{$cleanBase}_md.webp 720w";
        if ($lgExists) $srcsetWebp[] = "{$baseUrl}/{$cleanBase}_lg.webp 1280w";
        if ($mainWebpExists) $srcsetWebp[] = "{$baseUrl}/{$cleanBase}.webp 1920w";

        $mainWebpUrl = $mainWebpExists ? "{$baseUrl}/{$cleanBase}.webp" : null;

        // Fallback image url
        $jpgFallback = file_exists("{$diskPath}/{$cleanBase}.jpg") ? "{$baseUrl}/{$cleanBase}.jpg" : null;
        $pngFallback = file_exists("{$diskPath}/{$cleanBase}.png") ? "{$baseUrl}/{$cleanBase}.png" : null;
        $origFallback = file_exists("{$diskPath}/{$filename}") ? "{$baseUrl}/{$filename}" : null;

        $fallbackUrl = $mainWebpUrl ?: $jpgFallback ?: $pngFallback ?: $origFallback ?: asset('img/desain.png');

        // Dimensions detection
        $dims = [800, 600];
        $probeFile = "{$diskPath}/{$cleanBase}.webp";
        if (!file_exists($probeFile)) {
            $probeFile = "{$diskPath}/{$filename}";
        }
        if (file_exists($probeFile)) {
            $sz = @getimagesize($probeFile);
            if ($sz) {
                $dims = [$sz[0], $sz[1]];
            }
        }

        return [
            'has_image'    => true,
            'clean_base'   => $cleanBase,
            'main_url'     => $fallbackUrl,
            'webp_url'     => $mainWebpUrl,
            'srcset_webp'  => !empty($srcsetWebp) ? implode(', ', $srcsetWebp) : null,
            'fallback_url' => $fallbackUrl,
            'width'        => $dims[0],
            'height'       => $dims[1],
        ];
    }

    /**
     * Create GD resource from file path safely.
     */
    protected static function createGdResource(string $path, string $mime)
    {
        switch ($mime) {
            case 'image/jpeg':
                return @imagecreatefromjpeg($path);
            case 'image/png':
                return @imagecreatefrompng($path);
            case 'image/webp':
                return @imagecreatefromwebp($path);
            case 'image/gif':
                return @imagecreatefromgif($path);
            case 'image/avif':
                if (function_exists('imagecreatefromavif')) {
                    return @imagecreatefromavif($path);
                }
                return null;
            default:
                return null;
        }
    }

    /**
     * Check if an image resource has transparency.
     */
    protected static function hasAlphaChannel($image, string $mime): bool
    {
        if ($mime === 'image/jpeg') {
            return false;
        }
        return true;
    }

    /**
     * Resize image maintaining aspect ratio and preserving alpha channels.
     */
    protected static function resizeImage($source, int $srcW, int $srcH, int $targetW, int $targetH)
    {
        $target = imagecreatetruecolor($targetW, $targetH);

        // Alpha channel handling
        imagealphablending($target, false);
        imagesavealpha($target, true);
        $transparent = imagecolorallocatealpha($target, 0, 0, 0, 127);
        imagefilledrectangle($target, 0, 0, $targetW, $targetH, $transparent);

        imagecopyresampled($target, $source, 0, 0, 0, 0, $targetW, $targetH, $srcW, $srcH);

        return $target;
    }

    /**
     * Calculate downscaled dimensions without upscaling.
     */
    public static function calculateTargetDimensions(int $origW, int $origH, int $maxW, int $maxH): array
    {
        if ($origW <= $maxW && $origH <= $maxH) {
            return [$origW, $origH];
        }

        $ratio = min($maxW / $origW, $maxH / $origH);
        $newW = (int) max(1, round($origW * $ratio));
        $newH = (int) max(1, round($origH * $ratio));

        return [$newW, $newH];
    }

    /**
     * Correct EXIF orientation.
     */
    protected static function correctExifOrientation(string $path, $image)
    {
        $exif = @exif_read_data($path);
        if (!$exif || empty($exif['Orientation'])) {
            return $image;
        }

        switch ($exif['Orientation']) {
            case 3:
                $rotated = imagerotate($image, 180, 0);
                imagedestroy($image);
                return $rotated;
            case 6:
                $rotated = imagerotate($image, -90, 0);
                imagedestroy($image);
                return $rotated;
            case 8:
                $rotated = imagerotate($image, 90, 0);
                imagedestroy($image);
                return $rotated;
            default:
                return $image;
        }
    }

    /**
     * Convert GD resource to binary string.
     */
    protected static function gdToBlob($image, string $format, int $quality = 80): ?string
    {
        ob_start();
        switch ($format) {
            case 'webp':
                imagewebp($image, null, $quality);
                break;
            case 'jpeg':
            case 'jpg':
                imagejpeg($image, null, $quality);
                break;
            case 'png':
                imagepng($image, null, 6);
                break;
            case 'avif':
                if (function_exists('imageavif')) {
                    @imageavif($image, null, $quality);
                } else {
                    ob_end_clean();
                    return null;
                }
                break;
            default:
                ob_end_clean();
                return null;
        }
        $data = ob_get_clean();
        return $data ?: null;
    }
}
