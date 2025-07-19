<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelperSubKriteria extends Model
{
    use HasFactory;

    protected $table = 'helper_sub_kriteria';

    protected $guarded = [];

    public function kriteria()
    {
        return $this->belongsTo(HelperKriteria::class, 'id_kriteria');
    }

    public static function ValidateSubKriteria($data)
    {
        $existing = self::where('id_kriteria', $data['id'])
            ->where('sub_kriteria', $data['sub_kriteria'])
            ->first();

        if ($existing) {
            $existing->update(['sub_kriteria' => $data['sub_kriteria']]);

            return [
                'existing' => true,
                'message' => 'Sub Kriteria sudah ada, diperbarui.',
                'data' => $existing->toArray(),
            ];
        }

        $new = self::create([
            'id_kriteria' => $data['id'],
            'kriteria' => $data['kriteria'],
            'id_sub_kriteria' => self::max('id_sub_kriteria') + 1,
            'sub_kriteria' => $data['sub_kriteria'],
        ]);

        return [
            'existing' => false,
            'message' => 'Sub Kriteria baru ditambahkan.',
            'data' => $new->toArray(),
        ];
    }
}
