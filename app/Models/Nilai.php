<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Nilai extends Model
{
    protected $table = 'nilais';
    protected $guarded = [];

    public function kontes(): BelongsTo
    {
        return $this->belongsTo(Kontes::class, 'id_kontes');
    }

    public function peserta(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_peserta');
    }

    public function juri(): BelongsTo
    {
        return $this->belongsTo(Juri::class, 'id_juri');
    }

    public function bonsai(): BelongsTo
    {
        return $this->belongsTo(Bonsai::class, 'id_bonsai');
    }

    public function kriteriaPenilaian(): BelongsTo
    {
        return $this->belongsTo(Penilaian::class, 'id_kriteria_penilaian');
    }

    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(PendaftaranKontes::class, 'id_pendaftaran');
    }
}
