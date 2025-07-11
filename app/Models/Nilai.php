<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Nilai extends Model
{
    protected $fillable = [
        'id_kontes',
        'id_pendaftaran',
        'id_peserta',
        'id_juri',
        'id_bonsai',
        'id_kriteria_penilaian',
        'd_keanggotaan',
        'defuzzifikasi',
    ];

    // Relasi ke kontes
    public function kontes()
    {
        return $this->belongsTo(Kontes::class, 'id_kontes');
    }

    // Relasi ke PendaftaranKontes
    public function pendaftaranKontes()
    {
        return $this->belongsTo(PendaftaranKontes::class, 'id_pendaftaran');
    }

    // Relasi ke peserta (user)
    public function peserta()
    {
        return $this->belongsTo(User::class, 'id_peserta');
    }

    // Relasi ke juri
    public function juri()
    {
        return $this->belongsTo(Juri::class, 'id_juri');
    }

    // Relasi ke bonsai
    public function bonsai()
    {
        return $this->belongsTo(Bonsai::class, 'id_bonsai');
    }

    // Relasi ke kriteria dan sub-kriteria
    public function penilaian()
    {
        return $this->belongsTo(Penilaian::class, 'id_kriteria_penilaian');
    }

    public static function sudahDinilai($bonsaiId, $juriId)
    {
        return self::where('id_bonsai', $bonsaiId)
            ->where('id_juri', $juriId)
            ->exists();
    }
}
