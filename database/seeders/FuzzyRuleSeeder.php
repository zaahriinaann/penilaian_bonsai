<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FuzzyRule;
use App\Models\FuzzyRuleDetail;

class FuzzyRuleSeeder extends Seeder
{
    public function run()
    {
        $rules = [
            [
                'rule_name' => 'Rule 1',
                'output' => 'Kurang',
                'inputs' => [
                    'Keseimbangan Optik' => 'Kurang',
                    'Karakteristik Pohon' => 'Kurang',
                    'Karakteristik Pot' => 'Kurang',
                ],
            ],
            [
                'rule_name' => 'Rule 2',
                'output' => 'Cukup',
                'inputs' => [
                    'Keseimbangan Optik' => 'Cukup',
                    'Karakteristik Pohon' => 'Cukup',
                    'Karakteristik Pot' => 'Cukup',
                ],
            ],
            [
                'rule_name' => 'Rule 3',
                'output' => 'Baik',
                'inputs' => [
                    'Keseimbangan Optik' => 'Baik',
                    'Karakteristik Pohon' => 'Baik',
                    'Karakteristik Pot' => 'Baik',
                ],
            ],
            [
                'rule_name' => 'Rule 4',
                'output' => 'Baik Sekali',
                'inputs' => [
                    'Keseimbangan Optik' => 'Baik Sekali',
                    'Karakteristik Pohon' => 'Baik Sekali',
                    'Karakteristik Pot' => 'Baik Sekali',
                ],
            ],
            [
                'rule_name' => 'Rule 5',
                'output' => 'Cukup',
                'inputs' => [
                    'Keseimbangan Optik' => 'Baik Sekali',
                    'Karakteristik Pohon' => 'Kurang',
                    'Karakteristik Pot' => 'Baik',
                ],
            ],
            [
                'rule_name' => 'Rule 6',
                'output' => 'Cukup',
                'inputs' => [
                    'Keseimbangan Optik' => 'Cukup',
                    'Karakteristik Pohon' => 'Baik',
                    'Karakteristik Pot' => 'Kurang',
                ],
            ],
            [
                'rule_name' => 'Rule 7',
                'output' => 'Kurang',
                'inputs' => [
                    'Keseimbangan Optik' => 'Kurang',
                    'Karakteristik Pohon' => 'Baik',
                    'Karakteristik Pot' => 'Kurang',
                ],
            ],
            [
                'rule_name' => 'Rule 8',
                'output' => 'Baik',
                'inputs' => [
                    'Keseimbangan Optik' => 'Baik',
                    'Karakteristik Pohon' => 'Cukup',
                    'Karakteristik Pot' => 'Baik',
                ],
            ],
            [
                'rule_name' => 'Rule 9',
                'output' => 'Baik Sekali',
                'inputs' => [
                    'Keseimbangan Optik' => 'Baik Sekali',
                    'Karakteristik Pohon' => 'Baik',
                    'Karakteristik Pot' => 'Baik Sekali',
                ],
            ],
            [
                'rule_name' => 'Rule 10',
                'output' => 'Baik',
                'inputs' => [
                    'Keseimbangan Optik' => 'Cukup',
                    'Karakteristik Pohon' => 'Baik Sekali',
                    'Karakteristik Pot' => 'Cukup',
                ],
            ],
        ];

        foreach ($rules as $rule) {
            $ruleModel = FuzzyRule::create([
                'rule_name' => $rule['rule_name'],
                'output_himpunan' => $rule['output'],
            ]);

            foreach ($rule['inputs'] as $variable => $himpunan) {
                FuzzyRuleDetail::create([
                    'fuzzy_rule_id' => $ruleModel->id,
                    'input_variable' => $variable,
                    'himpunan' => $himpunan,
                ]);
            }
        }
    }
}
