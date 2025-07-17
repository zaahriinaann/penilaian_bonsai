<?php

namespace App\Services;

use App\Models\Nilai;
use App\Models\HelperDomain;
use App\Models\Defuzzifikasi;
use App\Models\Hasil;
use App\Models\RekapNilai;
use Illuminate\Support\Facades\DB;

class FuzzyMamDaniService
{
    private function muTri(float $x, float $a, float $b, float $c): float
    {
        if ($x < $a || $x > $c) return 0;
        if ($x == $b) return 1;
        return $x < $b ? ($x - $a) / ($b - $a) : ($c - $x) / ($c - $b);
    }

    private function muTrapezoid(float $x, float $a, float $b, float $c, float $d): float
    {
        if ($x <= $a || $x >= $d) return 0;
        if ($x >= $b && $x <= $c) return 1;
        if ($x > $a && $x < $b) return ($x - $a) / ($b - $a);
        return ($d - $x) / ($d - $c);
    }

    private const MID = [
        'kurang'       => 25,
        'cukup'        => 45,
        'baik'         => 65,
        'baik sekali'  => 85,
    ];

    public function hitungDerajatKeanggotaan(float $x, HelperDomain $d): float
    {
        $a = $d->domain_min;
        $c = $d->domain_max;
        $b = ($a + $c) / 2;

        if (!is_null($d->id_sub_kriteria)) {
            $mid = strtolower($d->himpunan);
            $b   = self::MID[$mid] ?? $b;
        }

        $h = strtolower($d->himpunan);
        if ($h === 'kurang') {
            return $this->muTrapezoid($x, $a - 10, $a, $b, $c);
        } elseif ($h === 'baik sekali') {
            return $this->muTrapezoid($x, $a, $b, $c, $c + 10);
        }

        return $this->muTri($x, $a, $b, $c);
    }

    /**
     * Rule inference + pencatatan aktif
     */
    private function hitungRuleInference(
        array $inputHimpunan,
        array $rules,
        int $kontesId,
        int $bonsaiId,
        int $juriId,
        int $kriteriaId
    ): float {
        $outputs = [];
        foreach ($rules as $rule) {
            $degrees = [];
            foreach ($rule['antecedent'] as $k => $h) {
                $degrees[] = $inputHimpunan[$k][$h] ?? 0.0;
            }
            $alpha = min($degrees);
            if ($alpha > 0) {
                // Catat ke DB
                DB::table('hasil_fuzzy_rules')->insert([
                    'id_kontes'     => $kontesId,
                    'id_bonsai'     => $bonsaiId,
                    'id_juri'       => $juriId,
                    'id_kriteria'   => $kriteriaId,
                    'fuzzy_rule_id' => $rule['id'],
                    'alpha'         => $alpha,
                    'z_value'       => $rule['consequent']['nilai'],
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);

                $outputs[] = ['alpha' => $alpha, 'z' => $rule['consequent']['nilai']];
            }
        }

        if (empty($outputs)) return 0.0;

        $num = $dem = 0.0;
        foreach ($outputs as $o) {
            $num += $o['alpha'] * $o['z'];
            $dem += $o['alpha'];
        }
        return $dem ? round($num / $dem, 2) : 0.0;
    }

    public function hitungFuzzyPerJuri(int $bonsaiId, int $juriId, int $kontesId, ?string $jsonRules = null): float
    {
        $groups = Nilai::where('id_bonsai', $bonsaiId)
            ->where('id_juri', $juriId)
            ->where('id_kontes', $kontesId)
            ->get()
            ->groupBy('id_kriteria');

        if ($groups->isEmpty()) return 0.0;

        $rules = $jsonRules ? json_decode($jsonRules, true)['rules'] : [];

        $sumZ = 0.0;
        $countK = 0;

        foreach ($groups as $idK => $vals) {
            $domains = HelperDomain::where('id_kriteria', $idK)
                ->whereNull('id_sub_kriteria')
                ->get();
            if ($domains->isEmpty()) continue;

            $muInput = [];
            foreach ($domains as $d) {
                $hName = $d->himpunan;
                $muVals = $vals
                    ->filter(fn($n) => strtolower($n->himpunan) === strtolower($hName) && $n->derajat_anggota > 0)
                    ->pluck('derajat_anggota');
                $muInput[$idK][$hName] = $muVals->isNotEmpty() ? $muVals->min() : 0.0;
            }

            $zRule = !empty($rules)
                ? $this->hitungRuleInference(
                    [$idK => $muInput[$idK]],
                    $rules,
                    $kontesId,
                    $bonsaiId,
                    $juriId,
                    $idK
                )
                : null;

            $alphaList = array_filter($muInput[$idK], fn($v) => $v > 0);
            if (empty($alphaList)) continue;

            $num = $dem = 0.0;
            foreach ($alphaList as $hName => $α) {
                $d = $domains->firstWhere('himpunan', $hName);
                if (!$d) continue;
                $a = $d->domain_min;
                $c = $d->domain_max;
                $b = ($a + $c) / 2;
                $aCut = $a + $α * ($b - $a);
                $cCut = $c - $α * ($c - $b);
                $zClassic = ($aCut + $b + $cCut) / 3;
                $num += $zClassic * $α;
                $dem += $α;
            }
            $zClassic = $dem ? round($num / $dem, 2) : 0.0;

            $zFinal = is_numeric($zRule) && $zRule > 0
                ? round(($zClassic + $zRule) / 2, 2)
                : $zClassic;

            Defuzzifikasi::updateOrCreate(
                ['id_kontes' => $kontesId, 'id_bonsai' => $bonsaiId, 'id_juri' => $juriId, 'id_kriteria' => $idK],
                ['hasil_defuzzifikasi' => $zFinal]
            );

            $sumZ += $zFinal;
            $countK++;
        }

        return $countK ? round($sumZ / $countK, 2) : 0.0;
    }

    public function hitungRekapAkhir(int $bonsaiId, int $kontesId): ?float
    {
        $data = Defuzzifikasi::where('id_bonsai', $bonsaiId)
            ->where('id_kontes', $kontesId)
            ->get();
        if ($data->isEmpty()) return null;

        $avgByK = $data->groupBy('id_kriteria')->map(function ($g) {
            $avg = round($g->avg('hasil_defuzzifikasi'), 2);
            $topHimp = $g->groupBy('hasil_himpunan')->sortByDesc(fn($c) => $c->count())->keys()->first();
            Hasil::updateOrCreate(
                ['id_bonsai' => $g->first()->id_bonsai, 'id_kontes' => $g->first()->id_kontes, 'id_kriteria' => $g->first()->id_kriteria],
                ['hasil_defuzzifikasi' => $avg, 'hasil_himpunan' => $topHimp]
            );
            return $avg;
        });

        $total = round($avgByK->sum(), 2);
        $avgskor = round($avgByK->avg(), 2);
        $himpAkhir = match (true) {
            $avgskor >= 85 => 'Baik Sekali',
            $avgskor >= 65 => 'Baik',
            $avgskor >= 45 => 'Cukup',
            default        => 'Kurang',
        };

        RekapNilai::updateOrCreate(
            ['id_kontes' => $kontesId, 'id_bonsai' => $bonsaiId],
            ['skor_akhir' => $total, 'himpunan_akhir' => $himpAkhir]
        );

        return $total;
    }
}
