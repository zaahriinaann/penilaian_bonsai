<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penilaian extends Model
{

    protected $table = 'penilaian';
    protected $fillable = ['kriteria', 'sub_kriteria', 'himpunan', 'min', 'max'];
}
