<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('admin'),
            'role' => 'admin',
            'no_anggota' => 'admin1',
            'cabang' => 'Cabang Utama',
            'no_hp' => '081234567890',
            'alamat' => 'Jl. Raya No. 1',
            'foto' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Distribusi anggota per tahun
        // $distribusi = [
        //     2021 => 6,
        //     2022 => 5,
        //     2023 => 8,
        //     2024 => 7,
        //     2025 => 7,
        // ];

        // $counter = 1;

        // foreach ($distribusi as $tahun => $jumlah) {
        //     for ($i = 1; $i <= $jumlah; $i++) {
        //         $created = Carbon::create($tahun, rand(1, 12), rand(1, 28));

        //         User::create([
        //             'name' => "Anggota {$counter}",
        //             'username' => "anggota_{$counter}",
        //             'email' => "anggota{$counter}@example.com",
        //             'password' => Hash::make('password'),
        //             'role' => 'anggota',

        //             'no_anggota' => "A-{$tahun}-{$i}",
        //             'cabang' => ['Cirebon', 'Bandung', 'Jakarta'][rand(0, 2)],
        //             'no_hp' => '08' . rand(1000000000, 9999999999),
        //             'alamat' => "Alamat anggota {$counter}",
        //             'foto' => 'foto-default.jpg',

        //             'created_at' => $created,
        //             'updated_at' => $created,
        //         ]);

        //         $counter++;
        //     }
        // }
    }
}
