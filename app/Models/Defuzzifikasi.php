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
        'hasil_defuzzifikasi',
    ];

    public function bonsai()
    {
        return $this->belongsTo(Bonsai::class, 'id_bonsai');
    }

    public function juri()
    {
        return $this->belongsTo(Juri::class, 'id_juri');
    }
}
