<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Postgalery;
use App\Models\Postinformasi;
use App\Models\Posttestimoni;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminUploadOptimizationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->withoutMiddleware();
        Postgalery::whereIn('nama', ['Batik Parang Kencana', 'Artwork to remove'])->delete();
    }

    public function test_postgalery_store_optimizes_image_and_saves_webp()
    {
        $image = UploadedFile::fake()->image('master_artwork.jpg', 2400, 1800);

        $response = $this->post(route('postsgalery.store'), [
            'image'   => $image,
            'nama'    => 'Batik Parang Kencana',
            'dimensi' => '120 x 80 cm',
            'link'    => 'https://wa.me/test',
        ]);

        $response->assertRedirect(route('postsgalery.index'));

        $post = Postgalery::latest()->first();
        $this->assertNotNull($post);
        $this->assertStringEndsWith('.webp', $post->image);

        // Verify files in storage
        Storage::disk('public')->assertExists('postsimg/' . $post->image);
        $base = pathinfo($post->image, PATHINFO_FILENAME);
        Storage::disk('public')->assertExists("postsimg/{$base}_sm.webp");
        Storage::disk('public')->assertExists("postsimg/{$base}_md.webp");
        Storage::disk('public')->assertExists("postsimg/{$base}_lg.webp");
    }

    public function test_postgalery_destroy_removes_all_variants()
    {
        $image = UploadedFile::fake()->image('to_remove.jpg', 1200, 900);
        $this->post(route('postsgalery.store'), [
            'image'   => $image,
            'nama'    => 'Artwork to remove',
            'dimensi' => '100 x 100 cm',
            'link'    => 'https://wa.me/test',
        ]);

        $post = Postgalery::where('nama', 'Artwork to remove')->first();
        $this->assertNotNull($post);
        $base = pathinfo($post->image, PATHINFO_FILENAME);

        Storage::disk('public')->assertExists('postsimg/' . $post->image);

        $response = $this->delete(route('postsgalery.destroy', $post->id));
        $response->assertRedirect(route('postsgalery.index'));

        // Assert all variants deleted
        Storage::disk('public')->assertMissing('postsimg/' . $post->image);
        Storage::disk('public')->assertMissing("postsimg/{$base}_sm.webp");
        Storage::disk('public')->assertMissing("postsimg/{$base}_md.webp");
        Storage::disk('public')->assertMissing("postsimg/{$base}_lg.webp");
    }

    public function test_postinformasi_store_and_destroy()
    {
        $image = UploadedFile::fake()->image('info_news.jpg', 1600, 1000);
        $response = $this->post(route('postsinformasi.store'), [
            'image'     => $image,
            'deskripsi' => 'Liputan pameran seni kontemporer.',
        ]);

        $response->assertRedirect(route('postsinformasi.index'));

        $post = Postinformasi::latest()->first();
        $this->assertNotNull($post);
        $this->assertStringEndsWith('.webp', $post->image);
        $base = pathinfo($post->image, PATHINFO_FILENAME);

        Storage::disk('public')->assertExists('postsimg/' . $post->image);

        // Delete
        $delResp = $this->delete(route('postsinformasi.destroy', $post->id));
        $delResp->assertRedirect(route('postsinformasi.index'));
        Storage::disk('public')->assertMissing('postsimg/' . $post->image);
    }

    public function test_posttestimoni_store_and_destroy()
    {
        $image = UploadedFile::fake()->image('testimoni_screenshot.png', 800, 800);
        $response = $this->post(route('posttestimoni.store'), [
            'image' => $image,
        ]);

        $response->assertRedirect(route('posttestimoni.index'));

        $post = Posttestimoni::latest()->first();
        $this->assertNotNull($post);
        $this->assertStringEndsWith('.webp', $post->image);

        Storage::disk('public')->assertExists('postsimg/' . $post->image);

        // Delete
        $delResp = $this->delete(route('posttestimoni.destroy', $post->id));
        $delResp->assertRedirect(route('posttestimoni.index'));
        Storage::disk('public')->assertMissing('postsimg/' . $post->image);
    }
}

