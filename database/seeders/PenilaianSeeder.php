<?php

namespace Database\Seeders;

use App\Models\Penilaian;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PenilaianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'kriteria' => 'Penampilan',
                'sub_kriteria' => 'Keseimbangan Optik',
                'himpunan' => [
                    ['himpunan' => 'Kurang', 'min' => 10, 'max' => 40],
                    ['himpunan' => 'Cukup', 'min' => 30, 'max' => 60],
                    ['himpunan' => 'Baik', 'min' => 50, 'max' => 80],
                    ['himpunan' => 'Baik Sekali', 'min' => 70, 'max' => 90],
                ]
            ],
            [
                'kriteria' => 'Penampilan',
                'sub_kriteria' => 'Realitas Alam',
                'himpunan' => [
                    ['himpunan' => 'Kurang', 'min' => 10, 'max' => 40],
                    ['himpunan' => 'Cukup', 'min' => 30, 'max' => 60],
                    ['himpunan' => 'Baik', 'min' => 50, 'max' => 80],
                    ['himpunan' => 'Baik Sekali', 'min' => 70, 'max' => 90],
                ]
            ],
            [
                'kriteria' => 'Penampilan',
                'sub_kriteria' => 'Penjiwaan',
                'himpunan' => [
                    ['himpunan' => 'Kurang', 'min' => 10, 'max' => 40],
                    ['himpunan' => 'Cukup', 'min' => 30, 'max' => 60],
                    ['himpunan' => 'Baik', 'min' => 50, 'max' => 80],
                    ['himpunan' => 'Baik Sekali', 'min' => 70, 'max' => 90],
                ]
            ],
            [
                'kriteria' => 'Gerak Dasar',
                'sub_kriteria' => 'Gaya',
                'himpunan' => [
                    ['himpunan' => 'Kurang', 'min' => 10, 'max' => 40],
                    ['himpunan' => 'Cukup', 'min' => 30, 'max' => 60],
                    ['himpunan' => 'Baik', 'min' => 50, 'max' => 80],
                    ['himpunan' => 'Baik Sekali', 'min' => 70, 'max' => 90],
                ]
            ],
            [
                'kriteria' => 'Gerak Dasar',
                'sub_kriteria' => 'Karakter',
                'himpunan' => [
                    ['himpunan' => 'Kurang', 'min' => 10, 'max' => 40],
                    ['himpunan' => 'Cukup', 'min' => 30, 'max' => 60],
                    ['himpunan' => 'Baik', 'min' => 50, 'max' => 80],
                    ['himpunan' => 'Baik Sekali', 'min' => 70, 'max' => 90],
                ]
            ],
            [
                'kriteria' => 'Gerak Dasar',
                'sub_kriteria' => 'Alur Gerak',
                'himpunan' => [
                    ['himpunan' => 'Kurang', 'min' => 10, 'max' => 40],
                    ['himpunan' => 'Cukup', 'min' => 30, 'max' => 60],
                    ['himpunan' => 'Baik', 'min' => 50, 'max' => 80],
                    ['himpunan' => 'Baik Sekali', 'min' => 70, 'max' => 90],
                ]
            ],
            [
                'kriteria' => 'Keserasian',
                'sub_kriteria' => 'Kesehatan',
                'himpunan' => [
                    ['himpunan' => 'Kurang', 'min' => 10, 'max' => 40],
                    ['himpunan' => 'Cukup', 'min' => 30, 'max' => 60],
                    ['himpunan' => 'Baik', 'min' => 50, 'max' => 80],
                    ['himpunan' => 'Baik Sekali', 'min' => 70, 'max' => 90],
                ]
            ],
            [
                'kriteria' => 'Keserasian',
                'sub_kriteria' => 'Peletakkan di Wadah/Pot',
                'himpunan' => [
                    ['himpunan' => 'Kurang', 'min' => 10, 'max' => 40],
                    ['himpunan' => 'Cukup', 'min' => 30, 'max' => 60],
                    ['himpunan' => 'Baik', 'min' => 50, 'max' => 80],
                    ['himpunan' => 'Baik Sekali', 'min' => 70, 'max' => 90],
                ]
            ],
            [
                'kriteria' => 'Keserasian',
                'sub_kriteria' => 'Kesan Tua',
                'himpunan' => [
                    ['himpunan' => 'Kurang', 'min' => 10, 'max' => 40],
                    ['himpunan' => 'Cukup', 'min' => 30, 'max' => 60],
                    ['himpunan' => 'Baik', 'min' => 50, 'max' => 80],
                    ['himpunan' => 'Baik Sekali', 'min' => 70, 'max' => 90],
                ]
            ],
            [
                'kriteria' => 'Kematangan',
                'sub_kriteria' => 'Tahapan',
                'himpunan' => [
                    ['himpunan' => 'Kurang', 'min' => 10, 'max' => 40],
                    ['himpunan' => 'Cukup', 'min' => 30, 'max' => 60],
                    ['himpunan' => 'Baik', 'min' => 50, 'max' => 80],
                    ['himpunan' => 'Baik Sekali', 'min' => 70, 'max' => 90],
                ]
            ],
            [
                'kriteria' => 'Kematangan',
                'sub_kriteria' => 'Keseimbangan Anatomi',
                'himpunan' => [
                    ['himpunan' => 'Kurang', 'min' => 10, 'max' => 40],
                    ['himpunan' => 'Cukup', 'min' => 30, 'max' => 60],
                    ['himpunan' => 'Baik', 'min' => 50, 'max' => 80],
                    ['himpunan' => 'Baik Sekali', 'min' => 70, 'max' => 90],
                ]
            ],
            [
                'kriteria' => 'Kematangan',
                'sub_kriteria' => 'Dimensi',
                'himpunan' => [
                    ['himpunan' => 'Kurang', 'min' => 10, 'max' => 40],
                    ['himpunan' => 'Cukup', 'min' => 30, 'max' => 60],
                    ['himpunan' => 'Baik', 'min' => 50, 'max' => 80],
                    ['himpunan' => 'Baik Sekali', 'min' => 70, 'max' => 90],
                ]
            ],
            [
                'kriteria' => 'Kematangan',
                'sub_kriteria' => 'Komposisi',
                'himpunan' => [
                    ['himpunan' => 'Kurang', 'min' => 10, 'max' => 40],
                    ['himpunan' => 'Cukup', 'min' => 30, 'max' => 60],
                    ['himpunan' => 'Baik', 'min' => 50, 'max' => 80],
                    ['himpunan' => 'Baik Sekali', 'min' => 70, 'max' => 90],
                ]
            ],
        ];

        foreach ($data as $item) {
            foreach ($item['himpunan'] as $himpunan) {
                Penilaian::create([
                    'kriteria' => $item['kriteria'],
                    'sub_kriteria' => $item['sub_kriteria'],
                    'himpunan' => $himpunan['himpunan'],
                    'min' => $himpunan['min'],
                    'max' => $himpunan['max'],
                ]);
            }
        }
    }
}
