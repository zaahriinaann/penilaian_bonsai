<?php

namespace App\Services;

use App\Models\{Nilai, HelperDomain, Defuzzifikasi, RekapNilai};

/**
 *  Fuzzy Mamdani engine – versi dinamis.
 *  ──────────────────────────────────────
 *  • id_juri  : memakai id dari tabel **juri** (bukan users.id)
 *  • Semua domain & himpunan dibaca dari tabel helper_domain
 *    –  row INPUT  : id_sub_kriteria ≠ NULL
 *    –  row OUTPUT : id_sub_kriteria  = NULL  (satu baris per himpunan per kriteria)
 */
class FuzzyMamDaniService
{
    /*--------------------------------------------------------------
     |  Triangular membership (µ)
     |--------------------------------------------------------------*/
    private function muTri(float $x, float $a, float $b, float $c): float
    {
        if ($x <= $a || $x >= $c)   return 0;
        if ($x == $b)           return 1;
        return $x < $b ? ($x - $a) / ($b - $a) : ($c - $x) / ($c - $b);
    }

    /* mid‑point default untuk INPUT */
    private const MID = [
        'kurang' => 25,
        'cukup' => 45,
        'baik' => 65,
        'baik sekali' => 85,
    ];

    /*--------------------------------------------------------------
     |  Hitung µ keanggotaan – fleksibel
     |--------------------------------------------------------------*/
    public function hitungDerajatKeanggotaan(float $x, HelperDomain $d): float
    {
        $a = $d->domain_min;
        $c = $d->domain_max;
        // titik puncak b:  • output → tengah domain
        //                 • input  → bawaan MID tabel di atas
        $b = ($a + $c) / 2;
        if (!is_null($d->id_sub_kriteria)) {
            $b = self::MID[strtolower($d->himpunan)] ?? $b;
        }
        return $this->muTri($x, $a, $b, $c);
    }

    /*--------------------------------------------------------------
     |  Defuzzifikasi per‑juri, per‑bonsai, per‑kontes
     |--------------------------------------------------------------*/
    public function hitungFuzzyPerJuri(int $bonsaiId, int $juriId, int $kontesId): float
    {
        // Kelompokkan nilai berdasarkan kriteria
        $inputs = Nilai::where('id_bonsai', $bonsaiId)
            ->where('id_juri', $juriId)
            ->where('id_kontes', $kontesId)
            ->get()
            ->groupBy('id_kriteria');

        if ($inputs->isEmpty()) return 0;

        $zFinalTotal = 0;
        $kriteriaCount = 0;

        foreach ($inputs as $idKriteria => $group) {
            /*── 1. Ambil domain OUTPUT untuk kriteria ini ──*/
            $outDomains = HelperDomain::where('id_kriteria', $idKriteria)
                ->whereNull('id_sub_kriteria')
                ->get();
            if ($outDomains->isEmpty()) continue; // tak ada definisi output

            /*── 2. Hitung α‑cut masing2 himpunan output ──*/
            $alpha = []; // [himpunan=>α]
            foreach ($outDomains as $out) {
                // cari semua µ dari INPUT yg himpunannya sama dengan $out
                $µlist = $group->filter(
                    fn($n) =>
                    strtolower($n->himpunan) == strtolower($out->himpunan) && $n->derajat_anggota > 0
                )->pluck('derajat_anggota');
                if ($µlist->isNotEmpty())
                    $alpha[$out->himpunan] = $µlist->min(); // min di sub‑kriteria
            }
            if (empty($alpha)) continue; // tak ada firing rule

            /*── 3. Defuzzifikasi (α‑cut centroid) ──*/
            $num = $den = 0;
            foreach ($alpha as $himp => $α) {
                $d = $outDomains->firstWhere('himpunan', $himp);
                if (!$d || $α == 0) continue;
                $a = $d->domain_min;
                $c = $d->domain_max;
                $b = ($a + $c) / 2;
                $aCut = $a + $α * ($b - $a);
                $cCut = $c - $α * ($c - $b);
                $z = ($aCut + $b + $cCut) / 3; // centroid segitiga ter‑cut
                $num += $z * $α;
                $den += $α;
            }
            $zKriteria = $den ? round($num / $den, 2) : 0;
            $zFinalTotal += $zKriteria;
            $kriteriaCount++;
        }

        // rata2 antar kriteria (jika lebih dari 1 kriteria diinput)
        $zFinal = $kriteriaCount ? round($zFinalTotal / $kriteriaCount, 2) : 0;

        /*── 4. Simpan ke tabel defuzzifikasi ──*/
        Defuzzifikasi::updateOrCreate([
            'id_kontes' => $kontesId,
            'id_bonsai' => $bonsaiId,
            'id_juri'  => $juriId
        ], ['hasil_defuzzifikasi' => $zFinal]);

        return $zFinal;
    }

    /*--------------------------------------------------------------
     |  Rekap rata‑rata antar‑juri
     |--------------------------------------------------------------*/
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
