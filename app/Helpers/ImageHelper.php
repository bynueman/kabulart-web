<?php

namespace App\Helpers;

use App\Services\MediaPipeline;

class ImageHelper
{
    public const PRESETS = [
        'gallery' => [
            'sizes'  => '(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 380px',
            'aspect' => '1 / 1',
        ],
        'info' => [
            'sizes'  => '(max-width: 768px) 100vw, 360px',
            'aspect' => '16 / 10',
        ],
        'hero' => [
            'sizes'  => '(max-width: 768px) 100vw, 600px',
            'aspect' => '4 / 3',
        ],
        'profile' => [
            'sizes'  => '(max-width: 768px) 100vw, 400px',
            'aspect' => '4 / 3',
        ],
        'testimoni' => [
            'sizes'  => '(max-width: 640px) 100vw, 320px',
            'aspect' => '1 / 1',
        ],
        'full' => [
            'sizes'  => '100vw',
            'aspect' => 'auto',
        ],
    ];

    /**
     * Render optimized HTML <picture> markup with WebP source, srcset, dimensions, and lazy loading.
     *
     * @param string|null $filename
     * @param string $alt
     * @param string $class
     * @param string $preset
     * @param bool $eager
     * @param string $subfolder
     * @return string
     */
    public static function renderPicture(
        ?string $filename,
        string $alt = '',
        string $class = '',
        string $preset = 'gallery',
        bool $eager = false,
        string $subfolder = 'postsimg'
    ): string {
        $data = MediaPipeline::getPictureData($filename, $subfolder);
        $presetConfig = self::PRESETS[$preset] ?? self::PRESETS['gallery'];
        $sizesAttr = $presetConfig['sizes'];

        $altAttr = htmlspecialchars($alt, ENT_QUOTES, 'UTF-8');
        $classAttr = htmlspecialchars($class, ENT_QUOTES, 'UTF-8');

        $loading = $eager ? 'eager' : 'lazy';
        $decoding = $eager ? 'sync' : 'async';
        $priority = $eager ? ' fetchpriority="high"' : '';

        $w = (int) $data['width'];
        $h = (int) $data['height'];

        $sourceTags = '';
        if (!empty($data['srcset_webp'])) {
            $sourceTags .= "<source type=\"image/webp\" srcset=\"{$data['srcset_webp']}\" sizes=\"{$sizesAttr}\">";
        } elseif (!empty($data['webp_url'])) {
            $sourceTags .= "<source type=\"image/webp\" srcset=\"{$data['webp_url']}\">";
        }

        $fallbackUrl = htmlspecialchars($data['fallback_url'], ENT_QUOTES, 'UTF-8');

        return "<picture class=\"kg-picture {$classAttr}\">"
            . $sourceTags
            . "<img src=\"{$fallbackUrl}\" alt=\"{$altAttr}\" width=\"{$w}\" height=\"{$h}\" loading=\"{$loading}\" decoding=\"{$decoding}\"{$priority} class=\"{$classAttr}\" onerror=\"this.onerror=null;this.classList.add('is-broken');\">"
            . "</picture>";
    }
}
