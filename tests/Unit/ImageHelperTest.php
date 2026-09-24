<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Helpers\ImageHelper;
use App\Services\MediaPipeline;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageHelperTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_renders_picture_tag_with_lazy_loading()
    {
        $html = ImageHelper::renderPicture('nonexistent.png', 'Sample Artwork', 'art-img', 'gallery', false);

        $this->assertStringContainsString('<picture', $html);
        $this->assertStringContainsString('loading="lazy"', $html);
        $this->assertStringContainsString('decoding="async"', $html);
        $this->assertStringContainsString('width=', $html);
        $this->assertStringContainsString('height=', $html);
        $this->assertStringContainsString('alt="Sample Artwork"', $html);
    }

    public function test_renders_eager_for_hero_images()
    {
        $html = ImageHelper::renderPicture('sample.jpg', 'Hero Image', 'hero-img', 'hero', true);

        $this->assertStringContainsString('loading="eager"', $html);
        $this->assertStringContainsString('fetchpriority="high"', $html);
        $this->assertStringNotContainsString('loading="lazy"', $html);
    }
}
