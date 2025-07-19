<?php

namespace App\Http\Controllers;

use App\Models\Kontes;
use App\Models\User;
use App\Models\Bonsai;
use App\Models\Nilai;
use App\Models\PendaftaranKontes;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // 1. KARTU TOTAL
        $dataRender = [
            'Kontes'  => [Kontes::count(), '00b894'],
            'Juri'    => [User::where('role', 'juri')->count(), '0984e3'],
            'Peserta' => [User::where('role', 'anggota')->count(), 'fdcb6e'],
            'Bonsai'  => [Bonsai::count(), 'd63031'],
        ];

        // 2. GRAFIK PER TAHUN
        $tahunSekarang = now()->year;
        $tahunRange = range($tahunSekarang - 4, $tahunSekarang);

        $kontesPerTahun  = [];
        $pesertaPerTahun = [];
        $bonsaiPerTahun  = [];
        $juriPerTahun    = [];
        $bonsaiDataPrediksi = [];

        foreach ($tahunRange as $tahun) {
            $kontesPerTahun[]  = Kontes::whereYear('created_at', $tahun)->count();
            $pesertaPerTahun[] = User::where('role', 'anggota')->whereYear('created_at', $tahun)->count();
            $bonsaiTahun = PendaftaranKontes::whereYear('created_at', $tahun)->count();
            $bonsaiPerTahun[]  = $bonsaiTahun;
            $bonsaiDataPrediksi[$tahun] = $bonsaiTahun;
            $juriPerTahun[]    = User::where('role', 'juri')->whereYear('created_at', $tahun)->count();
        }

        // 3. KONTES AKTIF
        $kontesAktif     = Kontes::where('status', 1)->first();
        $bonsaiTotal     = 0;
        $bonsaiDinilai   = 0;
        $bonsaiBelum     = 0;
        $slotTotal       = 0;
        $slotTerpakai    = 0;
        $slotSisa        = 0;

        if ($kontesAktif) {
            $slotTotal     = $kontesAktif->limit_peserta;
            $slotTerpakai  = PendaftaranKontes::where('kontes_id', $kontesAktif->id)->count();
            $slotSisa      = $slotTotal - $slotTerpakai;
            $bonsaiTotal   = $slotTerpakai;
            $bonsaiDinilai = Nilai::where('id_kontes', $kontesAktif->id)->distinct('id_bonsai')->count('id_bonsai');
            $bonsaiBelum   = $bonsaiTotal - $bonsaiDinilai;
        }

        // 4. PREDIKSI TREN DINAMIS BONSAI & MEJA
        $tahunKeys = array_keys($bonsaiDataPrediksi);
        $jumlahKenaikan = 0;
        $jumlahTahun = 0;

        for ($i = 1; $i < count($tahunKeys); $i++) {
            $prev = $bonsaiDataPrediksi[$tahunKeys[$i - 1]];
            $curr = $bonsaiDataPrediksi[$tahunKeys[$i]];

            if ($prev > 0) {
                $kenaikan = (($curr - $prev) / $prev) * 100;
                $jumlahKenaikan += $kenaikan;
                $jumlahTahun++;
            }
        }

        $rataKenaikan = $jumlahTahun > 0 ? $jumlahKenaikan / $jumlahTahun : 0;
        $bonsaiTerakhir = end($bonsaiDataPrediksi);
        $prediksiBonsai = ceil($bonsaiTerakhir * (1 + ($rataKenaikan / 100)));
        $prediksiMeja   = ceil($prediksiBonsai / 5);

        // 5. KIRIM KE VIEW
        return view('dashboard.index', [
            'dataRender'     => $dataRender,

            // grafik
            'tahun'          => $tahunRange,
            'data_kontes'    => $kontesPerTahun,
            'data_peserta'   => $pesertaPerTahun,
            'data_bonsai'    => $bonsaiPerTahun,
            'data_juri'      => $juriPerTahun,

            // kontes & penilaian
            'kontesAktif'    => $kontesAktif,
            'bonsaiTotal'    => $bonsaiTotal,
            'bonsaiDinilai'  => $bonsaiDinilai,
            'bonsaiBelum'    => $bonsaiBelum,

            // slot
            'slotTotal'      => $slotTotal,
            'slotTerpakai'   => $slotTerpakai,
            'slotSisa'       => $slotSisa,

            // prediksi
            'prediksiBonsai' => $prediksiBonsai,
            'prediksiMeja'   => $prediksiMeja,
            'rataKenaikan'   => round($rataKenaikan, 2),
        ]);
    }
}
