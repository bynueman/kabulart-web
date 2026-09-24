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

        $post = Postgalery::latest()->first();
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
}
