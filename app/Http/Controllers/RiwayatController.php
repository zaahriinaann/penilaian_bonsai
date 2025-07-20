<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kontes;
use App\Models\Bonsai;
use App\Models\Defuzzifikasi;
use App\Models\Hasil;
use App\Models\HasilFuzzyRule;
use App\Models\HelperDomain;
use App\Models\Juri;
use App\Models\Nilai;
use App\Models\Kriteria;
use App\Models\PendaftaranKontes;
use App\Models\RekapNilai;
use App\Models\SubKriteria;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class RiwayatController extends Controller
{
    // public function index()
    // {
    //     $kontes = Kontes::all();
    //     return view('juri.riwayat.index', compact('kontes'));
    // }

    // public function show($id)
    // {
    //     $kontes = Kontes::findOrFail($id);
    //     $bonsai = $kontes->bonsai()->with('pemilik')->get(); // relasi many-to-many via pendaftaran_kontes

    //     return view('juri.riwayat.show', compact('kontes', 'bonsai'));
    // }

    public function detail($kontesId, $bonsaiId)
    {
        $kontes = Kontes::findOrFail($kontesId);
        $bonsai = Bonsai::with('user')->findOrFail($bonsaiId);

        $pendaftaran = PendaftaranKontes::where('kontes_id', $kontesId)
            ->where('bonsai_id', $bonsaiId)
            ->first();

        // Ambil semua juri aktif dari tabel juri
        $juriList = Juri::where('status', 1)->get();

        $penilaian = Nilai::with(['juri', 'kriteria', 'subKriteria.himpunan'])
            ->where('id_kontes', $kontesId)
            ->where('id_bonsai', $bonsaiId)
            ->get()
            ->groupBy(fn($item) => $item->juri->nama ?? 'Tanpa Nama')
            ->map(fn($group) => $group->groupBy(fn($n) => $n->kriteria->kriteria ?? 'Tanpa Kriteria'));

        $defuzz = Defuzzifikasi::with(['juri', 'kriteria', 'himpunan'])
            ->where('id_kontes', $kontesId)
            ->where('id_bonsai', $bonsaiId)
            ->get()
            ->groupBy(fn($d) => $d->juri->nama ?? 'Tanpa Nama');

        $hasilRata = Hasil::with(['kriteria', 'himpunan'])
            ->where('id_kontes', $kontesId)
            ->where('id_bonsai', $bonsaiId)
            ->get();

        $rekap = RekapNilai::with('himpunan')
            ->where('id_kontes', $kontesId)
            ->where('id_bonsai', $bonsaiId)
            ->first();

        return view('riwayat.detail', compact(
            'kontes',
            'bonsai',
            'pendaftaran',
            'juriList',
            'penilaian',
            'defuzz',
            'hasilRata',
            'rekap'
        ));
    }

    public function rekap($id_bonsai)
    {

        dd($id_bonsai);
        $rekapData = RekapNilai::with(['bonsai.user', 'hasil.kriteria'])->get();

        $rekapSorted = $rekapData->map(function ($item) {
            return [
                'id_bonsai' => $item->id_bonsai,
                'nama_pohon' => $item->bonsai->nama_pohon,
                'nomor_pendaftaran' => $item->bonsai->nomor_pendaftaran,
                'pemilik' => $item->bonsai->user->name ?? '-',
                'kelas' => $item->bonsai->kelas,
                'ukuran_2' => $item->bonsai->ukuran_2,
                'skor_akhir' => $item->nilai_akhir,
                'himpunan_akhir' => $item->himpunan_akhir,
                'kategori' => $item->hasil->groupBy('kriteria.nama')->map(function ($sub) {
                    return $sub->map(function ($h) {
                        return [
                            'hasil' => $h->nilai,
                            'himpunan' => $h->himpunan,
                        ];
                    });
                }),
            ];
        });

        $detail = $rekapSorted->firstWhere('id_bonsai', $id_bonsai);

        if (!$detail) {
            abort(404, 'Data tidak ditemukan');
        }

        return view('juri.riwayat.rekap', compact('detail'));
    }


    public function riwayatAdminIndex(Request $request)
    {
        $query = Kontes::query();

        // Filter berdasarkan input search (nama kontes atau tahun dari teks bebas)
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_kontes', 'like', '%' . $search . '%')
                    ->orWhereYear('tanggal_mulai_kontes', 'like', "%$search%")
                    ->orWhereYear('tanggal_selesai_kontes', 'like', "%$search%");
            });
        }

        // Filter tahun spesifik dari dropdown
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_mulai_kontes', $request->tahun);
        }

        // Gunakan paginate alih-alih get()
        $kontesList = $query->orderByDesc('tanggal_mulai_kontes')
            ->paginate(10) // Bisa diubah jumlah per halaman
            ->withQueryString(); // Supaya filter & pencarian tetap saat pindah halaman

        return view('admin.riwayat.index', compact('kontesList'));
    }

    public function riwayatAdminJuri(Request $request, $kontesId)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $kontes = Kontes::findOrFail($kontesId);

        // Ambil ID semua juri yang pernah menilai di kontes ini
        $juriIds = Nilai::where('id_kontes', $kontesId)
            ->pluck('id_juri')
            ->unique();

        $juriQuery = Juri::with('user')->whereIn('id', $juriIds);

        // Filter berdasarkan pencarian
        if ($search = $request->input('search')) {
            $juriQuery->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // PAGINATION
        $juriList = $juriQuery->orderBy('id', 'asc') // optional
            ->paginate(10)
            ->withQueryString();

        return view('admin.riwayat.juri', compact('kontes', 'juriList'));
    }

    public function riwayatAdminPeserta(Request $request, $kontesId, $juriId)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $kontes = Kontes::findOrFail($kontesId);
        $juri = Juri::with('user')->findOrFail($juriId);

        $bonsaiIds = Nilai::where('id_kontes', $kontesId)
            ->where('id_juri', $juriId)
            ->pluck('id_bonsai')
            ->unique();

        $query = PendaftaranKontes::with(['user', 'bonsai'])
            ->where('kontes_id', $kontesId)
            ->whereIn('bonsai_id', $bonsaiIds);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('bonsai', fn($b) => $b->where('nama_pohon', 'like', "%{$search}%"));
            });
        }

        $pendaftarans = $query->orderBy('nomor_pendaftaran') // optional untuk urutan
            ->paginate(10)
            ->withQueryString(); // agar search tetap saat pindah halaman

        return view('admin.riwayat.peserta', compact('kontes', 'juri', 'pendaftarans'));
    }

    public function riwayatAdminDetail($kontesId, $juriId, $bonsaiId)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $kontes = Kontes::findOrFail($kontesId);
        $bonsai = Bonsai::with('user')->findOrFail($bonsaiId);
        $juri = Juri::with('user')->findOrFail($juriId);

        $nilaiAwal = Nilai::where('id_bonsai', $bonsaiId)
            ->where('id_juri', $juriId)
            ->where('id_kontes', $kontesId)
            ->get();

        $defuzzifikasiPerKriteria = Defuzzifikasi::where('id_bonsai', $bonsaiId)
            ->where('id_juri', $juriId)
            ->where('id_kontes', $kontesId)
            ->get()
            ->unique('id_kriteria')
            ->map(function ($item) {
                $domain = HelperDomain::where('id_kriteria', $item->id_kriteria)
                    ->whereNull('id_sub_kriteria')
                    ->first();
                $item->nama_kriteria = $domain->kriteria ?? '—';
                return $item;
            });

        $pendaftaran = PendaftaranKontes::where('bonsai_id', $bonsaiId)
            ->where('kontes_id', $kontesId)
            ->first();

        $ruleAktif = HasilFuzzyRule::with(['rule.details'])
            ->where('id_kontes', $kontesId)
            ->where('id_bonsai', $bonsaiId)
            ->where('id_juri', $juriId)
            ->get()
            ->groupBy('id_kriteria');

        $hasilAgregasi = HasilFuzzyRule::where('id_kontes', $kontesId)
            ->where('id_bonsai', $bonsaiId)
            ->where('id_juri', $juriId)
            ->get()
            ->groupBy('id_kriteria');

        $rekap = RekapNilai::where('id_kontes', $kontesId)
            ->where('id_bonsai', $bonsaiId)
            ->first();

        return view('admin.riwayat.detail', compact(
            'kontes',
            'juri',
            'bonsai',
            'nilaiAwal',
            'defuzzifikasiPerKriteria',
            'pendaftaran',
            'ruleAktif',
            'hasilAgregasi',
            'rekap'
        ));
    }

    public function cetakLaporan($kontesId)
    {
        $kontes = Kontes::findOrFail($kontesId);
        $rekapNilai = RekapNilai::where('id_kontes', $kontesId)->get();

        $rekapData = [];

        foreach ($rekapNilai as $rekap) {
            $bonsai = $rekap->bonsai;

            $pendaftaran = PendaftaranKontes::where('kontes_id', $kontesId)
                ->where('bonsai_id', $bonsai->id)
                ->first();

            $hasil = Hasil::where('id_kontes', $kontesId)
                ->where('id_bonsai', $bonsai->id)
                ->get()
                ->groupBy('id_kriteria');

            $kategori = [];

            foreach ($hasil as $idKriteria => $list) {
                // Ambil hanya satu record per kriteria (karena nilai sudah rata-rata)
                $first = $list->first();

                $kriteria = HelperDomain::where('id_kriteria', $idKriteria)
                    ->whereNull('id_sub_kriteria')
                    ->first();

                $kategori[] = [
                    'nama_kriteria' => $kriteria->kriteria ?? '—',
                    'rata2' => round($first->rata_defuzzifikasi, 2),
                    'himpunan' => $first->rata_himpunan,
                ];
            }

            if ($bonsai && $pendaftaran) {
                $rekapData[] = [
                    'nama_pohon' => $bonsai->nama_pohon,
                    'kelas' => $bonsai->kelas,
                    'pemilik' => $bonsai->user->name,
                    'nomor_juri' => $pendaftaran->nomor_juri,
                    'nomor_pendaftaran' => $pendaftaran->nomor_pendaftaran,
                    'skor_akhir' => $rekap->skor_akhir,
                    'himpunan_akhir' => $rekap->himpunan_akhir,
                    'kategori' => $kategori,
                ];
            }
        }

        $pdf = Pdf::loadView('admin.riwayat.cetak', compact('kontes', 'rekapData'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream('laporan-rekap-nilai.pdf');
    }

    public function riwayatJuriIndex(Request $request)
    {
        $juri = Juri::where('user_id', Auth::id())->firstOrFail();

        $kontesIds = Nilai::where('id_juri', $juri->id)->pluck('id_kontes')->unique();

        $query = Kontes::whereIn('id', $kontesIds);

        if ($request->filled('search')) {
            $query->where('nama_kontes', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_mulai_kontes', $request->tahun);
        }

        $kontesList = $query->orderByDesc('tanggal_mulai_kontes')->get();

        return view('juri.riwayat.index', compact('kontesList'));
    }

    public function riwayatJuriPeserta(Request $request, $kontesId)
    {
        $juri = Juri::where('user_id', Auth::id())->firstOrFail();
        $kontes = Kontes::findOrFail($kontesId);

        // Ambil bonsai yang pernah dinilai juri ini di kontes tersebut
        $bonsaiIds = Nilai::where('id_kontes', $kontes->id)
            ->where('id_juri', $juri->id)
            ->pluck('id_bonsai')
            ->unique();

        $query = PendaftaranKontes::with(['user', 'bonsai'])
            ->where('kontes_id', $kontes->id)
            ->whereIn('bonsai_id', $bonsaiIds);

        // Pencarian
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn($q1) => $q1->where('name', 'like', "%$search%"))
                    ->orWhereHas('bonsai', fn($q2) => $q2->where('nama_pohon', 'like', "%$search%"));
            });
        }

        $pendaftarans = $query->get();

        return view('juri.riwayat.peserta', compact('kontes', 'pendaftarans'));
    }

    public function riwayatJuriDetail($kontesId, $bonsaiId)
    {
        $juri = Juri::where('user_id', Auth::id())->firstOrFail();
        $kontes = Kontes::findOrFail($kontesId);
        $bonsai = Bonsai::with('user')->findOrFail($bonsaiId);

        $nilaiAwal = Nilai::where('id_bonsai', $bonsaiId)
            ->where('id_juri', $juri->id)
            ->where('id_kontes', $kontesId)
            ->get();

        $defuzzifikasiPerKriteria = Defuzzifikasi::where('id_bonsai', $bonsaiId)
            ->where('id_juri', $juri->id)
            ->where('id_kontes', $kontesId)
            ->get()
            ->unique('id_kriteria')
            ->map(function ($item) {
                $domain = HelperDomain::where('id_kriteria', $item->id_kriteria)
                    ->whereNull('id_sub_kriteria')
                    ->first();
                $item->nama_kriteria = $domain->kriteria ?? '—';
                return $item;
            });

        $pendaftaran = PendaftaranKontes::where('bonsai_id', $bonsaiId)
            ->where('kontes_id', $kontesId)
            ->first();

        $ruleAktif = HasilFuzzyRule::with(['rule.details'])
            ->where('id_kontes', $kontesId)
            ->where('id_bonsai', $bonsaiId)
            ->where('id_juri', $juri->id)
            ->get()
            ->groupBy('id_kriteria');

        $hasilAgregasi = HasilFuzzyRule::where('id_kontes', $kontesId)
            ->where('id_bonsai', $bonsaiId)
            ->where('id_juri', $juri->id)
            ->get()
            ->groupBy('id_kriteria');

        $rekap = RekapNilai::where('id_kontes', $kontesId)
            ->where('id_bonsai', $bonsaiId)
            ->first();

        return view('juri.riwayat.detail', compact(
            'kontes',
            'bonsai',
            'nilaiAwal',
            'defuzzifikasiPerKriteria',
            'pendaftaran',
            'ruleAktif',
            'hasilAgregasi',
            'rekap'
        ));
    }
}
