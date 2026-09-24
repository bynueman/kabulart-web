<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MediaPipeline;
use Illuminate\Support\Facades\Storage;

class OptimizeExistingImages extends Command
{
    protected $signature = 'media:optimize-existing {--dir=postsimg : Subfolder in storage/app/public} {--include-static : Also optimize static images in public/img}';
    protected $description = 'Batch optimize all existing images in storage and static folders to WebP and responsive variants without destroying originals.';

    public function handle(): int
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $subfolder = $this->option('dir');
        $includeStatic = $this->option('include-static');

        $storageDir = public_path("storage/{$subfolder}");

        if (!is_dir($storageDir)) {
            $this->error("Storage directory does not exist: {$storageDir}");
            return Command::FAILURE;
        }

        $this->info("Scanning {$storageDir} with 512MB memory limit...");

        $files = scandir($storageDir);
        $totalOriginalBytes = 0;
        $totalOptimizedBytes = 0;
        $processedCount = 0;
        $skippedCount = 0;
        $errorCount = 0;

        $reportTable = [];

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;

            $fullPath = "{$storageDir}/{$file}";
            if (!is_file($fullPath)) continue;

            // Skip existing generated variants
            if (preg_match('/_(sm|md|lg|orig)\.(webp|jpg|jpeg|png|avif)$/i', $file)) {
                $skippedCount++;
                continue;
            }

            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                continue;
            }

            $baseName = pathinfo($file, PATHINFO_FILENAME);
            $mainWebpPath = "{$storageDir}/{$baseName}.webp";
            $smWebpPath = "{$storageDir}/{$baseName}_sm.webp";

            $origBytes = filesize($fullPath);
            $totalOriginalBytes += $origBytes;

            // If already optimized, tally its existing size and skip re-encoding
            if (file_exists($mainWebpPath) && file_exists($smWebpPath)) {
                $webpBytes = filesize($mainWebpPath);
                $totalOptimizedBytes += $webpBytes;
                $processedCount++;
                $skippedCount++;
                continue;
            }

            $sz = @getimagesize($fullPath);
            $origDim = $sz ? "{$sz[0]}x{$sz[1]}" : "Unknown";

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $fullPath);
            finfo_close($finfo);

            try {
                $sourceGd = null;
                switch ($mime) {
                    case 'image/jpeg':
                        $sourceGd = @imagecreatefromjpeg($fullPath);
                        break;
                    case 'image/png':
                        $sourceGd = @imagecreatefrompng($fullPath);
                        break;
                    case 'image/webp':
                        $sourceGd = @imagecreatefromwebp($fullPath);
                        break;
                }

                if (!$sourceGd) {
                    $errorCount++;
                    $this->warn("Could not decode: {$file}");
                    continue;
                }

                $origW = imagesx($sourceGd);
                $origH = imagesy($sourceGd);

                // Fix orientation for JPEG
                if ($mime === 'image/jpeg' && function_exists('exif_read_data')) {
                    $exif = @exif_read_data($fullPath);
                    if ($exif && !empty($exif['Orientation'])) {
                        $rotAngle = 0;
                        switch ($exif['Orientation']) {
                            case 3: $rotAngle = 180; break;
                            case 6: $rotAngle = -90; break;
                            case 8: $rotAngle = 90; break;
                        }
                        if ($rotAngle !== 0) {
                            $rotated = imagerotate($sourceGd, $rotAngle, 0);
                            imagedestroy($sourceGd);
                            $sourceGd = $rotated;
                            $origW = imagesx($sourceGd);
                            $origH = imagesy($sourceGd);
                        }
                    }
                }

                // Downscale display if needed (max 1920)
                [$dispW, $dispH] = MediaPipeline::calculateTargetDimensions($origW, $origH, MediaPipeline::DISPLAY_MAX_WIDTH, MediaPipeline::DISPLAY_MAX_HEIGHT);

                // Create display version
                $dispGd = self::createResampled($sourceGd, $origW, $origH, $dispW, $dispH);
                $mainWebpPath = "{$storageDir}/{$baseName}.webp";
                imagewebp($dispGd, $mainWebpPath, MediaPipeline::WEBP_QUALITY);

                if (function_exists('imageavif')) {
                    $mainAvifPath = "{$storageDir}/{$baseName}.avif";
                    @imageavif($dispGd, $mainAvifPath, MediaPipeline::AVIF_QUALITY);
                }

                $webpBytes = filesize($mainWebpPath);
                $totalOptimizedBytes += $webpBytes;

                // Responsive variants
                foreach (MediaPipeline::SIZES as $sizeKey => $maxDim) {
                    $varPath = "{$storageDir}/{$baseName}_{$sizeKey}.webp";
                    if ($origW > $maxDim || $origH > $maxDim) {
                        [$vw, $vh] = MediaPipeline::calculateTargetDimensions($origW, $origH, $maxDim, $maxDim);
                        $varGd = self::createResampled($sourceGd, $origW, $origH, $vw, $vh);
                        imagewebp($varGd, $varPath, MediaPipeline::WEBP_QUALITY);
                        imagedestroy($varGd);
                    } else {
                        // Copy main display webp as size variant
                        copy($mainWebpPath, $varPath);
                    }
                }

                imagedestroy($dispGd);
                imagedestroy($sourceGd);
                gc_collect_cycles();

                $processedCount++;
                $reductionPct = round((1 - ($webpBytes / max(1, $origBytes))) * 100, 1);

                if (count($reportTable) < 15) {
                    $reportTable[] = [
                        'File'          => substr($file, 0, 24) . '...',
                        'Original'      => $origDim . ' (' . round($origBytes / 1024, 1) . ' KB)',
                        'WebP Display'  => "{$dispW}x{$dispH} (" . round($webpBytes / 1024, 1) . ' KB)',
                        'Reduction'     => "{$reductionPct}%",
                    ];
                }

            } catch (\Throwable $e) {
                $errorCount++;
                $this->error("Error processing {$file}: " . $e->getMessage());
            }
        }

        // If include-static, also process public/img
        if ($includeStatic) {
            $this->info("Processing static assets in public/img...");
            $staticDir = public_path('img');
            foreach (glob("{$staticDir}/*.{jpg,jpeg,png}", GLOB_BRACE) as $statFile) {
                $statBase = pathinfo($statFile, PATHINFO_FILENAME);
                $statExt = strtolower(pathinfo($statFile, PATHINFO_EXTENSION));
                $targetWebp = "{$staticDir}/{$statBase}.webp";

                $sGd = ($statExt === 'png') ? @imagecreatefrompng($statFile) : @imagecreatefromjpeg($statFile);
                if ($sGd) {
                    $sw = imagesx($sGd);
                    $sh = imagesy($sGd);
                    [$dw, $dh] = MediaPipeline::calculateTargetDimensions($sw, $sh, 1600, 1600);
                    $dGd = self::createResampled($sGd, $sw, $sh, $dw, $dh);
                    imagewebp($dGd, $targetWebp, 84);
                    imagedestroy($dGd);
                    imagedestroy($sGd);
                    $this->line("  Optimized static: {$statBase}.webp (" . round(filesize($targetWebp) / 1024, 1) . " KB)");
                }
            }
        }

        $this->newLine();
        $this->info("=== BATCH OPTIMIZATION COMPLETE ===");
        $this->table(['File', 'Original Size', 'WebP Display', 'Bandwidth Saved'], $reportTable);

        $origMb = round($totalOriginalBytes / 1048576, 2);
        $optMb = round($totalOptimizedBytes / 1048576, 2);
        $savedMb = round(($totalOriginalBytes - $totalOptimizedBytes) / 1048576, 2);
        $totalReduction = round((1 - ($totalOptimizedBytes / max(1, $totalOriginalBytes))) * 100, 1);

        $this->newLine();
        $this->info("Total Master Images Processed: {$processedCount}");
        $this->info("Variants Skipped: {$skippedCount}");
        $this->info("Original Storage Size: {$origMb} MB");
        $this->info("Optimized Display Size: {$optMb} MB");
        $this->info("Total Bandwidth Saved: {$savedMb} MB ({$totalReduction}% reduction!)");

        return Command::SUCCESS;
    }

    protected static function createResampled($source, int $srcW, int $srcH, int $targetW, int $targetH)
    {
        $target = imagecreatetruecolor($targetW, $targetH);
        imagealphablending($target, false);
        imagesavealpha($target, true);
        $transparent = imagecolorallocatealpha($target, 0, 0, 0, 127);
        imagefilledrectangle($target, 0, 0, $targetW, $targetH, $transparent);
        imagecopyresampled($target, $source, 0, 0, 0, 0, $targetW, $targetH, $srcW, $srcH);
        return $target;
    }
}
