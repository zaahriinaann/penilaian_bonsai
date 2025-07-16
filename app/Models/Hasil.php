<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hasil extends Model
{
    use HasFactory;

    protected $table = 'hasil';
    protected $fillable = [
        'id_bonsai',
        'id_kontes',
        'id_kriteria',
        'hasil_defuzzifikasi',
        'hasil_himpunan',
        'id_hasil_himpunan',
    ];
}
