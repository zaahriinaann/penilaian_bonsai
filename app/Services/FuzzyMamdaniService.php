<?php

namespace App\Services;

use App\Models\Nilai;
use App\Models\HelperDomain;
use App\Models\Defuzzifikasi;
use App\Models\Hasil;
use App\Models\RekapNilai;

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

    public function hitungFuzzyPerJuri(int $bonsaiId, int $juriId, int $kontesId): float
    {
        $groups = Nilai::where('id_bonsai', $bonsaiId)
            ->where('id_juri',   $juriId)
            ->where('id_kontes', $kontesId)
            ->get()
            ->groupBy('id_kriteria');

        if ($groups->isEmpty()) return 0;

        $sumZ = 0;
        $countK = 0;

        foreach ($groups as $idKriteria => $vals) {
            $domains = HelperDomain::where('id_kriteria', $idKriteria)
                ->whereNull('id_sub_kriteria')
                ->get();
            if ($domains->isEmpty()) continue;

            $alphaList = [];
            foreach ($domains as $d) {
                $muVals = $vals
                    ->filter(fn($n) => strtolower($n->himpunan) === strtolower($d->himpunan) && $n->derajat_anggota > 0)
                    ->pluck('derajat_anggota');
                if ($muVals->isNotEmpty()) {
                    $alphaList[$d->himpunan] = $muVals->min();
                }
            }
            if (empty($alphaList)) continue;

            $num = $den = 0;
            foreach ($alphaList as $h => $α) {
                $d = $domains->firstWhere('himpunan', $h);
                if (!$d || $α == 0) continue;

                $a = $d->domain_min;
                $c = $d->domain_max;
                $b = ($a + $c) / 2;
                $aCut = $a + $α * ($b - $a);
                $cCut = $c - $α * ($c - $b);
                $z = ($aCut + $b + $cCut) / 3;

                $num += $z * $α;
                $den += $α;
            }
            $zFinal = $den ? round($num / $den, 2) : 0;

            $out = $domains
                ->map(fn($d) => [
                    'domain' => $d,
                    'degree' => match (strtolower($d->himpunan)) {
                        'kurang'      => $this->muTrapezoid($zFinal, $d->domain_min - 10, $d->domain_min, ($d->domain_min + $d->domain_max) / 2, $d->domain_max),
                        'baik sekali' => $this->muTrapezoid($zFinal, $d->domain_min, ($d->domain_min + $d->domain_max) / 2, $d->domain_max, $d->domain_max + 10),
                        default       => $this->muTri($zFinal, $d->domain_min, ($d->domain_min + $d->domain_max) / 2, $d->domain_max),
                    }
                ])
                ->filter(fn($i) => $i['degree'] > 0)
                ->sortByDesc('degree')
                ->first()['domain'] ?? null;

            $himpunan = $out?->himpunan;
            $idHimpunan = $out?->id;

            Defuzzifikasi::updateOrCreate([
                'id_kontes'   => $kontesId,
                'id_bonsai'   => $bonsaiId,
                'id_juri'     => $juriId,
                'id_kriteria' => $idKriteria,
            ], [
                'hasil_defuzzifikasi' => $zFinal,
                'hasil_himpunan'       => $himpunan,
                'id_hasil_himpunan'    => $idHimpunan,
            ]);

            $sumZ += $zFinal;
            $countK++;
        }

        return $countK > 0 ? round($sumZ / $countK, 2) : 0;
    }

    public function hitungRekapAkhir(int $bonsaiId, int $kontesId): ?float
    {
        $data = Defuzzifikasi::where('id_bonsai', $bonsaiId)
            ->where('id_kontes', $kontesId)
            ->get();
        if ($data->isEmpty()) return null;

        $rata = $data
            ->groupBy('id_kriteria')
            ->map(function ($g) use ($bonsaiId, $kontesId) {
                $avg = round($g->avg('hasil_defuzzifikasi'), 2);
                $himp = $g->groupBy('hasil_himpunan')->sortByDesc(fn($c) => $c->count())->keys()->first();
                $idHimp = $g->firstWhere('hasil_himpunan', $himp)?->id_hasil_himpunan;

                Hasil::updateOrCreate([
                    'id_bonsai'   => $bonsaiId,
                    'id_kontes'   => $kontesId,
                    'id_kriteria' => $g->first()->id_kriteria,
                ], [
                    'hasil_defuzzifikasi' => $avg,
                    'hasil_himpunan'       => $himp,
                    'id_hasil_himpunan'    => $idHimp,
                ]);

                return $avg;
            });

        $total = round($rata->sum(), 2);

        $avgskor = round($rata->avg(), 2);
        $himpAkhir = match (true) {
            $avgskor >= 85 => 'Baik Sekali',
            $avgskor >= 65 => 'Baik',
            $avgskor >= 45 => 'Cukup',
            default        => 'Kurang',
        };

        RekapNilai::updateOrCreate([
            'id_kontes' => $kontesId,
            'id_bonsai' => $bonsaiId,
        ], [
            'skor_akhir'     => $total,
            'himpunan_akhir' => $himpAkhir,
        ]);

        return $total;
    }
}
