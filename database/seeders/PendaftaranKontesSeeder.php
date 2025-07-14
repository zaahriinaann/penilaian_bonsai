<?php

namespace Database\Seeders;

use App\Models\PendaftaranKontes;
use App\Models\Kontes;
use App\Models\Bonsai;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PendaftaranKontesSeeder extends Seeder
{
    public function run(): void
    {
        $distribusi = [
            2021 => 10,
            2022 => 10,
            2023 => 9,
            2024 => 2,
            2025 => 7,
        ];

        $bonsaiList = Bonsai::with('user')->get();
        $kontesList = Kontes::all();
        $counter = 1;

        foreach ($distribusi as $tahun => $jumlahPendaftaran) {
            // ✅ Perbaikan disini: pakai Carbon::parse
            $kontesTahunIni = $kontesList->filter(function ($k) use ($tahun) {
                return Carbon::parse($k->tanggal_mulai_kontes)->year == $tahun;
            })->values();

            if ($kontesTahunIni->isEmpty()) continue;

            for ($i = 0; $i < $jumlahPendaftaran; $i++) {
                $kontes = $kontesTahunIni->random();
                $bonsai = $bonsaiList->random();

                // Cek apakah bonsai sudah daftar ke kontes ini
                $sudahTerdaftar = PendaftaranKontes::where('kontes_id', $kontes->id)
                    ->where('bonsai_id', $bonsai->id)
                    ->exists();

                if ($sudahTerdaftar) {
                    $i--; // ulangi jika dobel
                    continue;
                }

                PendaftaranKontes::create([
                    'kontes_id' => $kontes->id,
                    'user_id' => $bonsai->user_id,
                    'bonsai_id' => $bonsai->id,
                    'kelas' => $bonsai->kelas ?? 'Pemula',
                    'nomor_pendaftaran' => 'P' . str_pad($counter, 3, '0', STR_PAD_LEFT),
                    'nomor_juri' => 'J' . rand(100, 999),
                    'status' => '0',
                    'created_at' => $kontes->tanggal_mulai_kontes,
                    'updated_at' => $kontes->tanggal_mulai_kontes,
                ]);

                $counter++;
            }
        }
    }
}
