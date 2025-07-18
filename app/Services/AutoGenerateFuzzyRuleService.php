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
            $kriterias = HelperKriteria::with('subKriterias')->get();

            foreach ($kriterias as $kriteria) {
                $subs = $kriteria->subKriterias;
                if ($subs->isEmpty()) continue;

                $subCount = $subs->count();

                // Bersihkan rule lama
                $ruleIds = FuzzyRule::where('id_kriteria', $kriteria->id)->pluck('id');
                if ($ruleIds->isNotEmpty()) {
                    FuzzyRuleDetail::whereIn('fuzzy_rule_id', $ruleIds)->delete();
                    FuzzyRule::whereIn('id', $ruleIds)->delete();
                }

                $generatedCombos = [];

                // Simpan 10 rule dasar dulu
                foreach ($this->baseRules as [$inputs, $output]) {
                    $sliced = array_slice($inputs, 0, $subCount);
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
                            'input_variable' => $subs[$i]->sub_kriteria,
                            'himpunan' => $himpunan,
                        ]);
                    }
                }

                // Generate kombinasi tambahan otomatis
                $this->generateCombinations(
                    $subs->pluck('sub_kriteria')->all(),
                    $subCount,
                    $kriteria->id,
                    $generatedCombos
                );
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

            // Simpan rule baru
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

        // Jika semua sama (jumlahnya sama dengan panjang input)
        if ($maxCount === count($inputs)) {
            return $top;
        }

        // Jika mayoritas (minimal > floor(n / 2))
        if ($maxCount > floor(count($inputs) / 2)) {
            return $top;
        }

        return null; // Tidak bisa diputuskan → skip
    }
}
