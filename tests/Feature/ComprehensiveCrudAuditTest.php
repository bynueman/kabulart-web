<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Postgalery;
use App\Models\Postinformasi;
use App\Models\Posttestimoni;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ComprehensiveCrudAuditTest extends TestCase
{
    protected User $adminUser;

    protected array $baselineTestimoniIds = [];

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        
        $this->adminUser = User::first() ?? User::create([
            'name'     => 'Audit Admin',
            'email'    => 'audit_admin@example.local',
            'password' => Hash::make('secret123'),
        ]);

        $this->baselineTestimoniIds = Posttestimoni::pluck('id')->toArray();
        $this->cleanupTestRecords();
    }

    protected function tearDown(): void
    {
        $this->cleanupTestRecords();
        parent::tearDown();
    }

    protected function cleanupTestRecords(): void
    {
        Postgalery::where('nama', 'like', 'AUDIT_%')->delete();
        Postinformasi::where('deskripsi', 'like', 'AUDIT_%')->delete();
        if (!empty($this->baselineTestimoniIds)) {
            Posttestimoni::whereNotIn('id', $this->baselineTestimoniIds)->delete();
        }
    }

    /**
     * SECTION 2: Authentication Audit
     */
    public function test_auth_unauthenticated_restrictions_and_flow(): void
    {
        // 1. Unauthenticated access blocked
        $routes = ['/homeadmin', '/postsinformasi', '/postsgalery', '/posttestimoni'];
        foreach ($routes as $route) {
            $resp = $this->get($route);
            $resp->assertRedirect('/adminpanel');
        }

        // 2. Invalid login fails gracefully
        $invalidResp = $this->post(route('login.action'), [
            'email'    => 'wrong_email@example.com',
            'password' => 'wrong_password',
        ]);
        $invalidResp->assertRedirect();
        $invalidResp->assertSessionHasErrors(['password']);
        $this->assertFalse(Auth::check());

        // 3. Valid login succeeds, regenerates session, and redirects to /homeadmin
        $validResp = $this->actingAs($this->adminUser)->get('/homeadmin');
        $validResp->assertStatus(200);
        $validResp->assertHeader('Cache-Control');
        $this->assertStringContainsString('no-store', $validResp->headers->get('Cache-Control'));

        // 4. Authenticated user cannot access /adminpanel (redirected to /homeadmin)
        $adminPanelResp = $this->actingAs($this->adminUser)->get('/adminpanel');
        $adminPanelResp->assertRedirect('/homeadmin');

        // 5. Logout invalidates session and redirects to /
        $logoutResp = $this->actingAs($this->adminUser)->get('/logout');
        $logoutResp->assertRedirect('/');
        $this->assertFalse(Auth::check());
    }

    /**
     * SECTION 3: Informasi CRUD Audit
     */
    public function test_informasi_crud_lifecycle_and_validation(): void
    {
        $user = $this->adminUser;

        // CREATE: Required fields validation
        $valResp = $this->actingAs($user)->post(route('postsinformasi.store'), []);
        $valResp->assertSessionHasErrors(['image', 'deskripsi']);

        // CREATE: Valid record with image
        $image = UploadedFile::fake()->image('info_audit.jpg', 1200, 800);
        $createResp = $this->actingAs($user)->post(route('postsinformasi.store'), [
            'image'     => $image,
            'deskripsi' => 'AUDIT_INFORMASI_INITIAL_CONTENT',
        ]);
        $createResp->assertRedirect(route('postsinformasi.index'));
        $createResp->assertSessionHas('success');

        $record = Postinformasi::where('deskripsi', 'AUDIT_INFORMASI_INITIAL_CONTENT')->first();
        $this->assertNotNull($record);
        $initialFilename = $record->image;
        Storage::disk('public')->assertExists('postsimg/' . $initialFilename);

        // READ: Admin index and Public index
        $adminList = $this->actingAs($user)->get(route('postsinformasi.index'));
        $adminList->assertStatus(200);
        $adminList->assertSee('AUDIT_INFORMASI_INITIAL_CONTENT');

        $publicList = $this->get('/informasi');
        $publicList->assertStatus(200);
        $publicList->assertSee('AUDIT_INFORMASI_INITIAL_CONTENT');

        // UPDATE: Text only without replacing image
        $updateResp1 = $this->actingAs($user)->put(route('postsinformasi.update', $record->id), [
            'deskripsi' => 'AUDIT_INFORMASI_UPDATED_TEXT_ONLY',
        ]);
        $updateResp1->assertRedirect(route('postsinformasi.index'));
        $record->refresh();
        $this->assertEquals('AUDIT_INFORMASI_UPDATED_TEXT_ONLY', $record->deskripsi);
        $this->assertEquals($initialFilename, $record->image); // image preserved
        Storage::disk('public')->assertExists('postsimg/' . $initialFilename);

        // UPDATE: Replace image
        $newImage = UploadedFile::fake()->image('info_audit_new.png', 1000, 700);
        $updateResp2 = $this->actingAs($user)->put(route('postsinformasi.update', $record->id), [
            'image'     => $newImage,
            'deskripsi' => 'AUDIT_INFORMASI_WITH_NEW_IMAGE',
        ]);
        $updateResp2->assertRedirect(route('postsinformasi.index'));
        $record->refresh();
        $this->assertNotEquals($initialFilename, $record->image);
        Storage::disk('public')->assertMissing('postsimg/' . $initialFilename);
        Storage::disk('public')->assertExists('postsimg/' . $record->image);

        // DELETE: Successfully removes record and media
        $currentFilename = $record->image;
        $deleteResp = $this->actingAs($user)->delete(route('postsinformasi.destroy', $record->id));
        $deleteResp->assertRedirect(route('postsinformasi.index'));
        $this->assertNull(Postinformasi::find($record->id));
        Storage::disk('public')->assertMissing('postsimg/' . $currentFilename);

        // INVALID ID: Return 404
        $notFoundResp = $this->actingAs($user)->get('/postsinformasi/999999/edit');
        $notFoundResp->assertStatus(404);
    }

    /**
     * SECTION 4: Gallery CRUD Audit & Update Matrix (A through E)
     */
    public function test_gallery_crud_and_update_matrix(): void
    {
        $user = $this->adminUser;

        // Neighboring file that must NEVER be touched
        Storage::disk('public')->put('postsimg/neighbor_artwork.webp', 'untouchable');

        // CREATE
        $image = UploadedFile::fake()->image('gallery_audit.jpg', 1600, 1200);
        $createResp = $this->actingAs($user)->post(route('postsgalery.store'), [
            'image'   => $image,
            'nama'    => 'AUDIT_GALLERY_ARTWORK',
            'dimensi' => '100 x 100 cm',
            'link'    => 'https://wa.me/62811111111',
        ]);
        $createResp->assertRedirect(route('postsgalery.index'));

        $artwork = Postgalery::where('nama', 'AUDIT_GALLERY_ARTWORK')->first();
        $this->assertNotNull($artwork);
        $originalFilename = $artwork->image;
        Storage::disk('public')->assertExists('postsimg/' . $originalFilename);

        // UPDATE MATRIX:
        // A. Update only artwork name
        $this->actingAs($user)->put(route('postsgalery.update', $artwork->id), [
            'nama'    => 'AUDIT_GALLERY_CASE_A',
            'dimensi' => '100 x 100 cm',
            'link'    => 'https://wa.me/62811111111',
        ])->assertRedirect(route('postsgalery.index'));
        $artwork->refresh();
        $this->assertEquals('AUDIT_GALLERY_CASE_A', $artwork->nama);
        $this->assertEquals($originalFilename, $artwork->image);

        // B. Update only size
        $this->actingAs($user)->put(route('postsgalery.update', $artwork->id), [
            'nama'    => 'AUDIT_GALLERY_CASE_A',
            'dimensi' => '150 x 120 cm',
            'link'    => 'https://wa.me/62811111111',
        ])->assertRedirect(route('postsgalery.index'));
        $artwork->refresh();
        $this->assertEquals('150 x 120 cm', $artwork->dimensi);
        $this->assertEquals($originalFilename, $artwork->image);

        // C. Update name + size
        $this->actingAs($user)->put(route('postsgalery.update', $artwork->id), [
            'nama'    => 'AUDIT_GALLERY_CASE_C',
            'dimensi' => '200 x 150 cm',
            'link'    => 'https://wa.me/62811111111',
        ])->assertRedirect(route('postsgalery.index'));
        $artwork->refresh();
        $this->assertEquals('AUDIT_GALLERY_CASE_C', $artwork->nama);
        $this->assertEquals('200 x 150 cm', $artwork->dimensi);
        $this->assertEquals($originalFilename, $artwork->image);

        // D. Replace image
        $replacementImg = UploadedFile::fake()->image('gallery_replacement.jpg', 1400, 1000);
        $this->actingAs($user)->put(route('postsgalery.update', $artwork->id), [
            'image'   => $replacementImg,
            'nama'    => 'AUDIT_GALLERY_CASE_D',
            'dimensi' => '200 x 150 cm',
            'link'    => 'https://wa.me/62811111111',
        ])->assertRedirect(route('postsgalery.index'));
        $artwork->refresh();
        $this->assertNotEquals($originalFilename, $artwork->image);
        Storage::disk('public')->assertMissing('postsimg/' . $originalFilename);
        Storage::disk('public')->assertExists('postsimg/' . $artwork->image);

        // E. Update without selecting a new image
        $currentFilename = $artwork->image;
        $this->actingAs($user)->put(route('postsgalery.update', $artwork->id), [
            'nama'    => 'AUDIT_GALLERY_CASE_E',
            'dimensi' => '200 x 150 cm',
            'link'    => 'https://wa.me/62811111111',
        ])->assertRedirect(route('postsgalery.index'));
        $artwork->refresh();
        $this->assertEquals($currentFilename, $artwork->image);
        Storage::disk('public')->assertExists('postsimg/' . $currentFilename);

        // Neighboring file must still be intact
        Storage::disk('public')->assertExists('postsimg/neighbor_artwork.webp');

        // DELETE: verify record removed and neighbor untouched
        $delResp = $this->actingAs($user)->delete(route('postsgalery.destroy', $artwork->id));
        $delResp->assertRedirect(route('postsgalery.index'));
        $this->assertNull(Postgalery::find($artwork->id));
        Storage::disk('public')->assertMissing('postsimg/' . $currentFilename);
        Storage::disk('public')->assertExists('postsimg/neighbor_artwork.webp');

        // INVALID ID: Return 404
        $notFoundResp = $this->actingAs($user)->get('/postsgalery/999999/edit');
        $notFoundResp->assertStatus(404);
    }

    /**
     * SECTION 5: Testimoni CRUD Audit
     */
    public function test_testimoni_crud_lifecycle(): void
    {
        $user = $this->adminUser;

        // CREATE: with image
        $image = UploadedFile::fake()->image('testi_audit.jpg', 800, 800);
        $createResp = $this->actingAs($user)->post(route('posttestimoni.store'), [
            'image' => $image,
        ]);
        $createResp->assertRedirect(route('posttestimoni.index'));

        $testimoni = Posttestimoni::latest()->first();
        $this->assertNotNull($testimoni);
        $initialFilename = $testimoni->image;
        Storage::disk('public')->assertExists('postsimg/' . $initialFilename);

        // UPDATE: replace image
        $newImage = UploadedFile::fake()->image('testi_audit_new.png', 900, 900);
        $upResp = $this->actingAs($user)->put(route('posttestimoni.update', $testimoni->id), [
            'image' => $newImage,
        ]);
        $upResp->assertRedirect(route('posttestimoni.index'));
        $testimoni->refresh();
        $this->assertNotEquals($initialFilename, $testimoni->image);
        Storage::disk('public')->assertMissing('postsimg/' . $initialFilename);
        Storage::disk('public')->assertExists('postsimg/' . $testimoni->image);

        // DELETE: removes record and image
        $currentFilename = $testimoni->image;
        $delResp = $this->actingAs($user)->delete(route('posttestimoni.destroy', $testimoni->id));
        $delResp->assertRedirect(route('posttestimoni.index'));
        $this->assertNull(Posttestimoni::find($testimoni->id));
        Storage::disk('public')->assertMissing('postsimg/' . $currentFilename);

        // INVALID ID: Return 404
        $notFoundResp = $this->actingAs($user)->get('/posttestimoni/999999/edit');
        $notFoundResp->assertStatus(404);
    }

    /**
     * SECTION 6 & 7: Upload Validation & Security Edge Cases
     */
    public function test_upload_rejects_fake_and_oversized_files(): void
    {
        $user = $this->adminUser;

        // 1. Text file renamed as .jpg
        $fakeJpg = UploadedFile::fake()->create('malicious.jpg', 10, 'text/plain');
        $resp1 = $this->actingAs($user)->post(route('postsgalery.store'), [
            'image'   => $fakeJpg,
            'nama'    => 'AUDIT_FAKE_JPG',
            'dimensi' => '100 x 100 cm',
            'link'    => 'https://example.com',
        ]);
        $resp1->assertSessionHasErrors(['image']);
        $this->assertNull(Postgalery::where('nama', 'AUDIT_FAKE_JPG')->first());

        // 2. Oversized file (> 15MB = 15360KB)
        $oversized = UploadedFile::fake()->create('huge.jpg', 16000, 'image/jpeg');
        $resp2 = $this->actingAs($user)->post(route('postsgalery.store'), [
            'image'   => $oversized,
            'nama'    => 'AUDIT_OVERSIZED',
            'dimensi' => '100 x 100 cm',
            'link'    => 'https://example.com',
        ]);
        $resp2->assertSessionHasErrors(['image']);
        $this->assertNull(Postgalery::where('nama', 'AUDIT_OVERSIZED')->first());
    }
}
