<?php

namespace App\Services;

use App\Models\{Nilai, HelperDomain, Defuzzifikasi, RekapNilai};

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

        $himpunan = strtolower($d->himpunan);

        if ($himpunan === 'kurang') {
            return $this->muTrapezoid($x, $a - 10, $a, $b, $c);
        } elseif ($himpunan === 'baik sekali') {
            return $this->muTrapezoid($x, $a, $b, $c, $c + 10);
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

                // Perkiraan luas centroid dari hasil potongan
                $aCut = $a + $α * ($b - $a);
                $cCut = $c - $α * ($c - $b);
                $z = ($aCut + $b + $cCut) / 3;

                $num += $z * $α;
                $den += $α;
            }

            $zFinal = $den ? round($num / $den, 2) : 0;

            // Ambil hasil himpunan dari zFinal
            $barisOutput = $outDomains
                ->map(function ($d) use ($zFinal) {
                    $a = $d->domain_min;
                    $c = $d->domain_max;
                    $b = ($a + $c) / 2;
                    $himpunan = strtolower($d->himpunan);

                    if ($himpunan === 'kurang') {
                        $degree = $this->muTrapezoid($zFinal, $a - 10, $a, $b, $c);
                    } elseif ($himpunan === 'baik sekali') {
                        $degree = $this->muTrapezoid($zFinal, $a, $b, $c, $c + 10);
                    } else {
                        $degree = $this->muTri($zFinal, $a, $b, $c);
                    }

                    return [
                        'domain' => $d,
                        'degree' => $degree,
                    ];
                })
                ->filter(fn($item) => $item['degree'] > 0)
                ->sortByDesc('degree')
                ->first()['domain'] ?? null;

            $hasilHimpunan = $barisOutput?->himpunan;
            $idHimpunan = $barisOutput?->id;

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
        // Ambil semua defuzzifikasi untuk satu bonsai pada kontes
        $defuzz = Defuzzifikasi::where('id_bonsai', $bonsaiId)
            ->where('id_kontes', $kontesId)
            ->get();

        if ($defuzz->isEmpty()) return null; // tidak ada data sama sekali

        $rataPerKriteria = $defuzz->groupBy('id_kriteria')->map(function ($group) use ($bonsaiId, $kontesId) {
            // 1. Rata-rata hasil_defuzzifikasi dari semua juri untuk satu kriteria
            $avg = round($group->avg('hasil_defuzzifikasi'), 2);

            // 2. Tentukan himpunan mayoritas
            $himpunan = $group->groupBy('hasil_himpunan')
                ->sortByDesc(fn($g) => $g->count())
                ->keys()
                ->first();

            // 3. Ambil id_hasil_himpunan dari salah satu record
            $idHimpunan = $group->firstWhere('hasil_himpunan', $himpunan)?->id_hasil_himpunan;

            // 4. Simpan atau update ke tabel hasil
            \App\Models\Hasil::updateOrCreate([
                'id_bonsai' => $bonsaiId,
                'id_kontes' => $kontesId,
                'id_kriteria' => $group->first()->id_kriteria,
            ], [
                'hasil_defuzzifikasi' => $avg,
                'hasil_himpunan' => $himpunan,
                'id_hasil_himpunan' => $idHimpunan,
            ]);

            return $avg;
        });

        // 5. Jumlahkan semua rata-rata per kriteria
        $total = round($rataPerKriteria->sum(), 2);
        $total = max(0, min($total, 360)); // batasan 0 – 360, jika diperlukan

        // 6. Tentukan himpunan akhir berdasarkan skor total
        $himpunanAkhir = match (true) {
            $total >= 321 => 'Baik Sekali',
            $total >= 281 => 'Baik',
            $total >= 241 => 'Cukup',
            default => 'Kurang',
        };

        // 7. Simpan ke tabel rekap_nilai
        \App\Models\RekapNilai::updateOrCreate([
            'id_kontes' => $kontesId,
            'id_bonsai' => $bonsaiId
        ], [
            'skor_akhir' => $total,
            'himpunan_akhir' => $himpunanAkhir
        ]);

        return $total;
    }
}
