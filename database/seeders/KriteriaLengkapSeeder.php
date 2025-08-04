<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KriteriaLengkapSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $kriteriaList = [
            'Penampilan' => [
                'Keseimbangan Optik',
                'Realitas Alam',
                'Penjiwaan'
            ],
            'Gerak Dasar' => [
                'Gaya',
                'Karakter',
                'Alur Gerak'
            ],
            'Keserasian' => [
                'Kesehatan',
                'Peletakkan di Wadah/Pot',
                'Kesan Tua'
            ],
            'Kematangan' => [
                'Tahapan',
                'Keseimbangan Anatomi',
                'Dimensi',
                'Komposisi'
            ]
        ];

        // Himpunan untuk input
        $himpunans = [
            1 => ['Baik Sekali', 70, 90],
            2 => ['Baik', 50, 80],
            3 => ['Cukup', 30, 60],
            4 => ['Kurang', 10, 40],
        ];

        // Domain output
        $outputDomains = [
            'Kurang' => [50, 65],
            'Cukup' => [55, 75],
            'Baik' => [65, 85],
            'Baik Sekali' => [75, 90],
        ];

        $outputDomainId = 1000;
        $masterSubId = 1; // untuk isi kolom id_sub_kriteria di helper_sub_kriteria

        foreach ($kriteriaList as $kriteriaName => $subs) {
            $kriteria = DB::table('helper_kriteria')
                ->where('kriteria', $kriteriaName)
                ->first();

            if (!$kriteria) continue;

            foreach ($subs as $sub) {
                // Insert ke helper_sub_kriteria (id_sub_kriteria wajib diisi)
                $subId = DB::table('helper_sub_kriteria')->insertGetId([
                    'id_kriteria' => $kriteria->id,
                    'kriteria' => $kriteria->kriteria,
                    'id_sub_kriteria' => $masterSubId++, // unik
                    'sub_kriteria' => $sub,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                // Insert helper_himpunan + helper_domain (input) + penilaian
                foreach ($himpunans as $idDomain => [$namaHimpunan, $min, $max]) {
                    // helper_himpunan
                    $idHimpunan = DB::table('helper_himpunan')->insertGetId([
                        'id_kriteria' => $kriteria->id,
                        'kriteria' => $kriteria->kriteria,
                        'id_sub_kriteria' => $subId,
                        'sub_kriteria' => $sub,
                        'id_himpunan' => 0, // kalau ada tabel master isi sesuai ID
                        'himpunan' => $namaHimpunan,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    // helper_domain input
                    DB::table('helper_domain')->insert([
                        'id_kriteria' => $kriteria->id,
                        'kriteria' => $kriteria->kriteria,
                        'id_sub_kriteria' => $subId, // wajib isi untuk input
                        'sub_kriteria' => $sub,
                        'id_himpunan' => $idHimpunan,
                        'himpunan' => $namaHimpunan,
                        'id_domain' => $idDomain,
                        'domain_min' => $min,
                        'domain_max' => $max,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    // penilaian
                    DB::table('penilaian')->insert([
                        'kriteria' => $kriteria->kriteria,
                        'sub_kriteria' => $sub,
                        'himpunan' => $namaHimpunan,
                        'min' => $min,
                        'max' => $max,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }

            // helper_domain output (id_sub_kriteria NULL)
            foreach ($outputDomains as $namaDomain => $range) {
                $idHimpunan = DB::table('helper_himpunan')
                    ->where('id_kriteria', $kriteria->id)
                    ->where('himpunan', $namaDomain)
                    ->value('id');

                DB::table('helper_domain')->insert([
                    'id_kriteria' => $kriteria->id,
                    'kriteria' => $kriteria->kriteria,
                    'id_sub_kriteria' => null, // beda dari input
                    'sub_kriteria' => null,
                    'id_himpunan' => $idHimpunan,
                    'himpunan' => $namaDomain,
                    'id_domain' => $outputDomainId++,
                    'domain_min' => $range[0],
                    'domain_max' => $range[1],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}
