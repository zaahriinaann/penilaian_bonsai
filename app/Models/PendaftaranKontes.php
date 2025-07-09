<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PendaftaranKontes extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'pendaftaran_kontes';
    protected $guarded = [];

    public function kontes()
    {
        return $this->belongsTo(Kontes::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bonsai()
    {
        return $this->belongsTo(Bonsai::class);
    }
}
