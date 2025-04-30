<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bonsai extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'bonsai';
    protected $guarded = [];
}
