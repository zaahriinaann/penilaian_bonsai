<?php

namespace App\Http\Controllers;

use App\Models\Kontes;
use App\Models\User;
use App\Models\Bonsai;
use App\Models\Nilai;
use App\Models\PendaftaranKontes;
use App\Models\Penilaian;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        /* ========= 1. DATA KARTU TOTAL ========= */
        $dataRender = [
            'Kontes'  => [Kontes::count(),               '00b894'],
            'Juri'    => [User::where('role', 'juri')->count(),     '0984e3'],
            'Peserta' => [User::where('role', 'anggota')->count(),  'fdcb6e'],
            'Bonsai'  => [Bonsai::count(),               'd63031'],
        ];

        /* ========= 2. DATA GRAFIK 5 TAHUN TERAKHIR ========= */
        $tahunSekarang = now()->year;
        $tahunRange    = range($tahunSekarang - 4, $tahunSekarang);

        $kontesPerTahun   = [];
        $pesertaPerTahun  = [];
        $bonsaiPerTahun   = [];

        foreach ($tahunRange as $tahun) {
            $kontesPerTahun[]  = Kontes::whereYear('created_at',  $tahun)->count();
            $pesertaPerTahun[] = User::where('role', 'anggota')
                ->whereYear('created_at', $tahun)->count();
            $bonsaiPerTahun[]  = Bonsai::whereYear('created_at',  $tahun)->count();
        }

        /* ========= 3. DATA KONTES AKTIF & STATISTIK PENILAIAN ========= */
        $kontesAktif = Kontes::where('status', 1)->first();

        // Nilai default jika belum ada kontes aktif
        $bonsaiTotal     = 0;
        $bonsaiDinilai   = 0;
        $bonsaiBelum     = 0;
        $statusDaftar    = 'Tidak';
        $slotTotal       = 0;
        $slotSisa        = 0;

        if ($kontesAktif) {
            $bonsaiTotal   = PendaftaranKontes::where('kontes_id', $kontesAktif->id)->count();

            $bonsaiDinilai = Nilai::where('id_kontes', $kontesAktif->id)
                ->distinct('id_bonsai')
                ->count('id_bonsai');

            $bonsaiBelum   = $bonsaiTotal - $bonsaiDinilai;

            $statusDaftar  = $kontesAktif->pendaftaran_dibuka ? 'Ya' : 'Tidak';
            $slotTotal     = $kontesAktif->slot_total;
            $slotSisa      = $slotTotal - $bonsaiTotal;
        }

        /* ========= 4. KIRIM KE VIEW ========= */
        return view('dashboard.index', [
            // kartu total
            'dataRender'     => $dataRender,

            // grafik
            'tahun'          => $tahunRange,
            'data_kontes'    => $kontesPerTahun,
            'data_peserta'   => $pesertaPerTahun,
            'data_bonsai'    => $bonsaiPerTahun,

            // kontes & penilaian
            'kontesAktif'    => $kontesAktif,
            'bonsaiTotal'    => $bonsaiTotal,
            'bonsaiDinilai'  => $bonsaiDinilai,
            'bonsaiBelum'    => $bonsaiBelum,

            // status pendaftaran
            'statusDaftar'   => $statusDaftar,
            'slotTotal'      => $slotTotal,
            'slotSisa'       => $slotSisa,
        ]);
    }
}
