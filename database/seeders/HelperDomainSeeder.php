<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HelperDomain;

class HelperDomainSeeder extends Seeder
{
    public function run(): void
    {
        $subKriterias = [
            'Keseimbangan Optik' => 1,
            'Realitas Alam' => 2,
            'Penjiwaan' => 3,
        ];

        $himpunans = [
            'Kurang' => [10, 40],
            'Cukup' => [30, 60],
            'Baik' => [50, 80],
            'Baik Sekali' => [70, 90],
        ];

        $idKriteria = 1;
        $kriteriaName = 'Penampilan';
        $idHimpunan = 1;
        $idDomain = 1;

        // Input sub-kriteria
        foreach ($subKriterias as $subName => $subId) {
            foreach ($himpunans as $himpunan => [$min, $max]) {
                HelperDomain::create([
                    'id_kriteria'     => $idKriteria,
                    'kriteria'        => $kriteriaName,
                    'id_sub_kriteria' => $subId,
                    'sub_kriteria'    => $subName,
                    'id_himpunan'     => $idHimpunan++,
                    'himpunan'        => $himpunan,
                    'id_domain'       => $idDomain++,
                    'domain_min'      => $min,
                    'domain_max'      => $max,
                ]);
            }
        }

        // Output (tanpa id_sub_kriteria)
        $outputDomains = [
            'Kurang' => [50, 60],
            'Cukup' => [61, 70],
            'Baik' => [71, 80],
            'Baik Sekali' => [81, 90],
        ];

        foreach ($outputDomains as $himpunan => [$min, $max]) {
            HelperDomain::create([
                'id_kriteria'     => $idKriteria,
                'kriteria'        => $kriteriaName,
                'id_sub_kriteria' => null,
                'sub_kriteria'    => null,
                'id_himpunan'     => $idHimpunan++,
                'himpunan'        => $himpunan,
                'id_domain'       => $idDomain++,
                'domain_min'      => $min,
                'domain_max'      => $max,
            ]);
        }
    }
}
