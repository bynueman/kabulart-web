<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TranslationService
{
    /**
     * Testing / Mock hooks.
     */
    protected static ?\Closure $fakeHandler = null;
    protected static array $fakeTranslations = [];
    protected static bool $simulateFailure = false;

    /**
     * Distinctive Indonesian linguistic markers and vocabulary.
     */
    protected static array $indonesianMarkers = [
        'yang', 'dan', 'di', 'dari', 'ini', 'itu', 'dengan', 'untuk', 'pada', 'adalah',
        'ke', 'oleh', 'sebagai', 'kegiatan', 'pelatihan', 'pameran', 'karya', 'seni',
        'lukisan', 'bersama', 'wisatawan', 'peserta', 'kabupaten', 'kecamatan', 'kota',
        'sang', 'tahun', 'bulan', 'hari', 'bunga', 'merah', 'sepasang', 'ikan', 'desa',
        'sungai', 'cerutu', 'burung', 'alam', 'kisah', 'wayang', 'kekasih', 'ukuran',
        'katun', 'cantik', 'tulis', 'tunggal', 'kami', 'anda', 'mereka', 'belajar',
        'membuat', 'dilakukan', 'dihadiri', 'diselenggarakan', 'terkini', 'informasi',
        'kesan', 'pelanggan', 'selengkapnya', 'pesan', 'sekarang', 'kelola', 'tambah',
        'batal', 'simpan', 'ubah', 'hapus', 'foto', 'judul', 'nama', 'tautan', 'tamu',
        'liburan', 'rombongan', 'mahasiswa', 'pelajar', 'warga', 'mancanegara'
    ];

    /**
     * Distinctive English linguistic markers and vocabulary.
     */
    protected static array $englishMarkers = [
        'the', 'and', 'in', 'of', 'to', 'is', 'a', 'an', 'with', 'for', 'on', 'at',
        'from', 'by', 'this', 'that', 'are', 'was', 'were', 'has', 'have', 'had',
        'training', 'making', 'carried', 'out', 'students', 'tourists', 'came',
        'learn', 'learning', 'held', 'conducted', 'attended', 'foreign', 'flower',
        'couple', 'sunflowers', 'farmer', 'silhouette', 'village', 'peaceful',
        'ornamental', 'people', 'smoking', 'cigars', 'race', 'wild', 'story',
        'shadow', 'puppets', 'friendship', 'size', 'cotton', 'red', 'blue', 'black',
        'single', 'visit', 'visiting', 'visitor', 'visitors', 'majority', 'customers',
        'countries', 'preferred', 'order', 'now', 'read', 'more', 'view', 'details',
        'about', 'notable', 'exhibitions', 'paintings', 'guest', 'vacation', 'traveled',
        'continued', 'contemporary', 'artwork', 'artworks', 'drawn', 'drawing',
        'hand', 'craft', 'artist', 'classic', 'modern', 'traditional', 'collection'
    ];

    /**
     * Proper nouns and neutral terms that should not bias language detection.
     */
    protected static array $neutralProperNouns = [
        'kabul', 'art', 'gallery', 'wiji', 'hartono', 'yogyakarta', 'borobudur',
        'paris', 'france', 'germany', 'frankfurt', 'bremen', 'madrid', 'spain',
        'stockholm', 'sweden', 'finland', 'melbourne', 'australia', 'bali',
        'jakarta', 'bandung', 'bromo', 'ijen', 'sri', 'lanka', 'poland', 'polandia',
        'jepang', 'japan', 'netherlands', 'belanda', 'anwb', 'wh', 'mr', 'bapak',
        'batik', 'expo'
    ];

    /**
     * Register a custom fake closure for testing.
     */
    public static function fake(?\Closure $handler = null): void
    {
        self::$fakeHandler = $handler;
    }

    /**
     * Register pre-mapped fake translations for testing.
     */
    public static function fakeTranslations(array $map): void
    {
        self::$fakeTranslations = $map;
    }

    /**
     * Simulate an API failure for testing failure resilience.
     */
    public static function simulateFailure(bool $fail = true): void
    {
        self::$simulateFailure = $fail;
    }

    /**
     * Reset all test fakes and flags.
     */
    public static function resetFakes(): void
    {
        self::$fakeHandler = null;
        self::$fakeTranslations = [];
        self::$simulateFailure = false;
    }

    /**
     * Detect whether a string is Indonesian ('id') or English ('en').
     * Uses linguistic stop word scoring, shields neutral proper nouns,
     * and deterministically falls back to config default for ambiguous input.
     */
    public function detectLanguage(string $text): string
    {
        $defaultLocale = config('app.locale', 'id');

        if (trim($text) === '') {
            return $defaultLocale;
        }

        // Clean text: strip punctuation and split into lowercase words
        $clean = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', mb_strtolower($text, 'UTF-8'));
        $words = preg_split('/\s+/', $clean, -1, PREG_SPLIT_NO_EMPTY);

        if (empty($words)) {
            return $defaultLocale;
        }

        $idScore = 0;
        $enScore = 0;

        $idSet   = array_flip(self::$indonesianMarkers);
        $enSet   = array_flip(self::$englishMarkers);
        $neutral = array_flip(self::$neutralProperNouns);

        foreach ($words as $word) {
            // Skip recognized proper nouns
            if (isset($neutral[$word])) {
                continue;
            }

            if (isset($idSet[$word])) {
                $idScore++;
            }
            if (isset($enSet[$word])) {
                $enScore++;
            }
        }

        // Distinct Indonesian affixes check if scores are close
        if ($idScore === $enScore) {
            foreach ($words as $w) {
                if (isset($neutral[$w])) {
                    continue;
                }
                // Common Indonesian prefixes/suffixes (e.g., ber-, me-, pe-, ter-, di-, -kan, -an, -nya)
                if (preg_match('/^(ber|men|mem|meng|pen|pem|peng|ter|per)/u', $w) && mb_strlen($w) > 4) {
                    $idScore += 0.5;
                }
                if (preg_match('/(kan|nya|lah|kah)$/u', $w) && mb_strlen($w) > 4) {
                    $idScore += 0.5;
                }
                // Common English suffixes (e.g., -ing, -ed, -tion, -ment, -ly, -ous)
                if (preg_match('/(ing|ed|tion|ment|ly|ous|ness)$/u', $w) && mb_strlen($w) > 4) {
                    $enScore += 0.5;
                }
            }
        }

        if ($idScore > $enScore) {
            return 'id';
        }
        if ($enScore > $idScore) {
            return 'en';
        }

        // Safe deterministic fallback
        return $defaultLocale;
    }

    /**
     * Translate text between supported languages ('id' and 'en').
     * Returns translated text, or null on failure (never crashes).
     */
    public function translate(string $text, string $targetLocale, ?string $sourceLocale = null): ?string
    {
        $text = trim($text);
        if ($text === '') {
            return '';
        }

        $targetLocale = strtolower($targetLocale);
        if (!in_array($targetLocale, ['id', 'en'], true)) {
            return null;
        }

        if ($sourceLocale === null) {
            $sourceLocale = $this->detectLanguage($text);
        } else {
            $sourceLocale = strtolower($sourceLocale);
        }

        // Same source and target requires no translation
        if ($sourceLocale === $targetLocale) {
            return $text;
        }

        // 1. Check if failure simulation is active (for testing)
        if (self::$simulateFailure) {
            Log::warning("TranslationService: Simulated API failure triggered for target '{$targetLocale}'");
            return null;
        }

        // 2. Check if custom fake handler is registered
        if (self::$fakeHandler !== null) {
            return (self::$fakeHandler)($text, $targetLocale, $sourceLocale);
        }

        // 3. Check if mapped fake translations are registered
        if (!empty(self::$fakeTranslations)) {
            if (isset(self::$fakeTranslations[$text])) {
                $val = self::$fakeTranslations[$text];
                return is_array($val) ? ($val[$targetLocale] ?? null) : $val;
            }
        }

        // 4. Resolve provider from configuration
        $driver = config('services.translation.driver', 'google');

        if ($driver === 'mock') {
            return "[{$targetLocale}] " . $text;
        }

        if ($driver === 'google') {
            return $this->translateWithGoogle($text, $targetLocale, $sourceLocale);
        }

        if ($driver === 'deepl') {
            return $this->translateWithDeepL($text, $targetLocale, $sourceLocale);
        }

        Log::info("TranslationService: No live translation API configured (driver: {$driver}). Returning null safely.");
        return null;
    }

    /**
     * Google Cloud Translation REST API implementation.
     */
    protected function translateWithGoogle(string $text, string $targetLocale, string $sourceLocale): ?string
    {
        $apiKey = config('services.translation.google_key');

        if (empty($apiKey)) {
            Log::warning('TranslationService: GOOGLE_TRANSLATE_API_KEY is not configured in .env');
            return null;
        }

        try {
            $response = Http::timeout(7)->post('https://translation.googleapis.com/language/translate/v2?key=' . urlencode($apiKey), [
                'q'      => $text,
                'target' => $targetLocale,
                'source' => $sourceLocale,
                'format' => 'text',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $translated = $data['data']['translations'][0]['translatedText'] ?? null;
                if (!empty($translated)) {
                    // Decode any unintended HTML entities returned by translation API
                    return html_entity_decode($translated, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                }
            } else {
                Log::warning('TranslationService: Google API error: ' . $response->status() . ' - ' . $response->body());
            }
        } catch (\Throwable $e) {
            Log::error('TranslationService: Google API request failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        }

        return null;
    }

    /**
     * DeepL API implementation.
     */
    protected function translateWithDeepL(string $text, string $targetLocale, string $sourceLocale): ?string
    {
        $apiKey = config('services.translation.deepl_key');

        if (empty($apiKey)) {
            Log::warning('TranslationService: DEEPL_AUTH_KEY is not configured in .env');
            return null;
        }

        // DeepL uses EN-US or EN-GB for target English, and ID for Indonesian
        $deepLTarget = $targetLocale === 'en' ? 'EN-US' : strtoupper($targetLocale);
        $deepLSource = strtoupper($sourceLocale);

        // Free tier uses api-free.deepl.com, Pro tier uses api.deepl.com
        $isFreeTier = str_ends_with($apiKey, ':fx');
        $endpoint   = $isFreeTier
            ? 'https://api-free.deepl.com/v2/translate'
            : 'https://api.deepl.com/v2/translate';

        try {
            $response = Http::timeout(7)
                ->withHeaders(['Authorization' => 'DeepL-Auth-Key ' . $apiKey])
                ->asForm()
                ->post($endpoint, [
                    'text'        => $text,
                    'target_lang' => $deepLTarget,
                    'source_lang' => $deepLSource,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['translations'][0]['text'] ?? null;
            } else {
                Log::warning('TranslationService: DeepL API error: ' . $response->status() . ' - ' . $response->body());
            }
        } catch (\Throwable $e) {
            Log::error('TranslationService: DeepL API request failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        }

        return null;
    }
}
