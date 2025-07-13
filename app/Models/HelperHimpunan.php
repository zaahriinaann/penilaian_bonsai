<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelperHimpunan extends Model
{
    use HasFactory;

    protected $table = 'helper_himpunan';

    protected $guarded = [];

    public static function ValidateHimpunan($data, $himpunanList)
    {
        $existing = self::where('id_sub_kriteria', $data['id_sub_kriteria'])->exists();

        if ($existing) {
            return [
                'existing' => true,
                'message' => 'Himpunan sudah ada.',
                'data' => self::where('id_sub_kriteria', $data['id_sub_kriteria'])->get()->toArray(),
            ];
        }

        $created = [];
        foreach ($himpunanList as $index => $himpunan) {
            $created[] = self::create([
                'id_kriteria' => $data['id_kriteria'],
                'kriteria' => $data['kriteria'],
                'id_sub_kriteria' => $data['id_sub_kriteria'],
                'sub_kriteria' => $data['sub_kriteria'],
                'id_himpunan' => $index + 1,
                'himpunan' => $himpunan,
            ])->toArray();
        }

        return [
            'existing' => false,
            'message' => 'Himpunan berhasil ditambahkan.',
            'data' => $created,
        ];
    }
}
