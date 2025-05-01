<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Akun Admin
        User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',

            'no_anggota' => '0001',
            'cabang' => 'Cabang Utama',
            'no_hp' => '081234567890',
            'alamat' => 'Jl. Raya No. 1',
            'foto' => 'default.png',
        ]);
    }
}
