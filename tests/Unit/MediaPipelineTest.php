<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\MediaPipeline;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaPipelineTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_rejects_non_image_file()
    {
        $this->expectException(\InvalidArgumentException::class);
        $file = UploadedFile::fake()->create('document.pdf', 500, 'application/pdf');
        MediaPipeline::processUpload($file, 'test_uploads');
    }

    public function test_processes_valid_jpeg_and_generates_variants()
    {
        $file = UploadedFile::fake()->image('test_artwork.jpg', 2400, 1600);
        $result = MediaPipeline::processUpload($file, 'test_uploads');

        $this->assertArrayHasKey('filename', $result);
        $this->assertArrayHasKey('variants', $result);
        $this->assertArrayHasKey('width', $result);
        $this->assertArrayHasKey('height', $result);

        // Check WebP main display image
        $webpPath = 'test_uploads/' . $result['filename'];
        Storage::disk('public')->assertExists($webpPath);

        // Check variants
        $baseName = pathinfo($result['filename'], PATHINFO_FILENAME);
        Storage::disk('public')->assertExists("test_uploads/{$baseName}_sm.webp");
        Storage::disk('public')->assertExists("test_uploads/{$baseName}_md.webp");
        Storage::disk('public')->assertExists("test_uploads/{$baseName}_lg.webp");
        Storage::disk('public')->assertExists("test_uploads/{$baseName}.jpg");
        Storage::disk('public')->assertExists("test_uploads/{$baseName}_orig.jpg");

        // Verify downscale: width should be at most 1920
        $this->assertLessThanOrEqual(1920, $result['width']);
    }

    public function test_does_not_upscale_small_images()
    {
        $file = UploadedFile::fake()->image('small_icon.png', 200, 150);
        $result = MediaPipeline::processUpload($file, 'test_uploads');

        $this->assertEquals(200, $result['width']);
        $this->assertEquals(150, $result['height']);
    }

    public function test_deletes_all_generated_variants()
    {
        $file = UploadedFile::fake()->image('to_delete.jpg', 800, 600);
        $result = MediaPipeline::processUpload($file, 'test_uploads');

        $baseName = pathinfo($result['filename'], PATHINFO_FILENAME);
        Storage::disk('public')->assertExists("test_uploads/{$result['filename']}");

        MediaPipeline::deleteVariants($result['filename'], 'test_uploads');

        Storage::disk('public')->assertMissing("test_uploads/{$result['filename']}");
        Storage::disk('public')->assertMissing("test_uploads/{$baseName}_sm.webp");
        Storage::disk('public')->assertMissing("test_uploads/{$baseName}_md.webp");
        Storage::disk('public')->assertMissing("test_uploads/{$baseName}_lg.webp");
    }
}
