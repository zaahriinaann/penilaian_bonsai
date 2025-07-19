<?php

namespace App\Services;

use App\Models\FuzzyRule;
use App\Models\FuzzyRuleDetail;
use App\Models\HelperKriteria;
use Illuminate\Support\Facades\DB;

class AutoGenerateFuzzyRuleService
{
    protected array $baseRules = [
        [['Kurang', 'Kurang', 'Kurang', 'Kurang'], 'Kurang'],
        [['Cukup', 'Cukup', 'Cukup', 'Cukup'], 'Cukup'],
        [['Baik', 'Baik', 'Baik', 'Baik'], 'Baik'],
        [['Baik Sekali', 'Baik Sekali', 'Baik Sekali', 'Baik Sekali'], 'Baik Sekali'],
        [['Kurang', 'Cukup', 'Baik', 'Cukup'], 'Cukup'],
        [['Cukup', 'Baik', 'Baik Sekali', 'Baik'], 'Baik'],
        [['Baik Sekali', 'Cukup', 'Cukup', 'Cukup'], 'Cukup'],
        [['Kurang', 'Baik Sekali', 'Baik', 'Baik'], 'Baik'],
        [['Baik', 'Kurang', 'Baik Sekali', 'Cukup'], 'Cukup'],
        [['Baik Sekali', 'Baik Sekali', 'Kurang', 'Cukup'], 'Cukup'],
    ];

    protected array $himpunans = ['Kurang', 'Cukup', 'Baik', 'Baik Sekali'];

    public function generate(): void
    {
        DB::transaction(function () {
            // ✅ HAPUS RULE orphan: kriteria yang sudah tidak ada di helper_domain
            $aktifKriteriaIds = DB::table('helper_domain')
                ->select('id_kriteria')
                ->distinct()
                ->pluck('id_kriteria')
                ->toArray();

            $orphanRuleIds = FuzzyRule::whereNotIn('id_kriteria', $aktifKriteriaIds)->pluck('id');
            if ($orphanRuleIds->isNotEmpty()) {
                FuzzyRuleDetail::whereIn('fuzzy_rule_id', $orphanRuleIds)->delete();
                FuzzyRule::whereIn('id', $orphanRuleIds)->delete();
            }

            // 🔁 Lanjut generate fuzzy_rules berdasarkan helper_domain
            $kriterias = HelperKriteria::all();

            foreach ($kriterias as $kriteria) {
                // Ambil sub_kriteria dari helper_domain saja
                $subNames = DB::table('helper_domain')
                    ->where('id_kriteria', $kriteria->id)
                    ->whereNotNull('sub_kriteria')
                    ->distinct()
                    ->pluck('sub_kriteria')
                    ->values()
                    ->toArray();

                if (empty($subNames)) continue;

                // Bersihkan rule lama utk kriteria ini
                $existingRuleIds = FuzzyRule::where('id_kriteria', $kriteria->id)->pluck('id');
                if ($existingRuleIds->isNotEmpty()) {
                    FuzzyRuleDetail::whereIn('fuzzy_rule_id', $existingRuleIds)->delete();
                    FuzzyRule::whereIn('id', $existingRuleIds)->delete();
                }

                $generatedCombos = [];

                // Simpan 10 rule dasar
                foreach ($this->baseRules as [$inputs, $output]) {
                    $sliced = array_slice($inputs, 0, count($subNames));
                    $key = implode('|', $sliced);
                    $generatedCombos[$key] = true;

                    $rule = FuzzyRule::create([
                        'id_kriteria' => $kriteria->id,
                        'id_sub_kriteria' => null,
                        'input_himpunan' => json_encode($sliced),
                        'output_himpunan' => $output,
                        'is_active' => true,
                    ]);

                    foreach ($sliced as $i => $himpunan) {
                        FuzzyRuleDetail::create([
                            'fuzzy_rule_id' => $rule->id,
                            'input_variable' => $subNames[$i],
                            'himpunan' => $himpunan,
                        ]);
                    }
                }

                // Kombinasi tambahan
                $this->generateCombinations($subNames, count($subNames), $kriteria->id, $generatedCombos);
            }
        });
    }

    private function generateCombinations(array $subNames, int $length, int $kriteriaId, array &$existing): void
    {
        $combinations = $this->cartesianProduct(array_fill(0, $length, $this->himpunans));

        foreach ($combinations as $combo) {
            $key = implode('|', $combo);
            if (isset($existing[$key])) continue;

            $output = $this->inferOutputFromInputs($combo);
            if (!$output) continue;

            $rule = FuzzyRule::create([
                'id_kriteria' => $kriteriaId,
                'id_sub_kriteria' => null,
                'input_himpunan' => json_encode($combo),
                'output_himpunan' => $output,
                'is_active' => true,
            ]);

            foreach ($combo as $i => $himpunan) {
                FuzzyRuleDetail::create([
                    'fuzzy_rule_id' => $rule->id,
                    'input_variable' => $subNames[$i],
                    'himpunan' => $himpunan,
                ]);
            }

            $existing[$key] = true;
        }
    }

    private function cartesianProduct(array $arrays): array
    {
        $result = [[]];
        foreach ($arrays as $propertyValues) {
            $append = [];
            foreach ($result as $product) {
                foreach ($propertyValues as $item) {
                    $append[] = array_merge($product, [$item]);
                }
            }
            $result = $append;
        }
        return $result;
    }

    private function inferOutputFromInputs(array $inputs): ?string
    {
        $counts = array_count_values($inputs);
        arsort($counts);
        $top = array_keys($counts)[0];
        $maxCount = $counts[$top];

        if ($maxCount === count($inputs)) {
            return $top;
        }

        if ($maxCount > floor(count($inputs) / 2)) {
            return $top;
        }

        return null;
    }
}
