<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hasil extends Model
{
    use HasFactory;

    protected $table = 'hasil';

    protected $fillable = [
        'id_kontes',
        'id_bonsai',
        'id_kriteria',
        'rata_defuzzifikasi',
        'rata_himpunan',
        'id_rata_himpunan',
    ];
}
