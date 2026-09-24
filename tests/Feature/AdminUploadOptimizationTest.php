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
        view()->share('errors', new \Illuminate\Support\ViewErrorBag());
        Postgalery::whereIn('nama', ['Batik Parang Kencana', 'Artwork to remove', 'Karya Asli', 'Lukisan Ganti Foto', 'Lukisan Foto Baru', 'Karya Diperbarui'])->delete();
        Postinformasi::whereIn('deskripsi', ['Kegiatan pameran seni baru di Yogyakarta.', 'Informasi kegiatan yang akan dihapus.', 'Deskripsi baru yang sudah diedit.'])->delete();
    }

    protected function tearDown(): void
    {
        Postgalery::whereIn('nama', ['Batik Parang Kencana', 'Artwork to remove', 'Karya Asli', 'Lukisan Ganti Foto', 'Lukisan Foto Baru', 'Karya Diperbarui'])->delete();
        Postinformasi::whereIn('deskripsi', ['Kegiatan pameran seni baru di Yogyakarta.', 'Informasi kegiatan yang akan dihapus.', 'Deskripsi baru yang sudah diedit.'])->delete();
        parent::tearDown();
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

    public function test_postgalery_edit_view_and_update_without_new_image()
    {
        $image = UploadedFile::fake()->image('original_artwork.jpg', 1200, 800);
        $this->post(route('postsgalery.store'), [
            'image'   => $image,
            'nama'    => 'Karya Asli',
            'dimensi' => '80 x 60 cm',
            'link'    => 'https://example.com/original',
        ]);

        $post = Postgalery::where('nama', 'Karya Asli')->first();
        $this->assertNotNull($post);
        $originalFilename = $post->image;

        // Test edit view loads
        $editResponse = $this->get(route('postsgalery.edit', $post->id));
        $editResponse->assertStatus(200);
        $editResponse->assertSee('Karya Asli');

        // Test update without changing image
        $updateResponse = $this->put(route('postsgalery.update', $post->id), [
            'nama'    => 'Karya Diperbarui',
            'dimensi' => '90 x 70 cm',
            'link'    => 'https://example.com/updated',
        ]);
        $updateResponse->assertRedirect(route('postsgalery.index'));

        $post->refresh();
        $this->assertEquals('Karya Diperbarui', $post->nama);
        $this->assertEquals('90 x 70 cm', $post->dimensi);
        $this->assertEquals($originalFilename, $post->image); // image preserved
    }

    public function test_postgalery_update_with_new_image_optimizes_and_deletes_old()
    {
        $oldImage = UploadedFile::fake()->image('old_photo.jpg', 800, 600);
        $this->post(route('postsgalery.store'), [
            'image'   => $oldImage,
            'nama'    => 'Lukisan Ganti Foto',
            'dimensi' => '50 x 50 cm',
            'link'    => 'https://example.com/swap',
        ]);

        $post = Postgalery::where('nama', 'Lukisan Ganti Foto')->first();
        $oldFilename = $post->image;
        Storage::disk('public')->assertExists('postsimg/' . $oldFilename);

        // Upload replacement image
        $newImage = UploadedFile::fake()->image('replacement_photo.png', 1600, 1200);
        $updateResponse = $this->put(route('postsgalery.update', $post->id), [
            'image'   => $newImage,
            'nama'    => 'Lukisan Foto Baru',
            'dimensi' => '50 x 50 cm',
            'link'    => 'https://example.com/swap',
        ]);
        $updateResponse->assertRedirect(route('postsgalery.index'));

        $post->refresh();
        $this->assertEquals('Lukisan Foto Baru', $post->nama);
        $this->assertNotEquals($oldFilename, $post->image);
        $this->assertStringEndsWith('.webp', $post->image);

        // Assert old variants deleted and new exists
        Storage::disk('public')->assertMissing('postsimg/' . $oldFilename);
        Storage::disk('public')->assertExists('postsimg/' . $post->image);
    }

    public function test_postinformasi_edit_view_and_update()
    {
        $image = UploadedFile::fake()->image('news_init.jpg', 1200, 800);
        $this->post(route('postsinformasi.store'), [
            'image'     => $image,
            'deskripsi' => 'Deskripsi lama.',
        ]);
        $post = Postinformasi::latest()->first();

        // Edit view
        $editResp = $this->get(route('postsinformasi.edit', $post->id));
        $editResp->assertStatus(200);

        // Update
        $upResp = $this->put(route('postsinformasi.update', $post->id), [
            'deskripsi' => 'Deskripsi baru yang sudah diedit.',
        ]);
        $upResp->assertRedirect(route('postsinformasi.index'));

        $post->refresh();
        $this->assertEquals('Deskripsi baru yang sudah diedit.', $post->deskripsi);
    }

    public function test_posttestimoni_edit_view_and_update()
    {
        $image = UploadedFile::fake()->image('testi_orig.jpg', 600, 600);
        $this->post(route('posttestimoni.store'), ['image' => $image]);
        $post = Posttestimoni::latest()->first();

        // Edit view
        $editResp = $this->get(route('posttestimoni.edit', $post->id));
        $editResp->assertStatus(200);

        // Update with new image
        $newImage = UploadedFile::fake()->image('testi_new.png', 800, 800);
        $upResp = $this->put(route('posttestimoni.update', $post->id), ['image' => $newImage]);
        $upResp->assertRedirect(route('posttestimoni.index'));

        $post->refresh();
        $this->assertStringEndsWith('.webp', $post->image);
        $post->delete();
    }
}

