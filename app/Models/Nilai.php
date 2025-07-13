<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Nilai extends Model
{
    protected $fillable = [
        'id_kontes',
        'id_pendaftaran',
        'id_peserta',
        'id_juri',
        'id_bonsai',
        'id_kriteria_penilaian',
        'nilai_awal',
        'derajat_anggota',
    ];

    // Relasi ke kontes
    public function kontes()
    {
        return $this->belongsTo(Kontes::class, 'id_kontes');
    }

    // Relasi ke PendaftaranKontes
    public function pendaftaranKontes()
    {
        return $this->belongsTo(PendaftaranKontes::class, 'id_pendaftaran');
    }

    // Relasi ke peserta (user)
    public function peserta()
    {
        return $this->belongsTo(User::class, 'id_peserta');
    }

    // Relasi ke juri
    public function juri()
    {
        return $this->belongsTo(Juri::class, 'id_juri');
    }

    // Relasi ke bonsai
    public function bonsai()
    {
        return $this->belongsTo(Bonsai::class, 'id_bonsai');
    }

    // Relasi ke kriteria dan sub-kriteria
    public function penilaian()
    {
        return $this->belongsTo(HelperDomain::class, 'id_kriteria_penilaian', 'id');
    }


    public static function sudahDinilai($bonsaiId, $juriId)
    {
        return self::where('id_bonsai', $bonsaiId)
            ->where('id_juri', $juriId)
            ->exists();
    }

    // public static function hitungFuzzy($nilai = null, $kriteria)
    // {
    //     // Definisi domain fuzzy untuk setiap himpunan
    //     $domain = [
    //         'Kurang'       => [10, 40],
    //         'Cukup'        => [30, 60],
    //         'Baik'         => [50, 80],
    //         'Baik Sekali'  => [70, 90],
    //     ];

    //     $himpunan = $kriteria->himpunan; // Nama himpunan, misal: "Baik"

    //     // Validasi jika himpunan tidak dikenali
    //     if (!isset($domain[$himpunan])) {
    //         return [null, 0]; // atau bisa lempar exception jika perlu
    //     }

    //     [$min, $max] = $domain[$himpunan];
    //     $mid = ($min + $max) / 2;

    //     // Nilai di luar domain → derajat keanggotaan = 0
    //     if ($nilai < $min || $nilai > $max) {
    //         return [$nilai, 0.0];
    //     }

    //     // Hitung derajat keanggotaan (μ)
    //     if ($nilai >= $min && $nilai <= $mid) {
    //         $mu = ($nilai - $min) / ($mid - $min); // Naik
    //     } else {
    //         $mu = ($max - $nilai) / ($max - $mid); // Turun
    //     }

    //     return [$nilai, round($mu, 2)];
    // }

    public static function hitungFuzzy($nilai, $kriteria)
    {
        $idSub = $kriteria->id_sub_kriteria ?? $kriteria->id_kriteria_penilaian;
        $himpunan = $kriteria->himpunan ?? null;

        if (!$idSub || !$himpunan) return [$nilai, 0];

        [$mu, $_] = HelperDomain::getCentroidAndMu($nilai, $idSub, $himpunan);

        return [$nilai, $mu];
    }

    public static function defuzzifikasi($bonsaiId, $juriId, $kontesId)
    {
        $data = self::where('id_bonsai', $bonsaiId)
            ->where('id_juri', $juriId)
            ->where('id_kontes', $kontesId)
            ->get();

        $totalZ = 0;
        $totalMu = 0;

        foreach ($data as $item) {
            $himpunan = $item->penilaian->himpunan ?? null;
            $idSub = $item->id_kriteria_penilaian;

            if (!$himpunan || !$idSub) continue;

            [$mu, $z] = HelperDomain::getCentroidAndMu($item->nilai_awal, $idSub, $himpunan);

            if ($mu > 0) {
                $totalZ += $mu * $z;
                $totalMu += $mu;
            }
        }

        return $totalMu > 0 ? round($totalZ / $totalMu, 2) : 0;
    }
}
