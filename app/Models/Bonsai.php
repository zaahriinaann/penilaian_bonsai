<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bonsai extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bonsai';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kontes()
    {
        return $this->belongsTo(Kontes::class, 'kontes_id');
    }

    public function pendaftaran()
    {
        return $this->belongsTo(PendaftaranKontes::class, 'pendaftaran_id');
    }

    public function nilai()
    {
        return $this->hasMany(Nilai::class, 'id_bonsai');
    }

    public function defuzzifikasi()
    {
        return $this->hasMany(Defuzzifikasi::class, 'id_bonsai');
    }

    public function rekapNilai()
    {
        return $this->hasOne(RekapNilai::class, 'id_bonsai');
    }

    public function pendaftaranKontes()
    {
        return $this->hasOne(PendaftaranKontes::class, 'bonsai_id');
    }
}
