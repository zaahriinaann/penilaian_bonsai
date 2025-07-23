<?php

namespace App\Http\Controllers;

use App\Models\Kontes;
use App\Models\User;
use App\Models\Bonsai;
use App\Models\Nilai;
use App\Models\PendaftaranKontes;
use App\Models\RekapNilai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $role = Auth::user()->role;

        if ($role == 'admin') {
            return $this->dashboardAdmin();
        }

        if ($role == 'juri') {
            return $this->dashboardJuri();
        }

        if ($role == 'anggota') {
            return $this->dashboardAnggota();
        }

        abort(403, 'Role tidak dikenali');
    }

    public function dashboardAdmin()
    {
        // 1. KARTU TOTAL
        $dataRender = [
            'Kontes'  => [Kontes::count(), '00b894'],
            'Juri'    => [User::where('role', 'juri')->count(), '0984e3'],
            'Peserta' => [User::where('role', 'anggota')->count(), 'fdcb6e'],
            'Bonsai'  => [Bonsai::count(), 'd63031'],
        ];

        // 2. GRAFIK PER TAHUN (5 tahun terakhir)
        $tahunSekarang  = now()->year;
        $tahunRange     = range($tahunSekarang - 4, $tahunSekarang);
        $kontesPerTahun  = [];
        $pesertaPerTahun = [];
        $bonsaiPerTahun  = [];
        $juriPerTahun    = [];
        $bonsaiPrediksi  = [];

        foreach ($tahunRange as $tahun) {
            $kontesPerTahun[]  = Kontes::whereYear('created_at', $tahun)->count();
            $pesertaPerTahun[] = User::where('role', 'anggota')->whereYear('created_at', $tahun)->count();
            $bonsaiCount       = PendaftaranKontes::whereYear('created_at', $tahun)->count();
            $bonsaiPerTahun[]  = $bonsaiCount;
            $bonsaiPrediksi[$tahun] = $bonsaiCount;
            $juriPerTahun[]    = User::where('role', 'juri')->whereYear('created_at', $tahun)->count();
        }

        // 3. KONTEST AKTIF & STATISTIK SLOT/BONSAI
        $kontesAktif    = Kontes::where('status', 1)->first();
        $bonsaiTotal    = $bonsaiDinilai = $bonsaiBelum = 0;
        $slotTotal      = $slotTerpakai = $slotSisa = 0;
        $pendingRanking = 0;

        if ($kontesAktif) {
            // slot
            $slotTotal    = $kontesAktif->limit_peserta;
            $slotTerpakai = PendaftaranKontes::where('kontes_id', $kontesAktif->id)->count();
            $slotSisa     = $slotTotal - $slotTerpakai;
            $bonsaiTotal  = $slotTerpakai;

            // bonsai yang sudah dinilai
            $bonsaiDinilai = Nilai::where('id_kontes', $kontesAktif->id)
                ->distinct('id_bonsai')
                ->count('id_bonsai');
            $bonsaiBelum = $bonsaiTotal - $bonsaiDinilai;

            // pending ranking: rekap_nilai tanpa peringkat
            $pendingRanking = RekapNilai::where('id_kontes', $kontesAktif->id)
                ->whereNull('peringkat')
                ->count();
        }

        // 4. PREDIKSI TREN BONSai & MEJA
        $years        = array_keys($bonsaiPrediksi);
        $totalGrowth  = 0;
        $countChanges = 0;
        for ($i = 1; $i < count($years); $i++) {
            $prev = $bonsaiPrediksi[$years[$i - 1]];
            $curr = $bonsaiPrediksi[$years[$i]];
            if ($prev > 0) {
                $growth = (($curr - $prev) / $prev) * 100;
                $totalGrowth += $growth;
                $countChanges++;
            }
        }
        $avgGrowth     = $countChanges > 0 ? $totalGrowth / $countChanges : 0;
        $lastCount     = end($bonsaiPrediksi);
        $prediksiBonsai = ceil($lastCount * (1 + ($avgGrowth / 100)));
        $prediksiMeja   = ceil($prediksiBonsai / 5);

        // 5. TOP 3 BONSAI TERBAIK
        $topBonsai = RekapNilai::with('bonsai.pendaftaranKontes.user')
            ->when($kontesAktif, fn($q) => $q->where('id_kontes', $kontesAktif->id))
            ->orderByDesc('skor_akhir')
            ->take(3)
            ->get();

        // 6. RETURN VIEW
        return view('dashboard.index', [
            'dataRender'      => $dataRender,
            'tahun'           => $tahunRange,
            'data_kontes'     => $kontesPerTahun,
            'data_peserta'    => $pesertaPerTahun,
            'data_bonsai'     => $bonsaiPerTahun,
            'data_juri'       => $juriPerTahun,
            'kontesAktif'     => $kontesAktif,
            'bonsaiTotal'     => $bonsaiTotal,
            'bonsaiDinilai'   => $bonsaiDinilai,
            'bonsaiBelum'     => $bonsaiBelum,
            'slotTotal'       => $slotTotal,
            'slotTerpakai'    => $slotTerpakai,
            'slotSisa'        => $slotSisa,
            'prediksiBonsai'  => $prediksiBonsai,
            'prediksiMeja'    => $prediksiMeja,
            'rataKenaikan'    => round($avgGrowth, 2),
            'topBonsai'       => $topBonsai,
            'pendingRanking'  => $pendingRanking,
        ]);
    }

    public function dashboardJuri()
    {
        $user = Auth::user();
        $juri = $user->juri; // relasi ke model Juri

        if (!$juri) {
            abort(403, 'User ini belum terdaftar sebagai juri.');
        }

        $idJuri = $juri->id;

        // Total kontes diikuti
        $totalKontes = Nilai::where('id_juri', $idJuri)
            ->distinct('id_kontes')
            ->count('id_kontes');

        // Total bonsai dinilai
        $bonsaiDinilai = Nilai::where('id_juri', $idJuri)
            ->distinct('id_bonsai')
            ->count('id_bonsai');

        // Kontes aktif & bonsai belum dinilai
        $kontesAktif = Kontes::where('status', 1)->first();
        $bonsaiBelumDinilai = 0;

        if ($kontesAktif) {
            $totalBonsai = PendaftaranKontes::where('kontes_id', $kontesAktif->id)->count();

            $sudahDinilai = Nilai::where('id_kontes', $kontesAktif->id)
                ->where('id_juri', $idJuri)
                ->distinct('id_bonsai')
                ->count('id_bonsai');

            $bonsaiBelumDinilai = $totalBonsai - $sudahDinilai;
        }

        // Grafik per tahun (5 tahun terakhir)
        $tahun = range(now()->year - 4, now()->year);
        $dataPenilaian = collect($tahun)->map(function ($th) use ($idJuri) {
            return Nilai::where('id_juri', $idJuri)
                ->whereYear('created_at', $th)
                ->distinct('id_bonsai')
                ->count('id_bonsai');
        });

        // Kontes yang pernah dinilai
        $kontesIdPernahDinilai = Nilai::where('id_juri', $idJuri)
            ->distinct('id_kontes')
            ->pluck('id_kontes');

        $kontesDiikuti = Kontes::whereIn('id', $kontesIdPernahDinilai)
            ->orderByDesc('tanggal_mulai_kontes')
            ->get();

        return view('dashboard.juri', compact(
            'totalKontes',
            'bonsaiDinilai',
            'bonsaiBelumDinilai',
            'kontesAktif',
            'tahun',
            'dataPenilaian',
            'kontesDiikuti'
        ));
    }

    public function dashboardAnggota()
    {
        $user = Auth::user();

        // Kontes aktif saat ini
        $kontesAktif = Kontes::where('status', 1)->first();

        // Statistik pendaftaran peserta
        $totalBonsai = Bonsai::whereHas('pendaftaranKontes', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->count();

        $totalKontes = PendaftaranKontes::where('user_id', $user->id)
            ->distinct('kontes_id')
            ->count('kontes_id');

        $bonsaiAnggota = Bonsai::with(['rekapNilai', 'pendaftaranKontes.kontes'])
            ->whereHas('pendaftaranKontes', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->get();


        // Statistik slot kontes aktif
        if ($kontesAktif) {
            $kontesAktif->slot_total = $kontesAktif->limit_peserta;
            $kontesAktif->slot_terisi = PendaftaranKontes::where('kontes_id', $kontesAktif->id)->count();
        }

        // Top 10 Bonsai terbaik untuk kontes aktif saja, sesuai peringkat admin
        $bestTen = collect();
        if ($kontesAktif) {
            $bestTen = RekapNilai::with(['bonsai.pendaftaranKontes.user'])
                ->where('id_kontes', $kontesAktif->id)
                ->whereNotNull('peringkat')
                ->orderBy('peringkat')
                ->take(10)
                ->get();
        }

        return view('dashboard.anggota', compact(
            'kontesAktif',
            'totalBonsai',
            'totalKontes',
            'bonsaiAnggota',
            'bestTen'
        ));
    }
}
