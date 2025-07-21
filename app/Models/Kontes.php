<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kontes extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'kontes';
    protected $guarded = [];


    public function pendaftaran()
    {
        return $this->hasMany(PendaftaranKontes::class);
    }

    public function juri()
    {
        return $this->belongsToMany(User::class, 'kontes_juri', 'kontes_id', 'juri_id');
    }

    public function bonsai()
    {
        return $this->belongsToMany(Bonsai::class, 'pendaftaran_kontes', 'kontes_id', 'bonsai_id');
    }
}
