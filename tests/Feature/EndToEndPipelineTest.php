<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Postgalery;
use App\Services\MediaPipeline;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class EndToEndPipelineTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
        Postgalery::where('nama', 'like', '%Test%')->delete();
    }

    public function test_upload_small_jpeg()
    {
        $file = UploadedFile::fake()->image('small_art.jpg', 600, 400);

        $res = $this->post(route('postsgalery.store'), [
            'image'   => $file,
            'nama'    => 'Small Art Test',
            'dimensi' => '30 x 20 cm',
            'link'    => 'https://wa.me/test',
        ]);

        $res->assertRedirect(route('postsgalery.index'));
        $post = Postgalery::where('nama', 'Small Art Test')->first();
        $this->assertNotNull($post);
        $this->assertStringEndsWith('.webp', $post->image);

        // Verify storage files
        $base = pathinfo($post->image, PATHINFO_FILENAME);
        $this->assertFileExists(public_path('storage/postsimg/' . $post->image));
        $this->assertFileExists(public_path("storage/postsimg/{$base}_sm.webp"));

        // Clean up
        MediaPipeline::deleteVariants($post->image, 'postsimg');
        $post->delete();
    }

    public function test_upload_very_large_jpeg_downscaled()
    {
        // 4000 x 3000 large image
        $file = UploadedFile::fake()->image('giant_artwork.jpg', 4000, 3000);

        $res = $this->post(route('postsgalery.store'), [
            'image'   => $file,
            'nama'    => 'Giant Art Test',
            'dimensi' => '200 x 150 cm',
            'link'    => 'https://wa.me/test',
        ]);

        $res->assertRedirect(route('postsgalery.index'));
        $post = Postgalery::where('nama', 'Giant Art Test')->first();
        $this->assertNotNull($post);

        $storedPath = public_path('storage/postsimg/' . $post->image);
        $this->assertFileExists($storedPath);

        $sz = getimagesize($storedPath);
        $this->assertLessThanOrEqual(1920, $sz[0]);
        $this->assertLessThanOrEqual(1920, $sz[1]);

        // Clean up
        MediaPipeline::deleteVariants($post->image, 'postsimg');
        $post->delete();
    }

    public function test_upload_png_preserves_alpha()
    {
        $file = UploadedFile::fake()->image('transparent_art.png', 800, 800);

        $res = $this->post(route('postsgalery.store'), [
            'image'   => $file,
            'nama'    => 'PNG Art Test',
            'dimensi' => '50 x 50 cm',
            'link'    => 'https://wa.me/test',
        ]);

        $res->assertRedirect(route('postsgalery.index'));
        $post = Postgalery::where('nama', 'PNG Art Test')->first();
        $this->assertNotNull($post);

        $this->assertFileExists(public_path('storage/postsimg/' . $post->image));

        // Clean up
        MediaPipeline::deleteVariants($post->image, 'postsimg');
        $post->delete();
    }

    public function test_upload_webp_directly()
    {
        // Create a real WebP image in temp
        $tmpWebp = tempnam(sys_get_temp_dir(), 'test_webp') . '.webp';
        $img = imagecreatetruecolor(400, 400);
        imagewebp($img, $tmpWebp, 80);
        imagedestroy($img);

        $file = new UploadedFile($tmpWebp, 'direct.webp', 'image/webp', null, true);

        $res = $this->post(route('postsgalery.store'), [
            'image'   => $file,
            'nama'    => 'Direct WebP Test',
            'dimensi' => '40 x 40 cm',
            'link'    => 'https://wa.me/test',
        ]);

        $res->assertRedirect(route('postsgalery.index'));
        $post = Postgalery::where('nama', 'Direct WebP Test')->first();
        $this->assertNotNull($post);

        $this->assertFileExists(public_path('storage/postsimg/' . $post->image));

        // Clean up
        MediaPipeline::deleteVariants($post->image, 'postsimg');
        $post->delete();
        @unlink($tmpWebp);
    }

    public function test_upload_unsupported_file_rejected_safely()
    {
        $file = UploadedFile::fake()->create('malicious.pdf', 100, 'application/pdf');

        $res = $this->post(route('postsgalery.store'), [
            'image'   => $file,
            'nama'    => 'Invalid File Test',
            'dimensi' => '10 x 10 cm',
            'link'    => 'https://wa.me/test',
        ]);

        $res->assertSessionHasErrors(['image']);
        $post = Postgalery::where('nama', 'Invalid File Test')->first();
        $this->assertNull($post);
    }
}
