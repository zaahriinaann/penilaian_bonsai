<?php

namespace App\Services;

use App\Models\{Nilai, HelperDomain, Defuzzifikasi, Hasil, RekapNilai, FuzzyRule, FuzzyRuleDetail, HasilFuzzyRule};

class FuzzyMamdaniService
{
    private function muTri(float $x, float $a, float $b, float $c): float
    {
        if ($x <= $a || $x >= $c) return 0;
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
        'kurang' => 25,
        'cukup' => 45,
        'baik' => 65,
        'baik sekali' => 85,
    ];

    public function hitungDerajatKeanggotaan(float $x, HelperDomain $d): float
    {
        $a = $d->domain_min;
        $c = $d->domain_max;
        $b = ($a + $c) / 2;

        if (!is_null($d->id_sub_kriteria)) {
            $b = self::MID[strtolower($d->himpunan)] ?? $b;
        }

        $h = strtolower($d->himpunan);

        return match ($h) {
            'kurang'       => $this->muTrapezoid($x, $a - 10, $a, $b, $c),
            'baik sekali'  => $this->muTrapezoid($x, $a, $b, $c, $c + 10),
            default        => $this->muTri($x, $a, $b, $c),
        };
    }

    public function hitungFuzzyPerJuri(int $bonsaiId, int $juriId, int $kontesId): float
    {
        $inputs = Nilai::where('id_bonsai', $bonsaiId)
            ->where('id_juri', $juriId)
            ->where('id_kontes', $kontesId)
            ->get()
            ->groupBy('id_kriteria');

        if ($inputs->isEmpty()) return 0;

        $totalZ = 0;
        $jumlahKriteria = 0;

        foreach ($inputs as $idKriteria => $group) {
            // ambil rules dan details
            $rules = FuzzyRule::with('details')
                ->where('id_kriteria', $idKriteria)
                ->where('is_active', 1)
                ->get();

            $outputDomains = HelperDomain::where('id_kriteria', $idKriteria)
                ->whereNull('id_sub_kriteria')
                ->get();

            if ($outputDomains->isEmpty() || $rules->isEmpty()) continue;

            // kelompokkan input berdasarkan sub_kriteria → himpunan → derajat
            $inputMap = [];
            foreach ($group as $n) {
                if ($n->derajat_anggota > 0) {
                    $inputMap[$n->sub_kriteria][strtolower($n->himpunan)] = $n->derajat_anggota;
                }
            }

            // inferensi: hitung alpha & z berdasarkan aturan
            $inferensi = [];

            foreach ($rules as $rule) {
                $alphas = [];

                foreach ($rule->details as $d) {
                    $sub = $d->input_variable;
                    $himpunan = strtolower($d->himpunan);

                    $α = $inputMap[$sub][$himpunan] ?? 0;
                    $alphas[] = $α;
                }

                $minAlpha = min($alphas);
                if ($minAlpha > 0) {
                    // ambil domain output himpunan
                    $outputDomain = $outputDomains->firstWhere('himpunan', $rule->output_himpunan);
                    if (!$outputDomain) continue;

                    $a = $outputDomain->domain_min;
                    $c = $outputDomain->domain_max;
                    $b = ($a + $c) / 2;

                    $aCut = $a + $minAlpha * ($b - $a);
                    $cCut = $c - $minAlpha * ($c - $b);
                    $z = ($aCut + $b + $cCut) / 3;

                    // Baru simpan ke hasil_fuzzy_rules
                    HasilFuzzyRule::create([
                        'id_kontes'     => $kontesId,
                        'id_bonsai'     => $bonsaiId,
                        'id_juri'       => $juriId,
                        'id_kriteria'   => $idKriteria,
                        'fuzzy_rule_id' => $rule->id,
                        'alpha'         => $minAlpha,
                        'z_value'       => $z,
                    ]);

                    $inferensi[] = [
                        'z' => $z,
                        'α' => $minAlpha,
                        'himpunan' => $outputDomain->himpunan,
                        'id_himpunan' => $outputDomain->id,
                    ];
                }
            }

            if (empty($inferensi)) continue;

            // agregasi defuzzifikasi dengan rata-rata tertimbang
            $num = $den = 0;
            foreach ($inferensi as $inf) {
                $num += $inf['z'] * $inf['α'];
                $den += $inf['α'];
            }

            $zFinal = $den ? round($num / $den, 2) : 0;

            // cari output himpunan dari hasil defuzzifikasi
            $finalOutput = $outputDomains
                ->map(function ($d) use ($zFinal) {
                    $a = $d->domain_min;
                    $c = $d->domain_max;
                    $b = ($a + $c) / 2;
                    $h = strtolower($d->himpunan);

                    $degree = match ($h) {
                        'kurang'       => $this->muTrapezoid($zFinal, $a - 10, $a, $b, $c),
                        'baik sekali'  => $this->muTrapezoid($zFinal, $a, $b, $c, $c + 10),
                        default        => $this->muTri($zFinal, $a, $b, $c),
                    };

                    return ['domain' => $d, 'degree' => $degree];
                })
                ->filter(fn($r) => $r['degree'] > 0)
                ->sortByDesc('degree')
                ->first()['domain'] ?? null;

            $hasilHimpunan = $finalOutput?->himpunan;
            $idHimpunan = $finalOutput?->id;

            Defuzzifikasi::updateOrCreate([
                'id_kontes' => $kontesId,
                'id_bonsai' => $bonsaiId,
                'id_juri' => $juriId,
                'id_kriteria' => $idKriteria,
            ], [
                'hasil_defuzzifikasi' => $zFinal,
                'hasil_himpunan' => $hasilHimpunan,
                'id_hasil_himpunan' => $idHimpunan,
            ]);

            $totalZ += $zFinal;
            $jumlahKriteria++;
        }

        return $jumlahKriteria > 0 ? round($totalZ / $jumlahKriteria, 2) : 0;
    }

    public function hitungRekapAkhir(int $bonsaiId, int $kontesId): ?float
    {
        $data = Defuzzifikasi::where('id_bonsai', $bonsaiId)
            ->where('id_kontes', $kontesId)
            ->get();

        if ($data->isEmpty()) return null;

        $rataPerKriteria = $data->groupBy('id_kriteria')->map(function ($group) {
            $avg = round($group->avg('hasil_defuzzifikasi'), 2);
            $topHimpunan = $group->groupBy('hasil_himpunan')->sortByDesc(fn($g) => $g->count())->keys()->first();
            $idHimpunan = $group->firstWhere('hasil_himpunan', $topHimpunan)?->id_hasil_himpunan;

            Hasil::updateOrCreate([
                'id_kontes'   => $group->first()->id_kontes,
                'id_bonsai'   => $group->first()->id_bonsai,
                'id_kriteria' => $group->first()->id_kriteria,
            ], [
                'rata_defuzzifikasi' => $avg,
                'rata_himpunan'      => $topHimpunan,
                'id_rata_himpunan'   => $idHimpunan,
            ]);

            return $avg;
        });

        $total = round($rataPerKriteria->sum(), 2);
        $total = min($total, 360);

        $himpunan = match (true) {
            $total >= 321 => 'Baik Sekali',
            $total >= 281 => 'Baik',
            $total >= 241 => 'Cukup',
            default       => 'Kurang',
        };

        RekapNilai::updateOrCreate([
            'id_kontes' => $kontesId,
            'id_bonsai' => $bonsaiId,
        ], [
            'skor_akhir'     => $total,
            'himpunan_akhir' => $himpunan,
        ]);

        return $total;
    }
}
