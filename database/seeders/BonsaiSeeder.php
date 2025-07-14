<?php

namespace Database\Seeders;

use App\Models\Bonsai;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BonsaiSeeder extends Seeder
{
    public function run(): void
    {
        $distribusi = [
            2021 => 5,
            2022 => 5,
            2023 => 7,
            2024 => 8,
            2025 => 3,
        ];

        $counter = 1;
        $anggotaList = User::where('role', 'anggota')->pluck('id')->toArray();

        foreach ($distribusi as $tahun => $jumlah) {
            for ($i = 1; $i <= $jumlah; $i++) {
                $created = Carbon::create($tahun, rand(1, 12), rand(1, 28));
                $user_id = $anggotaList[array_rand($anggotaList)];

                Bonsai::create([
                    'user_id' => $user_id,
                    'slug' => Str::slug("bonsai-{$counter}-{$tahun}"),
                    'nama_pohon' => "Bonsai {$counter}",
                    'nama_lokal' => "Lokal {$counter}",
                    'nama_latin' => "Latin {$counter}",
                    'ukuran' => ['Kecil', 'Sedang', 'Besar'][rand(0, 2)],
                    'ukuran_1' => rand(20, 40) . ' cm',
                    'ukuran_2' => rand(30, 60) . ' cm',
                    'format_ukuran' => 'Custom',

                    'no_induk_pohon' => "BNS-{$tahun}-{$counter}",
                    'masa_pemeliharaan' => rand(1, 10) . ' tahun',
                    'format_masa' => 'Tahun',
                    'kelas' => ['Pemula', 'Madya', 'Utama'][rand(0, 2)],
                    'foto' => 'foto-default.jpg',

                    'created_at' => $created,
                    'updated_at' => $created,
                ]);

                $counter++;
            }
        }
    }
}
