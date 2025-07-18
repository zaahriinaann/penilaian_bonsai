<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Nilai extends Model
{
    use HasFactory;
    protected $table = 'nilais';

    protected $fillable = [
        'id_kontes',
        'id_peserta',
        'id_juri',
        'id_bonsai',
        'id_pendaftaran',
        'id_kriteria',
        'kriteria',
        'id_sub_kriteria',
        'sub_kriteria',
        'himpunan',
        'nilai_awal',
        'derajat_anggota',
    ];

    // Relasi ke kontes
    public function kontes()
    {
        return $this->belongsTo(Kontes::class, 'id_kontes');
    }

    public function subKriteria()
    {
        return $this->belongsTo(HelperSubKriteria::class, 'id_sub_kriteria');
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

    public static function hitungFuzzy($nilai, $idSubKriteria)
    {
        $domainList = HelperDomain::where('id_sub_kriteria', $idSubKriteria)->get();
        $result = [];

        foreach ($domainList as $domain) {
            $min = $domain->domain_min;
            $max = $domain->domain_max;
            $mid = ($min + $max) / 2;

            // Skip jika nilai di luar domain
            if ($nilai < $min || $nilai > $max) continue;

            $mu = $nilai <= $mid
                ? ($nilai - $min) / ($mid - $min)
                : ($max - $nilai) / ($max - $mid);

            // Tambahkan hanya jika mu valid
            if ($mu > 0) {
                $result[] = [
                    'id_kriteria' => $domain->id_kriteria,
                    'id_sub_kriteria' => $idSubKriteria,
                    'himpunan' => $domain->himpunan,
                    'mu' => round($mu, 4),
                    'z' => round($mid, 2),
                    'nilai_awal' => $nilai,
                    'kriteria' => $domain->kriteria,
                    'sub_kriteria' => $domain->sub_kriteria
                ];
            }
        }

        // dd($result);
        return $result; // bisa lebih dari 1
    }

    public static function defuzzifikasi($bonsaiId, $juriId, $kontesId)
    {
        $nilaiList = self::where('id_bonsai', $bonsaiId)
            ->where('id_juri', $juriId)
            ->where('id_kontes', $kontesId)
            ->get();

        $totalZ = 0;
        $totalMu = 0;

        foreach ($nilaiList as $item) {
            // Ambil domain berdasarkan sub_kriteria dan himpunan
            $domain = HelperDomain::where('id_sub_kriteria', $item->id_kriteria_penilaian ?? $item->id_sub_kriteria)
                ->where('himpunan', $item->himpunan)
                ->first();

            if (!$domain || $item->derajat_anggota <= 0) continue;

            // Hitung centroid (z) sebagai nilai tengah dari domain
            $centroid = ($domain->domain_min + $domain->domain_max) / 2;

            // Kalikan μ dengan z lalu tambahkan ke total
            $totalZ += $item->derajat_anggota * $centroid;
            $totalMu += $item->derajat_anggota;
        }

        return $totalMu > 0 ? round($totalZ / $totalMu, 2) : 0;
    }

    public function kriteria()
    {
        return $this->belongsTo(HelperKriteria::class, 'id_kriteria');
    }
}
