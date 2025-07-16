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

    // ✅ 1. Hitung derajat keanggotaan per domain
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

    // ✅ 2-3. Hitung defuzzifikasi per kriteria untuk juri dan simpan ke tabel
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
            $outDomains = HelperDomain::where('id_kriteria', $idKriteria)
                ->whereNull('id_sub_kriteria')
                ->get();

            if ($outDomains->isEmpty()) continue;

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

            $zFinal = $den ? round($num / $den, 2) : 0;

            // ✅ Ambil label hasil_himpunan berdasarkan nilai z
            $barisOutput = $outDomains
                ->map(function ($d) use ($zFinal) {
                    $a = $d->domain_min;
                    $c = $d->domain_max;
                    $b = ($a + $c) / 2;
                    return [
                        'domain' => $d,
                        'degree' => $this->muTri($zFinal, $a, $b, $c),
                    ];
                })
                ->filter(fn($item) => $item['degree'] > 0)
                ->sortByDesc('degree')
                ->first()['domain'] ?? null;

            $hasilHimpunan = $barisOutput?->himpunan;
            $idHimpunan = $barisOutput?->id;

            // ✅ Simpan ke tabel defuzzifikasi
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

        // ✅ Kembalikan skor akhir dari juri ini
        return $jumlahKriteria > 0 ? round($totalZ / $jumlahKriteria, 2) : 0;
    }

    // ✅ 4-5. Hitung rata-rata semua juri & simpan ke rekap_nilai
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
        ], [
            'skor_akhir' => $avg
        ]);

        return $avg;
    }
}
