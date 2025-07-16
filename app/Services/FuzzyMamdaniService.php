<?php

namespace App\Services;

use App\Models\{Nilai, HelperDomain, Defuzzifikasi, RekapNilai};

class FuzzyMamDaniService
{
    private function muTri(float $x, float $a, float $b, float $c): float
    {
        if ($x <= $a || $x >= $c) return 0;
        if ($x == $b) return 1;
        return $x < $b ? ($x - $a) / ($b - $a) : ($c - $x) / ($c - $b);
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
        return $this->muTri($x, $a, $b, $c);
    }

    public function hitungFuzzyPerJuri(int $bonsaiId, int $juriId, int $kontesId): float
    {
        $inputs = Nilai::where('id_bonsai', $bonsaiId)
            ->where('id_juri', $juriId)
            ->where('id_kontes', $kontesId)
            ->get()
            ->groupBy('id_kriteria');

        if ($inputs->isEmpty()) return 0;

        $zFinalTotal = 0;
        $kriteriaCount = 0;
        $allOutDomains = collect();

        foreach ($inputs as $idKriteria => $group) {
            $outDomains = HelperDomain::where('id_kriteria', $idKriteria)
                ->whereNull('id_sub_kriteria')
                ->get();

            if ($outDomains->isEmpty()) continue;
            $allOutDomains = $allOutDomains->merge($outDomains);

            $alpha = [];
            foreach ($outDomains as $out) {
                $µlist = $group->filter(
                    fn($n) =>
                    strtolower($n->himpunan) === strtolower($out->himpunan)
                        && $n->derajat_anggota > 0
                )->pluck('derajat_anggota');

                if ($µlist->isNotEmpty()) {
                    $alpha[$out->himpunan] = $µlist->min();
                }
            }

            if (empty($alpha)) continue;

            $num = $den = 0;
            foreach ($alpha as $himp => $α) {
                $d = $outDomains->firstWhere('himpunan', $himp);
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

            $zKriteria = $den ? round($num / $den, 2) : 0;
            $zFinalTotal += $zKriteria;
            $kriteriaCount++;
        }

        $zFinal = $kriteriaCount ? round($zFinalTotal / $kriteriaCount, 2) : 0;

        // 🟢 Ambil baris OUTPUT (himpunan) tempat zFinal berada
        $barisOutput = $allOutDomains
            ->filter(fn($d) => is_null($d->id_sub_kriteria))
            ->first(fn($d) => $zFinal >= $d->domain_min && $zFinal <= $d->domain_max);

        $hasilHimpunan = $barisOutput?->himpunan;
        $idHimpunan    = $barisOutput?->id;

        // ✅ Simpan defuzzifikasi + hasil_himpunan (ID)
        Defuzzifikasi::updateOrCreate([
            'id_kontes' => $kontesId,
            'id_bonsai' => $bonsaiId,
            'id_juri' => $juriId
        ], [
            'hasil_defuzzifikasi' => $zFinal,
            'hasil_himpunan' => $hasilHimpunan,
            'id_hasil_himpunan' => $idHimpunan
        ]);

        return $zFinal;
    }

    public function hitungRekapAkhir(int $bonsaiId, int $kontesId): ?float
    {
        $values = Defuzzifikasi::where('id_bonsai', $bonsaiId)
            ->where('id_kontes', $kontesId)
            ->pluck('hasil_defuzzifikasi');

        if ($values->isEmpty()) return null;

        $avg = round($values->avg(), 2);

        RekapNilai::updateOrCreate([
            'id_kontes' => $kontesId,
            'id_bonsai' => $bonsaiId
        ], ['skor_akhir' => $avg]);

        return $avg;
    }
}
