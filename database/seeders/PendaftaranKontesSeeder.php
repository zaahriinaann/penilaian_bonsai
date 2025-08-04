<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PendaftaranKontesSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua kontes beserta tanggal mulai
        $kontesList = DB::table('kontes')->select('id', 'tanggal_mulai_kontes')->get();

        // Ambil semua bonsai
        $bonsaiList = DB::table('bonsai')->select('id', 'user_id', 'kelas')->get();

        if ($bonsaiList->count() < 1) {
            $this->command->warn('Tidak ada bonsai di database, seeder dibatalkan.');
            return;
        }

        foreach ($kontesList as $kontes) {
            // Tentukan daftar bonsai untuk kontes ini
            if ($bonsaiList->count() >= 20) {
                $bonsaiKontes = $bonsaiList->shuffle()->values();
            } else {
                // Gandakan sampai minimal 20
                $bonsaiKontes = collect();
                while ($bonsaiKontes->count() < 20) {
                    $bonsaiKontes = $bonsaiKontes->merge($bonsaiList->shuffle());
                }
                $bonsaiKontes = $bonsaiKontes->take(20)->values();
            }

            // Nomor pendaftaran global
            $nomorPendaftaran = 1;

            // Kelompokkan bonsai per kelas
            $bonsaiByKelas = $bonsaiKontes->groupBy('kelas');

            foreach ($bonsaiByKelas as $kelas => $bonsaiKelas) {
                // Nomor juri per kelas
                $nomorJuri = 1;

                foreach ($bonsaiKelas as $bonsai) {
                    // Random tanggal antara H-5 dan H-1 sebelum kontes mulai
                    $createdAt = Carbon::parse($kontes->tanggal_mulai_kontes)
                        ->subDays(rand(1, 5))
                        ->setTime(rand(8, 18), rand(0, 59), rand(0, 59));

                    DB::table('pendaftaran_kontes')->insert([
                        'kontes_id' => $kontes->id,
                        'user_id' => $bonsai->user_id,
                        'bonsai_id' => $bonsai->id,
                        'kelas' => $kelas,
                        'nomor_pendaftaran' => 'P' . $nomorPendaftaran++,
                        'nomor_juri' => 'J' . $nomorJuri++,
                        'status' => 0,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                        'deleted_at' => null,
                    ]);
                }
            }
        }
    }
}
