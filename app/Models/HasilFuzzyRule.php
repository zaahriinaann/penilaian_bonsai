<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilFuzzyRule extends Model
{
    use HasFactory;

    protected $table = 'hasil_fuzzy_rules';

    protected $fillable = [
        'id_kontes',
        'id_bonsai',
        'id_juri',
        'id_kriteria',
        'fuzzy_rule_id',
        'alpha',
        'z_value',
    ];

    public function rule()
    {
        return $this->belongsTo(FuzzyRule::class, 'fuzzy_rule_id');
    }
}
