<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelperDomain extends Model
{
    use HasFactory;
    protected $table = 'helper_domain';

    protected $fillable = [
        'id_kriteria',
        'kriteria',
        'id_sub_kriteria',
        'sub_kriteria',
        'id_himpunan',
        'himpunan',
        'id_domain',
        'domain_min',
        'domain_max',
    ];

    public function subKriteria()
    {
        return $this->belongsTo(HelperSubKriteria::class, 'id_sub_kriteria', 'id_sub_kriteria');
    }

    // public static function ValidateDomain($dataList, $minList, $maxList)
    // {
    //     $first = $dataList[0] ?? null;

    //     if (!$first) {
    //         return [
    //             'existing' => false,
    //             'message' => 'Data himpunan kosong.',
    //             'data' => [],
    //         ];
    //     }

    //     $existing = self::where('id_sub_kriteria', $first['id_sub_kriteria'])->exists();

    //     if ($existing) {
    //         foreach ($dataList as $index => $item) {
    //             self::where('id_sub_kriteria', $item['id_sub_kriteria'])
    //                 ->where('id_himpunan', $item['id_himpunan'])
    //                 ->update([
    //                     'domain_min' => $minList[$index] ?? null,
    //                     'domain_max' => $maxList[$index] ?? null,
    //                 ]);
    //         }

    //         return [
    //             'existing' => true,
    //             'message' => 'Domain sudah ada, diperbarui.',
    //             'data' => self::where('id_sub_kriteria', $first['id_sub_kriteria'])->get()->toArray(),
    //         ];
    //     }

    //     $created = [];
    //     foreach ($dataList as $index => $item) {
    //         $created[] = self::create([
    //             'id_kriteria' => $item['id_kriteria'],
    //             'kriteria' => $item['kriteria'],
    //             'id_sub_kriteria' => $item['id_sub_kriteria'],
    //             'sub_kriteria' => $item['sub_kriteria'],
    //             'id_himpunan' => $item['id_himpunan'],
    //             'himpunan' => $item['himpunan'],
    //             'id_domain' => $index + 1,
    //             'domain_min' => $minList[$index] ?? null,
    //             'domain_max' => $maxList[$index] ?? null,
    //         ])->toArray();
    //     }

    //     return [
    //         'existing' => false,
    //         'message' => 'Domain berhasil disimpan.',
    //         'data' => $created,
    //     ];
    // }

    public static function ValidateDomain(array $dataList, array $minList, array $maxList)
    {
        $first = $dataList[0] ?? null;

        if (!$first) {
            return [
                'existing' => false,
                'message' => 'Data himpunan kosong.',
                'data' => [],
            ];
        }

        $existing = self::where('id_sub_kriteria', $first['id_sub_kriteria'])->exists();

        if ($existing) {
            foreach ($dataList as $index => $item) {
                self::where('id_sub_kriteria', $item['id_sub_kriteria'])
                    ->where('id_himpunan', $item['id_himpunan'])
                    ->update([
                        'domain_min' => $minList[$index] ?? null,
                        'domain_max' => $maxList[$index] ?? null,
                    ]);
            }

            $updated = self::where('id_sub_kriteria', $first['id_sub_kriteria'])->get()->toArray();

            return [
                'existing' => true,
                'message' => 'Domain sudah ada dan berhasil diperbarui.',
                'data' => $updated,
            ];
        }

        $created = [];
        foreach ($dataList as $index => $item) {
            $created[] = self::create([
                'id_kriteria'     => $item['id_kriteria'],
                'kriteria'        => $item['kriteria'],
                'id_sub_kriteria' => $item['id_sub_kriteria'],
                'sub_kriteria'    => $item['sub_kriteria'],
                'id_himpunan'     => $item['id_himpunan'],
                'himpunan'        => $item['himpunan'],
                'id_domain'       => $index + 1,
                'domain_min'      => $minList[$index] ?? null,
                'domain_max'      => $maxList[$index] ?? null,
            ])->toArray();
        }

        return [
            'existing' => false,
            'message' => 'Domain berhasil disimpan.',
            'data' => $created,
        ];
    }

    public static function getCentroidAndMu($nilai, $idSubKriteria, $himpunan)
    {
        $domain = self::where('id_sub_kriteria', $idSubKriteria)
            ->where('himpunan', $himpunan)
            ->first();

        if (!$domain) return [0, 0];

        $min = $domain->domain_min;
        $max = $domain->domain_max;
        $mid = ($min + $max) / 2;

        // Cek apakah nilai di luar range
        if ($nilai < $min || $nilai > $max) {
            return [0, round($mid, 2)]; // mu = 0, z = centroid
        }

        // Hitung derajat keanggotaan (mu)
        $mu = $nilai <= $mid
            ? ($nilai - $min) / ($mid - $min)
            : ($max - $nilai) / ($max - $mid);

        return [round($mu, 4), round($mid, 2)]; // [mu, z]
    }
}
