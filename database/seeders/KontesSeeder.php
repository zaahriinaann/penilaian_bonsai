<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kontes;
use Illuminate\Support\Str;
use Carbon\Carbon;

class KontesSeeder extends Seeder
{
    public function run(): void
    {
        $distribusiKontes = [
            2021 => 3,
            2022 => 1,
            2023 => 1,
            2024 => 1,
            2025 => 1,
        ];

        foreach ($distribusiKontes as $tahun => $jumlah) {
            for ($i = 1; $i <= $jumlah; $i++) {
                $start = Carbon::create($tahun, rand(1, 12), rand(1, 25));
                $end   = $start->copy()->addDays(rand(1, 3));

                // Pastikan jumlah peserta minimal 30
                $jumlahPeserta = rand(30, 60); // bisa kamu ubah range max-nya kalau mau

                Kontes::create([
                    'nama_kontes' => "Kontes Bonsai {$tahun}-{$i}",
                    'slug' => Str::slug("Kontes Bonsai {$tahun}-{$i}"),
                    'tempat_kontes' => ['Cirebon', 'Bandung', 'Jakarta', 'Surabaya', 'Yogyakarta'][$tahun % 5],
                    'tingkat_kontes' => ['Lokal', 'Regional', 'Nasional'][$i % 3],
                    'link_gmaps' => 'https://maps.google.com?q=' . $tahun,
                    'tanggal_mulai_kontes' => $start,
                    'tanggal_selesai_kontes' => $end,
                    'jumlah_peserta' => $jumlahPeserta,
                    'limit_peserta' => $jumlahPeserta,
                    'harga_tiket_kontes' => rand(0, 50000),
                    'status' => $tahun === 2025 && $i === 1 ? '1' : '0',
                    'created_at' => $start,
                    'updated_at' => $start,
                ]);
            }
        }
    }
}
