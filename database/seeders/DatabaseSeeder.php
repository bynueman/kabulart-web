<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(AdminsTableSeeder::class);

        // Seed sample data using existing images
        \App\Models\Postinformasi::create([
            'image' => '324xvU7XtQu6X9CNt8zEI7vFWTRPkiaUdXqhBwp1.jpg',
            'deskripsi' => 'Pameran Batik Kontemporer Kabul Art Gallery - Menampilkan karya-karya seni lukis batik istimewa yang memadukan tradisi klasik dengan sentuhan modern khas Yogyakarta.',
        ]);

        \App\Models\Postinformasi::create([
            'image' => '4EaPKk4CI587AuxGHK8Vj8OBgl1M12SiLTM9g9Nu.jpg',
            'deskripsi' => 'Workshop Pembuatan Lukisan Batik bersama seniman lokal. Terbuka untuk umum dan wisatawan mancanegara.',
        ]);

        $galleryImages = [
            ['5z3V4Fd435Jdvo8NzARwZKbk0I1CxHs7zOgWxoo9.jpg', 'Lukisan Batik Borobudur', '100 x 75 cm'],
            ['9TOrvLgE9vgOdnH2KjwbgAblLULiTUHdcsSi0frW.jpg', 'Wayang Heritage', '120 x 80 cm'],
            ['CE5DYW636Kt52vI3qSW6UCiterh7ll0kk9FMKeE0.jpg', 'Harmoni Alam Jogja', '90 x 70 cm'],
            ['Fe4Y7nxV8XuVfl01j8B2UZEGdv0InNCsEjsiKCTB.jpg', 'Batik Abstrak Senja', '110 x 85 cm'],
            ['HS10PMZb6IpfXKA11c3VTMWSDIQnSKKSjZDRLCSH.jpg', 'Pesona Budaya Jawa', '100 x 100 cm'],
            ['czK3wPThyVUGsqDD3SfGYdI3pyGiWevfqQRBnhZG.jpg', 'Motif Klasik Yogyakarta', '80 x 60 cm'],
        ];

        foreach ($galleryImages as $item) {
            \App\Models\Postgalery::create([
                'image' => $item[0],
                'nama' => $item[1],
                'dimensi' => $item[2],
                'link' => 'https://wa.me/6282223242071',
            ]);
        }

        $testimoniImages = [
            'evKrj17YNZaf4iHBIE2Uzafxp3fbz2ZM7D2D7Ugj.jpg',
            'nUd3gQUtIorLTWsiHHrpRs8v0h00bFNQZytcC7No.jpg',
            'oM71JkLqGc0KinfruSidLLNTyRGxCBSSXlP69mdp.jpg',
            'qq4ArrVLfI575YOGQjbzq2hizcZlQQ4pN0eFYvi0.jpg',
        ];

        foreach ($testimoniImages as $img) {
            \App\Models\Posttestimoni::create([
                'image' => $img,
            ]);
        }
    }
}
