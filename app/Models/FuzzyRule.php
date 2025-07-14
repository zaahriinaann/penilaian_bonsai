<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuzzyRule extends Model
{
    protected $fillable = ['rule_name', 'output_himpunan'];

    public function details()
    {
        return $this->hasMany(FuzzyRuleDetail::class, 'fuzzy_rule_id');
    }
}
