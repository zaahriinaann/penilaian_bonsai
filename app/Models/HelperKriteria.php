<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelperKriteria extends Model
{
    use HasFactory;

    protected $table = 'helper_kriteria';

    protected $guarded = [];

    // protected $primaryKey = 'id';

    // protected $fillable = [
    //     'kriteria',
    //     'himpunan',
    //     'min',
    //     'max',
    // ];

    // protected $casts = [
    //     'min' => 'integer',
    //     'max' => 'integer',
    // ];
}
