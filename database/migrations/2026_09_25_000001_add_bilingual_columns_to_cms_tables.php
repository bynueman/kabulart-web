<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add bilingual columns to postinformasis
        Schema::table('postinformasis', function (Blueprint $table) {
            $table->text('deskripsi_id')->nullable()->after('deskripsi');
            $table->text('deskripsi_en')->nullable()->after('deskripsi_id');
            $table->string('translation_source', 10)->default('id')->after('deskripsi_en');
            $table->boolean('translation_manual')->default(false)->after('translation_source');
        });

        // 2. Add bilingual columns to postgaleries
        Schema::table('postgaleries', function (Blueprint $table) {
            $table->text('nama_id')->nullable()->after('nama');
            $table->text('nama_en')->nullable()->after('nama_id');
            $table->string('dimensi_id')->nullable()->after('dimensi');
            $table->string('dimensi_en')->nullable()->after('dimensi_id');
            $table->string('translation_source', 10)->default('id')->after('link');
            $table->boolean('translation_manual')->default(false)->after('translation_source');
        });

        // 3. Zero-data-loss safe backfill for existing records
        // Postinformasi backfill:
        $infoTranslations = [
            1 => [
                'id' => 'Kabul Art Gallery adalah perusahaan yang menawarkan lukisan batik di Yogyakarta. Mayoritas pelanggannya adalah wisatawan mancanegara dari berbagai negara termasuk Jerman. Wisatawan asing umumnya lebih menyukai batik dalam bentuk lukisan.',
            ],
            2 => [
                'id' => 'Tamu liburan dari Belanda ini melakukan perjalanan dari Jakarta, Bandung, Yogyakarta, Gunung Bromo, Kawah Ijen, dan lanjut ke Bali. Mereka singgah di Kabul Art Gallery untuk melihat dan belajar mengenai keunikan lukisan batik khas Yogyakarta.',
            ],
            3 => [
                'id' => 'Rombongan ANWB Group dari Belanda sedang belajar cara membuat batik saat berkunjung ke kota Yogyakarta. Antusiasme para peserta sangat tinggi saat mencoba langsung teknik mencanting.',
            ],
            4 => [
                'id' => 'Pelatihan pembuatan batik tulis yang diikuti oleh para pelajar dari Jepang di Kabul Art Gallery Yogyakarta.',
            ],
            5 => [
                'id' => 'Mahasiswa Jepang belajar batik tulis di Kabul Art Gallery, dibimbing langsung oleh pengrajin batik berpengalaman.',
            ],
            6 => [
                'id' => 'Pelajar Jepang belajar membatik di Kabul Art Gallery sesi kedua yang diselenggarakan pada Sabtu pagi dengan penuh semangat.',
            ],
            7 => [
                'id' => '12 wisatawan asal Prancis berkunjung ke Kabul Art Gallery untuk belajar membuat batik bersama sang maestro, Bapak Kabul.',
            ],
            8 => [
                'id' => '5 wisatawan dari Belanda datang ke Kabul Art Gallery untuk mempelajari proses membatik bersama sang maestro Bapak Kabul.',
            ],
            9 => [
                'id' => 'Pelatihan batik di Kabul Art Gallery yang diselenggarakan oleh pemerintah kecamatan dan dihadiri oleh warga masyarakat setempat dengan dedikasi tinggi.',
            ],
            10 => [
                'id' => 'Pelatihan batik di Kabul Art Gallery yang diikuti oleh wisatawan mancanegara asal Polandia yang sangat antusias mendalami seni tradisional Indonesia.',
            ],
        ];

        $existingInfos = DB::table('postinformasis')->get();
        foreach ($existingInfos as $info) {
            $exactOriginal = $info->deskripsi;
            $idTrans = $infoTranslations[$info->id]['id'] ?? $exactOriginal;

            DB::table('postinformasis')->where('id', $info->id)->update([
                'deskripsi_en'       => $exactOriginal,
                'deskripsi_id'       => $idTrans,
                'translation_source' => 'en',
                'translation_manual' => false,
            ]);
        }

        // Postgalery backfill:
        $galleryNameTranslations = [
            1  => 'Bunga Merah Tunggal',
            2  => 'Sepasang Jerapah',
            3  => 'Koleksi Bunga Matahari',
            4  => 'Petani Laut',
            5  => 'Siluet Biru',
            6  => 'Desa Tepi Sungai',
            7  => 'Desa Damai di Bawah Bulan Purnama',
            8  => 'Ikan Hias Cantik',
            9  => 'Orang Menghisap Cerutu',
            10 => 'Karapan Sapi Borobudur',
            11 => 'Petani Padi',
            12 => 'Jerapah di Alam Liar',
            13 => 'Kisah Wayang Sepasang Kekasih',
            14 => 'Ikan Hitam Abstrak',
            15 => 'Persahabatan Ikan Merah',
        ];

        $existingGalleries = DB::table('postgaleries')->get();
        foreach ($existingGalleries as $gallery) {
            $exactOriginalName = $gallery->nama;
            $idName = $galleryNameTranslations[$gallery->id] ?? $exactOriginalName;

            // Handle dimension localization e.g. "Size 50 x 70 cm, Cotton" -> "Ukuran 50 x 70 cm, Katun"
            $dim = $gallery->dimensi ?? '';
            $dimId = str_ireplace(['Size ', 'Cotton'], ['Ukuran ', 'Katun'], $dim);

            DB::table('postgaleries')->where('id', $gallery->id)->update([
                'nama_en'            => $exactOriginalName,
                'nama_id'            => $idName,
                'dimensi_en'         => $dim,
                'dimensi_id'         => $dimId,
                'translation_source' => 'en',
                'translation_manual' => false,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('postinformasis', function (Blueprint $table) {
            $table->dropColumn(['deskripsi_id', 'deskripsi_en', 'translation_source', 'translation_manual']);
        });

        Schema::table('postgaleries', function (Blueprint $table) {
            $table->dropColumn(['nama_id', 'nama_en', 'dimensi_id', 'dimensi_en', 'translation_source', 'translation_manual']);
        });
    }
};
