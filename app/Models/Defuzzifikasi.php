<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Defuzzifikasi extends Model
{
    use HasFactory;

    protected $table = 'defuzzifikasi';

    protected $fillable = [
        'id_kontes',
        'id_bonsai',
        'id_juri',
        'id_kriteria',
        'hasil_defuzzifikasi',
        'hasil_himpunan',
        'id_hasil_himpunan',
    ];

    public function bonsai()
    {
        return $this->belongsTo(Bonsai::class, 'id_bonsai');
    }

    public function juri()
    {
        return $this->belongsTo(Juri::class, 'id_juri');
    }

    public function kriteria()
    {
        return $this->belongsTo(HelperKriteria::class, 'id_kriteria');
    }

    public function hasilHimpunan()
    {
        return $this->belongsTo(HelperDomain::class, 'id_hasil_himpunan');
    }

    public function helperDomain()
    {
        return $this->belongsTo(HelperDomain::class, 'id_kriteria', 'id_kriteria')
            ->whereNull('id_sub_kriteria');
    }
}
