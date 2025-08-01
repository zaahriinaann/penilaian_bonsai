<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RekapNilai extends Model
{
    protected $table = 'rekap_nilai';

    protected $fillable = [
        'id_kontes',
        'id_bonsai',
        'skor_akhir',
        'himpunan_akhir',
        'peringkat',
    ];

    public function bonsai()
    {
        return $this->belongsTo(Bonsai::class, 'id_bonsai');
    }

    public function kontes()
    {
        return $this->belongsTo(Kontes::class, 'id_kontes');
    }
}
