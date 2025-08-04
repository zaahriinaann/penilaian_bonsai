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

        $tahunSekarang  = now()->year;
        $tahunRange     = range($tahunSekarang - 4, $tahunSekarang);

        // 2. DATA 5 TAHUN TERAKHIR
        $kontesPerTahun = [];
        $bonsaiPerTahun = [];
        $juriPerTahun   = [];

        foreach ($tahunRange as $tahun) {
            // Kontes per tahun
            $kontesPerTahun[] = Kontes::whereYear('tanggal_mulai_kontes', $tahun)->count();

            // Bonsai per tahun → dari pendaftaran_kontes
            $bonsaiPerTahun[] = PendaftaranKontes::whereHas('kontes', function ($q) use ($tahun) {
                $q->whereYear('tanggal_mulai_kontes', $tahun);
            })->count();

            // Juri per tahun → dari tabel nilai
            $juriPerTahun[] = Nilai::join('kontes', 'nilais.id_kontes', '=', 'kontes.id')
                ->whereYear('kontes.tanggal_mulai_kontes', $tahun)
                ->distinct('nilais.id_juri')
                ->count('nilais.id_juri');
        }

        // 3. TREND KRITERIA PER TAHUN
        $kriteriaList = HelperDomain::select('id_kriteria', 'kriteria')->distinct()->get();
        $kriteriaTren = [];

        foreach ($kriteriaList as $item) {
            $scoresPerTahun = [];
            foreach ($tahunRange as $tahun) {
                $avgScore = DB::table('hasil')
                    ->join('kontes', 'hasil.id_kontes', '=', 'kontes.id')
                    ->whereBetween('kontes.tanggal_mulai_kontes', [$tahun . '-01-01', $tahun . '-12-31'])
                    ->whereBetween('kontes.tanggal_selesai_kontes', [$tahun . '-01-01', $tahun . '-12-31'])
                    ->where('id_kriteria', $item->id_kriteria)
                    ->avg('rata_defuzzifikasi');
                $scoresPerTahun[] = $avgScore ? round($avgScore, 2) : 0;
            }
            $kriteriaTren[$item->kriteria] = $scoresPerTahun;
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

        // 5. DATA PER KONTES TAHUN BERJALAN
        $kontesSetahun = Kontes::whereYear('tanggal_mulai_kontes', $tahunSekarang)
            ->orderBy('tanggal_mulai_kontes')
            ->get();

        $namaKontes      = [];
        $bonsaiPerKontes = [];
        $juriPerKontes   = [];

        foreach ($kontesSetahun as $kontes) {
            $namaKontes[] = $kontes->nama_kontes;

            // Bonsai dari pendaftaran_kontes
            $bonsaiPerKontes[] = PendaftaranKontes::where('kontes_id', $kontes->id)
                ->count();

            // Juri dari tabel nilais
            $juriPerKontes[] = Nilai::where('id_kontes', $kontes->id)
                ->distinct('id_juri')
                ->count('id_juri');
        }

        // 6. PREDIKSI BERDASARKAN KONTEST SEBELUMNYA
        $kontesTerakhir = Kontes::where('status', 0)
            ->orderByDesc('tanggal_mulai_kontes')
            ->first();

        $prediksiBonsai = 0;
        $prediksiMeja   = 0;

        if ($kontesTerakhir) {
            $prediksiBonsai = PendaftaranKontes::where('kontes_id', $kontesTerakhir->id)->count();
            $prediksiMeja   = ceil($prediksiBonsai / 5);
        }

        // 7. TOP 3 BONSAI TERBAIK
        $topBonsai = RekapNilai::with('bonsai.pendaftaranKontes.user')
            ->when($kontesAktif, fn($q) => $q->where('id_kontes', $kontesAktif->id))
            ->orderByDesc('skor_akhir')
            ->take(3)
            ->get();

        return view('dashboard.index', [
            'dataRender'     => $dataRender,
            'tahunSekarang'  => $tahunSekarang,
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
            'rataKenaikan'   => 0,
            'topBonsai'      => $topBonsai,
            'pendingRanking' => $pendingRanking,
            'namaKontes'     => $namaKontes,
            'bonsaiPerKontes' => $bonsaiPerKontes,
            'juriPerKontes'  => $juriPerKontes,
        ]);
    }

    public function dashboardJuri()
    {
        $user = Auth::user();
        $juri = $user->juri;

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
            $totalBonsai = PendaftaranKontes::where('kontes_id', $kontesAktif->id)->count();
            $sudahDinilai = Nilai::where('id_kontes', $kontesAktif->id)
                ->where('id_juri', $idJuri)
                ->distinct('id_bonsai')
                ->count('id_bonsai');

            $bonsaiBelumDinilai = $totalBonsai - $sudahDinilai;
        }

        // 3. Grafik jumlah bonsai yang dinilai per tahun
        $tahun         = range(now()->year - 4, now()->year);
        $dataPenilaian = collect($tahun)->map(function ($th) use ($idJuri) {
            return Nilai::join('kontes', 'nilais.id_kontes', '=', 'kontes.id')
                ->where('nilais.id_juri', $idJuri)
                ->whereYear('kontes.tanggal_mulai_kontes', $th)
                ->distinct('nilais.id_bonsai')
                ->count('nilais.id_bonsai');
        });

        // 4. Tren rata-rata skor per kriteria per tahun (pakai tanggal kontes)
        $kriteriaList = HelperDomain::select('id_kriteria', 'kriteria')->distinct()->get();
        $kriteriaTren = [];

        foreach ($kriteriaList as $item) {
            $scores = [];
            foreach ($tahun as $th) {
                $avg = DB::table('hasil')
                    ->join('kontes', 'hasil.id_kontes', '=', 'kontes.id')
                    ->whereBetween('kontes.tanggal_mulai_kontes', [$th . '-01-01', $th . '-12-31'])
                    ->whereBetween('kontes.tanggal_selesai_kontes', [$th . '-01-01', $th . '-12-31'])
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
            $kontesAktif->slot_total  = $kontesAktif->limit_peserta;
            $kontesAktif->slot_terisi = PendaftaranKontes::where('kontes_id', $kontesAktif->id)->count();
        }

        // 3. Top 10 Bonsai terbaik
        $bestTen = collect();
        if ($kontesAktif) {
            $bestTen = RekapNilai::with('bonsai.pendaftaranKontes.user')
                ->where('id_kontes', $kontesAktif->id)
                ->whereNotNull('peringkat')
                ->orderBy('peringkat')
                ->take(10)
                ->get();
        }

        // 4. Grafik Bonsai Dinilai per tahun
        $tahun         = range(now()->year - 4, now()->year);
        $dataPenilaian = collect($tahun)->map(
            fn($th) =>
            Nilai::join('kontes', 'nilais.id_kontes', '=', 'kontes.id')
                ->whereHas(
                    'bonsai.pendaftaranKontes',
                    fn($q) =>
                    $q->where('user_id', $user->id)
                )
                ->whereYear('kontes.tanggal_mulai_kontes', $th)
                ->distinct('nilais.id_bonsai')
                ->count('nilais.id_bonsai')
        );

        // 5. Tren rata-rata skor per kriteria per tahun (pakai tanggal kontes)
        $kriteriaList = HelperDomain::select('id_kriteria', 'kriteria')->distinct()->get();
        $kriteriaTren = [];

        foreach ($kriteriaList as $item) {
            $scores = [];
            foreach ($tahun as $th) {
                $avg = DB::table('hasil')
                    ->join('kontes', 'hasil.id_kontes', '=', 'kontes.id')
                    ->whereBetween('kontes.tanggal_mulai_kontes', [$th . '-01-01', $th . '-12-31'])
                    ->whereBetween('kontes.tanggal_selesai_kontes', [$th . '-01-01', $th . '-12-31'])
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
