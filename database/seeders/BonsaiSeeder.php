<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BonsaiSeeder extends Seeder
{
    public function run(): void
    {
        $ukuranOptions = [
            ['label' => 'Small', 'kode' => 1, 'min' => 10, 'max' => 50, 'format' => 'cm'],
            ['label' => 'Medium', 'kode' => 2, 'min' => 51, 'max' => 100, 'format' => 'cm'],
            ['label' => 'Large', 'kode' => 3, 'min' => 101, 'max' => 200, 'format' => 'cm']
        ];

        $pesertaList = DB::table('users')
            ->where('role', 'anggota')
            ->pluck('id', 'username')
            ->toArray();

        $namaPohonList = [
            'Serut',
            'Beringin',
            'Sancang',
            'Asam Jawa',
            'Kimeng',
            'Bougenville',
            'Juniper',
            'Anting Putri',
            'Santigi'
        ];

        // === Purwawidada Bonsai 1: Hokiantie ===
        if (isset($pesertaList['purwa'])) {
            $purwaId = $pesertaList['purwa'];
            $ukuran = $ukuranOptions[array_rand($ukuranOptions)];
            $tinggi = rand($ukuran['min'], $ukuran['max']);
            $kelas = rand(1, 100) <= 70 ? 'Madya' : 'Utama';
            $slug = $this->generateUniqueSlug('Hokiantie', 'purwa', $ukuran['label']);

            DB::table('bonsai')->insert([
                'user_id' => $purwaId,
                'slug' => $slug,
                'nama_pohon' => 'Hokiantie',
                'nama_lokal' => null,
                'nama_latin' => null,
                'ukuran' => $ukuran['label'] . ' ( ' . $tinggi . ' ' . $ukuran['format'] . ' )',
                'ukuran_1' => $ukuran['kode'],
                'ukuran_2' => $tinggi,
                'format_ukuran' => $ukuran['format'],
                'no_induk_pohon' => 'BONSAI' . rand(10000000, 99999999),
                'masa_pemeliharaan' => rand(6, 24),
                'format_masa' => 'bulan',
                'kelas' => $kelas,
                'foto' => null,
                'created_at' => Carbon::now()->subDays(rand(1, 10)),
                'updated_at' => Carbon::now()->subDays(rand(1, 10)),
                'deleted_at' => null,
            ]);

            // === Purwawidada Bonsai 2: Random ===
            $namaPohon = $namaPohonList[array_rand($namaPohonList)];
            $ukuran = $ukuranOptions[array_rand($ukuranOptions)];
            $tinggi = rand($ukuran['min'], $ukuran['max']);
            $kelas = rand(1, 100) <= 70 ? 'Madya' : 'Utama';
            $slug = $this->generateUniqueSlug($namaPohon, 'purwa', $ukuran['label']);

            DB::table('bonsai')->insert([
                'user_id' => $purwaId,
                'slug' => $slug,
                'nama_pohon' => $namaPohon,
                'nama_lokal' => null,
                'nama_latin' => null,
                'ukuran' => $ukuran['label'] . ' ( ' . $tinggi . ' ' . $ukuran['format'] . ' )',
                'ukuran_1' => $ukuran['kode'],
                'ukuran_2' => $tinggi,
                'format_ukuran' => $ukuran['format'],
                'no_induk_pohon' => 'BONSAI' . rand(10000000, 99999999),
                'masa_pemeliharaan' => rand(6, 24),
                'format_masa' => 'bulan',
                'kelas' => $kelas,
                'foto' => null,
                'created_at' => Carbon::now()->subDays(rand(1, 10)),
                'updated_at' => Carbon::now()->subDays(rand(1, 10)),
                'deleted_at' => null,
            ]);
        }

        // === Sisa 38 bonsai untuk peserta lain ===
        for ($i = 0; $i < 38; $i++) {
            $username = array_rand($pesertaList);
            $userId = $pesertaList[$username];

            $namaPohon = $namaPohonList[array_rand($namaPohonList)];
            $ukuran = $ukuranOptions[array_rand($ukuranOptions)];
            $tinggi = rand($ukuran['min'], $ukuran['max']);
            $kelas = rand(1, 100) <= 70 ? 'Madya' : 'Utama';
            $slug = $this->generateUniqueSlug($namaPohon, $username, $ukuran['label']);

            DB::table('bonsai')->insert([
                'user_id' => $userId,
                'slug' => $slug,
                'nama_pohon' => $namaPohon,
                'nama_lokal' => null,
                'nama_latin' => null,
                'ukuran' => $ukuran['label'] . ' ( ' . $tinggi . ' ' . $ukuran['format'] . ' )',
                'ukuran_1' => $ukuran['kode'],
                'ukuran_2' => $tinggi,
                'format_ukuran' => $ukuran['format'],
                'no_induk_pohon' => 'BONSAI' . rand(10000000, 99999999),
                'masa_pemeliharaan' => rand(6, 24),
                'format_masa' => 'bulan',
                'kelas' => $kelas,
                'foto' => null,
                'created_at' => Carbon::now()->subDays(rand(1, 10)),
                'updated_at' => Carbon::now()->subDays(rand(1, 10)),
                'deleted_at' => null,
            ]);
        }
    }

    private function generateUniqueSlug($namaPohon, $username, $ukuranLabel)
    {
        $baseSlug = Str::slug($namaPohon . '-' . $username . '-' . strtolower($ukuranLabel));
        $slug = $baseSlug;
        $counter = 1;
        while (DB::table('bonsai')->where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        return $slug;
    }
}
