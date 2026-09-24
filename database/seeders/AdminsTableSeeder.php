<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name'      => 'ADMIN',
            'email'     => 'adminkukabuljogja@gmail.com',
            'password'  => bcrypt('kabuljogja')
        ]);
    }
}
