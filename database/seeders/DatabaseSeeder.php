<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Student;
use App\Models\Badge;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat User (Orang Tua/Admin) sebagai akun utama
        $user = User::create([
            'name' => 'Orang Tua Faris',
            'email' => 'ortu@example.com',
            'password' => Hash::make('password123'),
        ]);

        // 2. Buat data Siswa (Anak) yang terhubung ke User tadi
        // Ini akan memiliki ID = 1 yang kita pakai di tombol "MULAI MAIN"
        Student::create([
            'user_id' => $user->id,
            'name' => 'Adik Hebat',
            'age' => 7,
            'avatar_url' => null, // Bisa diisi path gambar nantinya
            'total_points' => 0,
        ]);

        // 3. Buat Daftar Lencana (Badges) yang menarik untuk anak SD
        Badge::insert([
            [
                'name' => 'Pahlawan Gigi',
                'description' => 'Berhasil menyikat gigi sampai putih berkilau!',
                'image_path' => 'badges/gigi_hero.png',
                'required_points' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Raja Sabun',
                'description' => 'Tanganmu sekarang bersih dari kuman jahat!',
                'image_path' => 'badges/sabun_king.png',
                'required_points' => 150,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bintang Kebersihan',
                'description' => 'Kamu sudah jadi juara menjaga diri sendiri!',
                'image_path' => 'badges/star.png',
                'required_points' => 300,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}