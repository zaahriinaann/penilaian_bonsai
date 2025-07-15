<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuzzyRuleDetail extends Model
{
    protected $fillable = ['fuzzy_rule_id', 'input_variable', 'himpunan'];

    public function rule()
    {
        return $this->belongsTo(FuzzyRule::class, 'fuzzy_rule_id');
    }
}
