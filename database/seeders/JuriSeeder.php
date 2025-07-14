<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Juri;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class JuriSeeder extends Seeder
{
    public function run(): void
    {
        $distribusi = [
            2021 => 2,
            2022 => 1,
            2023 => 1,
            2024 => 2,
            2025 => 3,
        ];

        $counter = 1;

        foreach ($distribusi as $tahun => $jumlah) {
            for ($i = 1; $i <= $jumlah; $i++) {
                $created = Carbon::create($tahun, rand(1, 12), rand(1, 28));
                $username = "juri_{$counter}";

                // Buat user juri dulu
                $user = User::create([
                    'name' => "Juri {$counter}",
                    'username' => $username,
                    'email' => "juri{$counter}@example.com",
                    'password' => Hash::make($username), // ✅ password = username
                    'role' => 'juri',

                    'no_anggota' => "J-{$tahun}-{$i}",
                    'cabang' => ['Cirebon', 'Bandung', 'Jakarta'][rand(0, 2)],
                    'no_hp' => '08' . rand(1000000000, 9999999999),
                    'alamat' => "Alamat juri {$counter}",
                    'foto' => 'foto-default.jpg',

                    'created_at' => $created,
                    'updated_at' => $created,
                ]);

                // Masukkan ke tabel juri
                Juri::create([
                    'user_id' => $user->id,
                    'slug' => Str::slug($username),
                    'no_induk_juri' => "NIJ-{$tahun}{$i}",
                    'nama_juri' => $user->name,
                    'foto' => 'foto-default.jpg',
                    'sertifikat' => "Sertifikat-J{$counter}.pdf",
                    'no_telepon' => $user->no_hp,
                    'email' => $user->email,
                    'username' => $user->username,
                    'password' => $user->password, // tetap simpan hash yang sama
                    'status' => '1',
                    'role' => 'juri',

                    'created_at' => $created,
                    'updated_at' => $created,
                ]);

                $counter++;
            }
        }
    }
}
