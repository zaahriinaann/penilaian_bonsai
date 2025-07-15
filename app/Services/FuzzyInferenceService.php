<?php

namespace App\Services;

use App\Models\Nilai;
use App\Models\FuzzyRule;

class FuzzyInferenceService
{
    /**
     * Proses inferensi rules fuzzy Mamdani untuk 1 bonsai-juri-kontes,
     * return: array output_himpunan => μ (misal: ['Kurang'=>0.2, 'Cukup'=>0.5, ...])
     */
    public static function inferensi($bonsaiId, $juriId, $kontesId)
    {
        // 1. Baca hasil fuzzy input
        $muMap = [];
        foreach (
            Nilai::where('id_bonsai', $bonsaiId)
                ->where('id_juri', $juriId)
                ->where('id_kontes', $kontesId)->get() as $n
        ) {
            $muMap[$n->sub_kriteria][$n->himpunan] = $n->derajat_anggota;
        }

        // 2. Proses rule Mamdani
        $rules = FuzzyRule::with('details')->get();
        $outputMu = []; // output_himpunan => μ

        foreach ($rules as $rule) {
            $minMu = null;
            foreach ($rule->details as $detail) {
                $inputVar = $detail->input_variable;
                $himpunan = $detail->himpunan;
                $mu = $muMap[$inputVar][$himpunan] ?? 0;
                $minMu = is_null($minMu) ? $mu : min($minMu, $mu);
            }
            if ($minMu > 0) {
                $out = $rule->output_himpunan;
                $outputMu[$out] = max($outputMu[$out] ?? 0, $minMu); // agregasi Mamdani
            }
        }

        return $outputMu; // array: output_himpunan => μ
    }
}
