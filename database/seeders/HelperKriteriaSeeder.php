<?php

namespace Database\Seeders;

use App\Models\HelperKriteria;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HelperKriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $data = [
            [
                'kriteria' => 'Penampilan',
                // 'himpunan' => [
                //     ['himpunan' => 'Kurang', 'min' => 10, 'max' => 40],
                //     ['himpunan' => 'Cukup', 'min' => 30, 'max' => 60],
                //     ['himpunan' => 'Baik', 'min' => 50, 'max' => 80],
                //     ['himpunan' => 'Baik Sekali', 'min' => 70, 'max' => 90],
                // ]
            ],
            [
                'kriteria' => 'Gerak Dasar',
                // 'himpunan' => [
                //     ['himpunan' => 'Kurang', 'min' => 10, 'max' => 40],
                //     ['himpunan' => 'Cukup', 'min' => 30, 'max' => 60],
                //     ['himpunan' => 'Baik', 'min' => 50, 'max' => 80],
                //     ['himpunan' => 'Baik Sekali', 'min' => 70, 'max' => 90],
                // ]
            ],
            [
                'kriteria' => 'Keserasian',
                // 'himpunan' => [
                //     ['himpunan' => 'Kurang', 'min' => 10, 'max' => 40],
                //     ['himpunan' => 'Cukup', 'min' => 30, 'max' => 60],
                //     ['himpunan' => 'Baik', 'min' => 50, 'max' => 80],
                //     ['himpunan' => 'Baik Sekali', 'min' => 70, 'max' => 90],
                // ]
            ],
            [
                'kriteria' => 'Kematangan',
                // 'himpunan' => [
                //     ['himpunan' => 'Kurang', 'min' => 10, 'max' => 40],
                //     ['himpunan' => 'Cukup', 'min' => 30, 'max' => 60],
                //     ['himpunan' => 'Baik', 'min' => 50, 'max' => 80],
                //     ['himpunan' => 'Baik Sekali', 'min' => 70, 'max' => 90],
                // ]
            ],
        ];

        foreach ($data as $item) {
            HelperKriteria::create([
                'kriteria' => $item['kriteria'],
                // 'himpunan' => $himpunan['himpunan'],
                // 'min' => $himpunan['min'],
                // 'max' => $himpunan['max'],
            ]);
        }
    }
}
