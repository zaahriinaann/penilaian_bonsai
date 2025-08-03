<?php

namespace App\Http\Controllers;

use App\Models\Kontes;
use App\Models\User;
use App\Models\Bonsai;
use App\Models\HelperDomain;
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

        $kontesPerTahun = [];
        $bonsaiPerTahun = [];
        $juriPerTahun   = [];

        foreach ($tahunRange as $tahun) {
            $kontesPerTahun[] = Kontes::whereYear('created_at', $tahun)->count();
            $bonsaiPerTahun[] = PendaftaranKontes::whereYear('created_at', $tahun)->count();
            $juriPerTahun[]   = User::where('role', 'juri')->whereYear('created_at', $tahun)->count();
        }

        // 3. Tren rata-rata skor per kriteria per tahun (menggunakan HelperDomain)
        $kriteriaList = HelperDomain::select('id_kriteria', 'kriteria')->distinct()->get();
        $kriteriaTren = [];

        foreach ($kriteriaList as $item) {
            $idK           = $item->id_kriteria;
            $nama          = $item->kriteria;
            $scoresPerTahun = [];

            foreach ($tahunRange as $tahun) {
                $avgScore = DB::table('hasil')
                    ->whereYear('created_at', $tahun)
                    ->where('id_kriteria', $idK)
                    ->avg('rata_defuzzifikasi');

                $scoresPerTahun[] = $avgScore ? round($avgScore, 2) : 0;
            }

            $kriteriaTren[$nama] = $scoresPerTahun;
        }

        // 4. KONTEST AKTIF & STATISTIK SLOT/BONSAI
        $kontesAktif    = Kontes::where('status', 1)->first();
        $bonsaiTotal    = $bonsaiDinilai = $bonsaiBelum = 0;
        $slotTotal      = $slotTerpakai = $slotSisa = 0;
        $pendingRanking = 0;

        if ($kontesAktif) {
            $slotTotal    = $kontesAktif->limit_peserta;
            $slotTerpakai = PendaftaranKontes::where('kontes_id', $kontesAktif->id)->count();
            $slotSisa     = $slotTotal - $slotTerpakai;
            $bonsaiTotal  = $slotTerpakai;

            $bonsaiDinilai = Nilai::where('id_kontes', $kontesAktif->id)
                ->distinct('id_bonsai')
                ->count('id_bonsai');
            $bonsaiBelum = $bonsaiTotal - $bonsaiDinilai;

            $pendingRanking = RekapNilai::where('id_kontes', $kontesAktif->id)
                ->whereNull('peringkat')
                ->count();
        }

        // 5. PREDIKSI TREN BONSAI & MEJA
        $bonsaiPrediksi = [];
        foreach ($tahunRange as $tahun) {
            $bonsaiPrediksi[$tahun] = PendaftaranKontes::whereYear('created_at', $tahun)->count();
        }

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

        $avgGrowth      = $countChanges > 0 ? $totalGrowth / $countChanges : 0;
        $lastCount      = end($bonsaiPrediksi);
        $prediksiBonsai = ceil($lastCount * (1 + ($avgGrowth / 100)));
        $prediksiMeja   = ceil($prediksiBonsai / 5);

        // 6. TOP 3 BONSAI TERBAIK
        $topBonsai = RekapNilai::with('bonsai.pendaftaranKontes.user')
            ->when($kontesAktif, fn($q) => $q->where('id_kontes', $kontesAktif->id))
            ->orderByDesc('skor_akhir')
            ->take(3)
            ->get();

        // 7. RETURN VIEW
        return view('dashboard.index', [
            'dataRender'     => $dataRender,
            'tahun'          => $tahunRange,
            'data_kontes'    => $kontesPerTahun,
            'data_bonsai'    => $bonsaiPerTahun,
            'data_juri'      => $juriPerTahun,
            'kriteriaTren'   => $kriteriaTren,
            'kontesAktif'    => $kontesAktif,
            'bonsaiTotal'    => $bonsaiTotal,
            'bonsaiDinilai'  => $bonsaiDinilai,
            'bonsaiBelum'    => $bonsaiBelum,
            'slotTotal'      => $slotTotal,
            'slotTerpakai'   => $slotTerpakai,
            'slotSisa'       => $slotSisa,
            'prediksiBonsai' => $prediksiBonsai,
            'prediksiMeja'   => $prediksiMeja,
            'rataKenaikan'   => round($avgGrowth, 2),
            'topBonsai'      => $topBonsai,
            'pendingRanking' => $pendingRanking,
        ]);
    }

    public function dashboardJuri()
    {
        $user = Auth::user();
        $juri = $user->juri; // Pastikan relasi User->juri() sudah terdefinisi

        if (!$juri) {
            abort(403, 'User ini belum terdaftar sebagai juri.');
        }

        $idJuri = $juri->id;

        // 1. Total kontes diikuti dan total bonsai dinilai
        $totalKontes   = Nilai::where('id_juri', $idJuri)
            ->distinct('id_kontes')
            ->count('id_kontes');

        $bonsaiDinilai = Nilai::where('id_juri', $idJuri)
            ->distinct('id_bonsai')
            ->count('id_bonsai');

        // 2. Kontes aktif & bonsai belum dinilai
        $kontesAktif         = Kontes::where('status', 1)->first();
        $bonsaiBelumDinilai  = 0;

        if ($kontesAktif) {
            $totalBonsai = PendaftaranKontes::where('kontes_id', $kontesAktif->id)
                ->count();

            $sudahDinilai = Nilai::where('id_kontes', $kontesAktif->id)
                ->where('id_juri', $idJuri)
                ->distinct('id_bonsai')
                ->count('id_bonsai');

            $bonsaiBelumDinilai = $totalBonsai - $sudahDinilai;
        }

        // 3. Grafik jumlah bonsai yang dinilai per tahun (5 tahun terakhir)
        $tahun          = range(now()->year - 4, now()->year);
        $dataPenilaian  = collect($tahun)->map(function ($th) use ($idJuri) {
            return Nilai::where('id_juri', $idJuri)
                ->whereYear('created_at', $th)
                ->distinct('id_bonsai')
                ->count('id_bonsai');
        });

        // 4. Tren rata-rata skor per kriteria per tahun (menggunakan helper_domain)
        $kriteriaList = HelperDomain::select('id_kriteria', 'kriteria')
            ->distinct()
            ->get();

        $kriteriaTren = [];
        foreach ($kriteriaList as $item) {
            $scores = [];
            foreach ($tahun as $th) {
                $avg = DB::table('hasil')
                    ->whereYear('created_at', $th)
                    ->where('id_kriteria', $item->id_kriteria)
                    ->avg('rata_defuzzifikasi');

                $scores[] = $avg !== null ? round($avg, 2) : 0;
            }
            $kriteriaTren[$item->kriteria] = $scores;
        }

        // 5. Daftar kontes yang pernah dinilai juri
        $kontesId = Nilai::where('id_juri', $idJuri)
            ->distinct('id_kontes')
            ->pluck('id_kontes');

        $kontesDiikuti = Kontes::whereIn('id', $kontesId)
            ->orderByDesc('tanggal_mulai_kontes')
            ->get();

        // 6. Kirim semua data ke view
        return view('dashboard.juri', [
            'totalKontes'         => $totalKontes,
            'bonsaiDinilai'       => $bonsaiDinilai,
            'bonsaiBelumDinilai'  => $bonsaiBelumDinilai,
            'kontesAktif'         => $kontesAktif,
            'tahun'               => $tahun,
            'dataPenilaian'       => $dataPenilaian,
            'kontesDiikuti'       => $kontesDiikuti,
            'kriteriaTren'        => $kriteriaTren,
        ]);
    }

    public function dashboardAnggota()
    {
        $user = Auth::user();

        // Kontes aktif saat ini
        $kontesAktif = Kontes::where('status', 1)->first();

        // 1. Statistik pendaftaran peserta
        $totalBonsai = Bonsai::whereHas(
            'pendaftaranKontes',
            fn($q) =>
            $q->where('user_id', $user->id)
        )->count();

        $totalKontes = PendaftaranKontes::where('user_id', $user->id)
            ->distinct('kontes_id')
            ->count('kontes_id');

        $bonsaiAnggota = Bonsai::with(['rekapNilai', 'pendaftaranKontes.kontes'])
            ->whereHas(
                'pendaftaranKontes',
                fn($q) =>
                $q->where('user_id', $user->id)
            )->get();

        // 2. Statistik slot kontes aktif
        if ($kontesAktif) {
            $kontesAktif->slot_total   = $kontesAktif->limit_peserta;
            $kontesAktif->slot_terisi  = PendaftaranKontes::where('kontes_id', $kontesAktif->id)->count();
        }

        // 3. Top 10 Bonsai terbaik (peringkat sudah terisi)
        $bestTen = collect();
        if ($kontesAktif) {
            $bestTen = RekapNilai::with('bonsai.pendaftaranKontes.user')
                ->where('id_kontes', $kontesAktif->id)
                ->whereNotNull('peringkat')
                ->orderBy('peringkat')
                ->take(10)
                ->get();
        }

        // 4. Grafik Bonsai Dinilai per tahun (5 tahun terakhir)
        $tahun         = range(now()->year - 4, now()->year);
        $dataPenilaian = collect($tahun)->map(
            fn($th) =>
            Nilai::whereHas(
                'bonsai.pendaftaranKontes',
                fn($q) =>
                $q->where('user_id', $user->id)
            )->whereYear('created_at', $th)
                ->distinct('id_bonsai')
                ->count('id_bonsai')
        );

        // 5. Tren rata-rata skor per kriteria per tahun
        $kriteriaList = HelperDomain::select('id_kriteria', 'kriteria')->distinct()->get();
        $kriteriaTren = [];
        foreach ($kriteriaList as $item) {
            $scores = [];
            foreach ($tahun as $th) {
                $avg = DB::table('hasil')
                    ->whereYear('created_at', $th)
                    ->where('id_kriteria', $item->id_kriteria)
                    ->avg('rata_defuzzifikasi');
                $scores[] = $avg !== null ? round($avg, 2) : 0;
            }
            $kriteriaTren[$item->kriteria] = $scores;
        }

        return view('dashboard.anggota', compact(
            'kontesAktif',
            'totalBonsai',
            'totalKontes',
            'bonsaiAnggota',
            'bestTen',
            'tahun',
            'dataPenilaian',
            'kriteriaTren'
        ));
    }
}
