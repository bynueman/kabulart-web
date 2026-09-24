<?php

namespace Tests\Feature;

use App\Models\Postgalery;
use App\Models\Postinformasi;
use App\Models\Posttestimoni;
use App\Models\User;
use App\Services\TranslationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BilingualSystemTest extends TestCase
{
    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Reset translation service fakes before each test
        TranslationService::resetFakes();

        // Create or fetch admin user
        $this->adminUser = User::firstOrCreate(
            ['email' => 'admin_test@kabulart.com'],
            [
                'name'     => 'Admin Test',
                'password' => Hash::make('password123'),
            ]
        );
    }

    protected function tearDown(): void
    {
        TranslationService::resetFakes();
        parent::tearDown();
    }

    /**
     * Helper to generate a minimal valid 1x1 WebP for file upload tests.
     */
    protected function createValidWebpFile(string $name = 'test.webp'): UploadedFile
    {
        $webpContent = base64_decode('UklGRhoAAABXRUJQVlA4TA0AAAAvAAAAEAcQERGIiP4HAA==');
        $tempPath = tempnam(sys_get_temp_dir(), 'bilingual_test_') . '.webp';
        file_put_contents($tempPath, $webpContent);

        return new UploadedFile($tempPath, $name, 'image/webp', null, true);
    }

    /*
    |--------------------------------------------------------------------------
    | 1. LANGUAGE SWITCHING & PERSISTENCE
    |--------------------------------------------------------------------------
    */

    public function test_locale_switcher_routes_and_cookies()
    {
        // Switch to Indonesian
        $responseId = $this->get(route('locale.switch', 'id'));
        $responseId->assertRedirect('/');
        $responseId->assertSessionHas('locale', 'id');
        $responseId->assertCookie('kabul_locale', 'id');

        // Switch to English
        $responseEn = $this->get(route('locale.switch', 'en'));
        $responseEn->assertRedirect('/');
        $responseEn->assertSessionHas('locale', 'en');
        $responseEn->assertCookie('kabul_locale', 'en');

        // Invalid locale defaults safely to default (id)
        $responseInvalid = $this->get('/locale/fr');
        $responseInvalid->assertRedirect('/');
        $responseInvalid->assertSessionHas('locale', 'id');

        // Route alias /language/{locale} works identically
        $responseAlias = $this->get('/language/en');
        $responseAlias->assertRedirect('/');
        $responseAlias->assertSessionHas('locale', 'en');
    }

    public function test_locale_persists_across_multiple_pages()
    {
        // 1. Visit switch to English
        $this->get('/locale/en')->assertRedirect('/');

        // 2. Open Home page -> must be English
        $respHome = $this->withSession(['locale' => 'en'])->get('/');
        $respHome->assertOk();
        $respHome->assertSee('Featured Artworks');
        $respHome->assertSee('ORDER NOW');
        $respHome->assertSee('<html lang="en"', false);

        // 3. Open Profil page -> must remain English
        $respProfil = $this->withSession(['locale' => 'en'])->get('/profil');
        $respProfil->assertOk();
        $respProfil->assertSee('About Us');
        $respProfil->assertSee('PROFILE');
        $respProfil->assertSee('Notable Exhibitions');
        $respProfil->assertSee('<html lang="en"', false);

        // 4. Open Gallery page -> must remain English
        $respGallery = $this->withSession(['locale' => 'en'])->get('/gallery');
        $respGallery->assertOk();
        $respGallery->assertSee('Art Collection');
        $respGallery->assertSee('ORDER NOW');
        $respGallery->assertSee('<html lang="en"', false);

        // 5. Open Testimoni page -> must remain English
        $respTestimoni = $this->withSession(['locale' => 'en'])->get('/testimoni');
        $respTestimoni->assertOk();
        $respTestimoni->assertSee('Customer Impressions');
        $respTestimoni->assertSee('TESTIMONIALS');
        $respTestimoni->assertSee('<html lang="en"', false);
    }

    public function test_cookie_persists_locale_when_session_is_fresh()
    {
        $response = $this->withUnencryptedCookie('kabul_locale', 'en')->get('/profil');
        $response->assertOk();
        $response->assertSee('<html lang="en"', false);
        $response->assertSee('About Us');
    }

    public function test_browser_accept_language_detection_on_first_visit()
    {
        // English preferred browser
        $responseEn = $this->withHeaders(['Accept-Language' => 'en-US,en;q=0.9'])->get('/');
        $responseEn->assertOk();
        $responseEn->assertSee('<html lang="en"', false);

        // Indonesian browser
        $responseId = $this->withHeaders(['Accept-Language' => 'id-ID,id;q=0.9'])->get('/');
        $responseId->assertOk();
        $responseId->assertSee('<html lang="id"', false);
    }

    /*
    |--------------------------------------------------------------------------
    | 2. PUBLIC VIEWS RENDERING IN BOTH LANGUAGES
    |--------------------------------------------------------------------------
    */

    public function test_public_pages_render_in_indonesian()
    {
        $session = ['locale' => 'id'];

        // Home
        $home = $this->withSession($session)->get('/');
        $home->assertOk();
        $home->assertSee('<html lang="id"', false);
        $home->assertSee('Karya Pilihan');
        $home->assertSee('PESAN SEKARANG');
        $home->assertSee('SELENGKAPNYA →');

        // Profil
        $profil = $this->withSession($session)->get('/profil');
        $profil->assertOk();
        $profil->assertSee('<html lang="id"', false);
        $profil->assertSee('Tentang Kami');
        $profil->assertSee('PROFIL');
        $profil->assertSee('Autobiografi The Maestro');
        $profil->assertSee('Rekam Jejak Pameran');

        // Informasi
        $info = $this->withSession($session)->get('/informasi');
        $info->assertOk();
        $info->assertSee('<html lang="id"', false);
        $info->assertSee('Kabar Terkini');
        $info->assertSee('Kegiatan');

        // Gallery
        $gallery = $this->withSession($session)->get('/gallery');
        $gallery->assertOk();
        $gallery->assertSee('<html lang="id"', false);
        $gallery->assertSee('Koleksi Karya');
        $gallery->assertSee('PESAN SEKARANG');

        // Testimoni
        $testimoni = $this->withSession($session)->get('/testimoni');
        $testimoni->assertOk();
        $testimoni->assertSee('<html lang="id"', false);
        $testimoni->assertSee('Kesan Pelanggan');
        $testimoni->assertSee('TESTIMONI');
    }

    public function test_public_pages_render_in_english()
    {
        $session = ['locale' => 'en'];

        // Home
        $home = $this->withSession($session)->get('/');
        $home->assertOk();
        $home->assertSee('<html lang="en"', false);
        $home->assertSee('Featured Artworks');
        $home->assertSee('ORDER NOW');
        $home->assertSee('VIEW MORE →');

        // Profil
        $profil = $this->withSession($session)->get('/profil');
        $profil->assertOk();
        $profil->assertSee('<html lang="en"', false);
        $profil->assertSee('About Us');
        $profil->assertSee('PROFILE');
        $profil->assertSee('Autobiography of The Maestro');
        $profil->assertSee('Notable Exhibitions');
        $profil->assertSee('Exhibited by all of student art academy in Paris, France');

        // Informasi
        $info = $this->withSession($session)->get('/informasi');
        $info->assertOk();
        $info->assertSee('<html lang="en"', false);
        $info->assertSee('Latest News');
        $info->assertSee('Activities');

        // Gallery
        $gallery = $this->withSession($session)->get('/gallery');
        $gallery->assertOk();
        $gallery->assertSee('<html lang="en"', false);
        $gallery->assertSee('Art Collection');
        $gallery->assertSee('ORDER NOW');

        // Testimoni
        $testimoni = $this->withSession($session)->get('/testimoni');
        $testimoni->assertOk();
        $testimoni->assertSee('<html lang="en"', false);
        $testimoni->assertSee('Customer Impressions');
        $testimoni->assertSee('TESTIMONIALS');
    }

    public function test_language_switcher_appears_on_every_public_page()
    {
        $urls = ['/', '/profil', '/informasi', '/gallery', '/testimoni'];

        foreach ($urls as $url) {
            $resp = $this->get($url);
            $resp->assertOk();
            $resp->assertSee('kg-lang-switch', false);
            $resp->assertSee('/locale/id');
            $resp->assertSee('/locale/en');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 3. ADMIN UI LOCALIZATION
    |--------------------------------------------------------------------------
    */

    public function test_admin_ui_renders_in_indonesian()
    {
        $session = ['locale' => 'id'];

        $dash = $this->actingAs($this->adminUser)->withSession($session)->get('/homeadmin');
        $dash->assertOk();
        $dash->assertSee('<html lang="id"', false);
        $dash->assertSee('Dashboard');
        $dash->assertSee('Selamat Datang');
        $dash->assertSee('Karya Gallery');
        $dash->assertSee('Akses Cepat');
        $dash->assertSee('adm-lang-switch', false);

        $gal = $this->actingAs($this->adminUser)->withSession($session)->get(route('postsgalery.index'));
        $gal->assertOk();
        $gal->assertSee('Karya Gallery');
        $gal->assertSee('Tambah Karya');
        $gal->assertSee('Nama Karya');

        $info = $this->actingAs($this->adminUser)->withSession($session)->get(route('postsinformasi.index'));
        $info->assertOk();
        $info->assertSee('Informasi');
        $info->assertSee('Tambah Informasi');
        $info->assertSee('Deskripsi');
    }

    public function test_admin_ui_renders_in_english()
    {
        $session = ['locale' => 'en'];

        $dash = $this->actingAs($this->adminUser)->withSession($session)->get('/homeadmin');
        $dash->assertOk();
        $dash->assertSee('<html lang="en"', false);
        $dash->assertSee('Dashboard');
        $dash->assertSee('Welcome');
        $dash->assertSee('Gallery Artworks');
        $dash->assertSee('Quick Access');
        $dash->assertSee('adm-lang-switch', false);

        $gal = $this->actingAs($this->adminUser)->withSession($session)->get(route('postsgalery.index'));
        $gal->assertOk();
        $gal->assertSee('Gallery Artworks');
        $gal->assertSee('Add Artwork');
        $gal->assertSee('Artwork Name');

        $info = $this->actingAs($this->adminUser)->withSession($session)->get(route('postsinformasi.index'));
        $info->assertOk();
        $info->assertSee('Information');
        $info->assertSee('Add Information');
        $info->assertSee('Description');
    }

    /*
    |--------------------------------------------------------------------------
    | 4. TRANSLATION SERVICE DETECTION & ACCURACY
    |--------------------------------------------------------------------------
    */

    public function test_translation_service_language_detection()
    {
        $svc = new TranslationService();

        // Clear Indonesian text
        $this->assertEquals('id', $svc->detectLanguage('Pelatihan batik bersama wisatawan dari Polandia.'));
        $this->assertEquals('id', $svc->detectLanguage('Karya seni batik tulis kontemporer dari Yogyakarta'));
        $this->assertEquals('id', $svc->detectLanguage('Bunga Merah Tunggal'));

        // Clear English text
        $this->assertEquals('en', $svc->detectLanguage('Batik training with visitors from Poland.'));
        $this->assertEquals('en', $svc->detectLanguage('Contemporary hand-drawn batik artwork from Yogyakarta'));
        $this->assertEquals('en', $svc->detectLanguage('Single Red Flower'));

        // Mixed & proper noun cases
        $this->assertEquals('id', $svc->detectLanguage('Expo Batik di Paris'));
        $this->assertEquals('id', $svc->detectLanguage('Wiji Hartono / Kabul – Yogyakarta'));

        // Short brand phrase falls back deterministically to config default
        $this->assertEquals(config('app.locale', 'id'), $svc->detectLanguage('Kabul Gallery'));
    }

    /*
    |--------------------------------------------------------------------------
    | 5. CMS BILINGUAL CRUD & TRANSLATION SERVICE BEHAVIOR
    |--------------------------------------------------------------------------
    */

    public function test_cms_creates_indonesian_content_and_auto_translates_english()
    {
        // Fake translation map for deterministic test
        TranslationService::fake(function ($text, $targetLocale, $sourceLocale) {
            if ($targetLocale === 'en') {
                return 'Batik exhibition with visitors from Germany.';
            }
            return 'Pameran batik bersama wisatawan dari Jerman.';
        });

        $image = $this->createValidWebpFile('info_bilingual_id.webp');

        $resp = $this->actingAs($this->adminUser)->post(route('postsinformasi.store'), [
            'image'     => $image,
            'deskripsi' => 'Pameran batik bersama wisatawan dari Jerman.',
        ]);

        $resp->assertRedirect(route('postsinformasi.index'));
        $resp->assertSessionHas('success');

        $record = Postinformasi::latest('id')->first();
        $this->assertNotNull($record);

        // Source exact text must be preserved
        $this->assertEquals('Pameran batik bersama wisatawan dari Jerman.', $record->deskripsi_id);
        $this->assertEquals('id', $record->translation_source);

        // Auto translation counterpart generated
        $this->assertEquals('Batik exhibition with visitors from Germany.', $record->deskripsi_en);
        $this->assertFalse($record->translation_manual);

        // Clean up test record and uploaded file
        \App\Services\MediaPipeline::deleteVariants($record->image, 'postsimg');
        $record->delete();
    }

    public function test_cms_creates_english_content_and_auto_translates_indonesian()
    {
        TranslationService::fake(function ($text, $targetLocale, $sourceLocale) {
            if ($targetLocale === 'id') {
                return 'Sepasang burung merak di taman tropis.';
            }
            return 'A pair of peacocks in a tropical garden.';
        });

        $image = $this->createValidWebpFile('gallery_bilingual_en.webp');

        $resp = $this->actingAs($this->adminUser)->post(route('postsgalery.store'), [
            'image'   => $image,
            'nama'    => 'A pair of peacocks in a tropical garden.',
            'dimensi' => 'Size 60 x 90 cm, Cotton',
            'link'    => 'https://wa.me/628123456789',
        ]);

        $resp->assertRedirect(route('postsgalery.index'));
        $resp->assertSessionHas('success');

        $record = Postgalery::latest('id')->first();
        $this->assertNotNull($record);

        // Source exact text preserved
        $this->assertEquals('A pair of peacocks in a tropical garden.', $record->nama_en);
        $this->assertEquals('en', $record->translation_source);

        // Auto translation counterpart generated
        $this->assertEquals('Sepasang burung merak di taman tropis.', $record->nama_id);
        $this->assertFalse($record->translation_manual);

        // Clean up test record and uploaded file
        \App\Services\MediaPipeline::deleteVariants($record->image, 'postsimg');
        $record->delete();
    }

    public function test_translation_failure_saves_content_safely_with_controlled_warning()
    {
        // Simulate translation failure
        TranslationService::simulateFailure(true);

        $image = $this->createValidWebpFile('fail_safe.webp');

        $resp = $this->actingAs($this->adminUser)->post(route('postsinformasi.store'), [
            'image'     => $image,
            'deskripsi' => 'Pelatihan batik tulis untuk komunitas seni lokal Yogyakarta.',
        ]);

        $resp->assertRedirect(route('postsinformasi.index'));
        $resp->assertSessionHas('success');
        $resp->assertSessionHas('warning');

        $record = Postinformasi::latest('id')->first();
        $this->assertNotNull($record);

        // Source text must be completely safe and intact
        $this->assertEquals('Pelatihan batik tulis untuk komunitas seni lokal Yogyakarta.', $record->deskripsi_id);
        $this->assertEquals('Pelatihan batik tulis untuk komunitas seni lokal Yogyakarta.', $record->deskripsi);
        $this->assertEquals('id', $record->translation_source);

        // English is null (not overwritten or crashed)
        $this->assertNull($record->deskripsi_en);

        // Clean up test record and uploaded file
        \App\Services\MediaPipeline::deleteVariants($record->image, 'postsimg');
        $record->delete();
    }

    public function test_manual_translation_override_is_preserved_on_update()
    {
        $info = Postinformasi::create([
            'image'              => 'manual_test.webp',
            'deskripsi'          => 'Auto description ID',
            'deskripsi_id'       => 'Auto description ID',
            'deskripsi_en'       => 'Auto description EN',
            'translation_source' => 'id',
            'translation_manual' => false,
        ]);

        // Admin manually modifies English translation
        $resp = $this->actingAs($this->adminUser)->put(route('postsinformasi.update', $info->id), [
            'deskripsi_id' => 'Deskripsi Bahasa Indonesia yang disempurnakan',
            'deskripsi_en' => 'Carefully human-curated English description',
        ]);

        $resp->assertRedirect(route('postsinformasi.index'));
        $resp->assertSessionHas('success');

        $info->refresh();
        $this->assertEquals('Deskripsi Bahasa Indonesia yang disempurnakan', $info->deskripsi_id);
        $this->assertEquals('Carefully human-curated English description', $info->deskripsi_en);
        $this->assertTrue($info->translation_manual);

        // Clean up test record
        $info->delete();
    }

    public function test_fallback_display_when_translation_is_missing()
    {
        // Create record that only has Indonesian text
        $info = Postinformasi::create([
            'image'              => 'fallback_test.webp',
            'deskripsi'          => 'Teks Bahasa Indonesia Asli',
            'deskripsi_id'       => 'Teks Bahasa Indonesia Asli',
            'deskripsi_en'       => null,
            'translation_source' => 'id',
        ]);

        // When visitor accesses in English, it should gracefully fall back to Indonesian rather than empty
        app()->setLocale('en');
        $this->assertEquals('Teks Bahasa Indonesia Asli', $info->deskripsi);

        // When visitor accesses in Indonesian, returns Indonesian
        app()->setLocale('id');
        $this->assertEquals('Teks Bahasa Indonesia Asli', $info->deskripsi);

        // Clean up test record
        $info->delete();
    }
}
