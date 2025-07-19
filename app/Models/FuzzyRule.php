<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuzzyRule extends Model
{
    protected $table = 'fuzzy_rules';
    protected $guarded = []; // <- wajib ada ini kalau kamu pakai create()

    public function details()
    {
        return $this->hasMany(FuzzyRuleDetail::class, 'fuzzy_rule_id');
    }

    public function kriteria()
    {
        return $this->belongsTo(HelperKriteria::class, 'id_kriteria');
    }
}
