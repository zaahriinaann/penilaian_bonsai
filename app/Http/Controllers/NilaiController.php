<?php

namespace App\Http\Controllers;

use App\Models\Bonsai;
use App\Models\Kontes;
use App\Models\Nilai;
use App\Models\PendaftaranKontes;
use App\Models\RekapNilai;
use App\Models\Defuzzifikasi;
use App\Models\Hasil;
use App\Models\HasilFuzzyRule;
use App\Models\HelperKriteria;
use App\Models\HelperDomain;
use App\Models\HelperSubKriteria;
use App\Models\Juri;
use App\Services\FuzzyMamDaniService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NilaiController extends Controller
{
    public function index()
    {
        $kontes = Kontes::where('status', 1)->first();
        $pendaftarans = [];

        if ($kontes) {
            $pendaftarans = PendaftaranKontes::with(['user', 'bonsai'])
                ->where('kontes_id', $kontes->id)
                ->get();
        }

        return view('juri.nilai.index', compact('pendaftarans', 'kontes'));
    }

    public function formPenilaian($bonsaiId)
    {
        $bonsai = Bonsai::with('user')->findOrFail($bonsaiId);

        $domains = HelperDomain::with('subKriteria')
            ->get()
            ->groupBy('subKriteria.id_kriteria');

        return view('juri.nilai.nilai', compact('bonsai', 'domains'));
    }

    public function store(Request $request, FuzzyMamDaniService $fuzzy)
    {
        $request->validate([
            'bonsai_id' => 'required',
            'nilai'     => 'required|array',
        ]);

        $bonsai = Bonsai::with('user')->findOrFail($request->bonsai_id);
        $kontes = Kontes::where('status', 1)->first();
        if (!$kontes) {
            return redirect()->back()->withErrors('Tidak ada kontes yang sedang berlangsung.');
        }

        $pendaftaran = PendaftaranKontes::where('kontes_id', $kontes->id)
            ->where('bonsai_id', $bonsai->id)
            ->firstOrFail();
        $juriId = Juri::where('user_id', Auth::id())->value('id');
        $pesertaId = $bonsai->user->id;

        foreach ($request->nilai as $idSubKriteria => $angka) {
            $domains = HelperDomain::where('id_sub_kriteria', $idSubKriteria)->get();

            foreach ($domains as $domain) {
                $mu = $fuzzy->hitungDerajatKeanggotaan($angka, $domain);

                Nilai::create([
                    'id_kontes'             => $kontes->id,
                    'id_pendaftaran'        => $pendaftaran->id,
                    'id_peserta'            => $pesertaId,
                    'id_juri'               => $juriId,
                    'id_bonsai'             => $bonsai->id,
                    'id_kriteria'           => $domain->id_kriteria,
                    'kriteria'              => $domain->kriteria,
                    'id_sub_kriteria'       => $domain->id_sub_kriteria,
                    'sub_kriteria'          => $domain->sub_kriteria,
                    'nilai_awal'            => $angka,
                    'derajat_anggota'       => $mu,
                    'himpunan'              => $domain->himpunan,
                ]);
            }
        }

        $hasilJuri = $fuzzy->hitungFuzzyPerJuri($bonsai->id, $juriId, $kontes->id);
        $hasilTotal = $fuzzy->hitungRekapAkhir($bonsai->id, $kontes->id);

        return redirect()
            ->route('nilai.index')
            ->with('success', 'Nilai berhasil disimpan. Skor juri: ' . $hasilJuri . ' | Rata²: ' . $hasilTotal);
    }

    public function show($id)
    {
        $bonsai = Bonsai::with('user')->findOrFail($id);
        $juriId = Auth::id();
        $juriModelId = Juri::where('user_id', $juriId)->value('id');

        $kontesId = Nilai::where('id_bonsai', $id)
            ->where('id_juri', $juriModelId)
            ->orderByDesc('id')
            ->value('id_kontes');

        $nilaiAwal = Nilai::where('id_bonsai', $id)
            ->where('id_juri', $juriModelId)
            ->where('id_kontes', $kontesId)
            ->get();

        $defuzzifikasiPerKriteria = Defuzzifikasi::where('id_bonsai', $id)
            ->where('id_juri', $juriModelId)
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

        $pendaftaran = PendaftaranKontes::where('bonsai_id', $id)
            ->where('kontes_id', $kontesId)
            ->first();

        // ✅ Tambahkan: ambil rule yang aktif
        $ruleAktif = HasilFuzzyRule::with(['rule.details'])
            ->where('id_kontes', $kontesId)
            ->where('id_bonsai', $id)
            ->where('id_juri', $juriModelId)
            ->orderBy('id_kriteria')
            ->get()
            ->groupBy('id_kriteria');

        $hasilAgregasi = HasilFuzzyRule::where('id_kontes', $kontesId)
            ->where('id_bonsai', $id)
            ->where('id_juri', $juriModelId)
            ->get()
            ->groupBy('id_kriteria');


        return view('juri.nilai.show', compact(
            'bonsai',
            'nilaiAwal',
            'defuzzifikasiPerKriteria',
            'pendaftaran',
            'ruleAktif',
            'hasilAgregasi',
        ));
    }

    public function edit($id)
    {
        $bonsai = Bonsai::with('user')->findOrFail($id);
        $juri = Juri::where('user_id', Auth::id())->firstOrFail();

        // Ambil semua nilai yang SUDAH diinput juri
        $nilai = Nilai::where('id_bonsai', $id)
            ->where('id_juri', $juri->id)
            ->whereNotNull('nilai_awal')
            ->get()
            ->keyBy('id_sub_kriteria');

        // Ambil semua sub-kriteria yang dipakai dalam penilaian bonsai ini
        $subDomains = HelperDomain::whereNotNull('id_sub_kriteria')
            ->whereIn('id_kriteria', $nilai->pluck('id_kriteria')->unique())
            ->get()
            ->unique('id_sub_kriteria') // ⬅️ hanya ambil 1 per sub_kriteria
            ->groupBy('kriteria');      // ⬅️ grup berdasarkan nama kriteria


        $data = [];

        foreach ($subDomains as $namaKriteria => $subList) {
            $subs = [];

            foreach ($subList as $sub) {
                $subs[] = [
                    'id_sub_kriteria'    => $sub->id_sub_kriteria,
                    'nama_sub_kriteria'  => $sub->sub_kriteria,
                    'nilai_awal'         => $nilai[$sub->id_sub_kriteria]->nilai_awal ?? null,
                ];
            }

            $data[] = [
                'kriteria'       => $namaKriteria,
                'sub_kriterias'  => $subs,
            ];
        }

        return view('juri.nilai.edit', compact('bonsai', 'data'));
    }

    public function update(Request $request, FuzzyMamDaniService $fuzzy, $bonsaiId)
    {
        $request->validate([
            'nilai' => 'required|array',
        ]);

        $bonsai = Bonsai::with('user')->findOrFail($bonsaiId);
        $juriId = Juri::where('user_id', Auth::id())->value('id');
        $kontes = Kontes::where('status', 1)->firstOrFail();
        $pesertaId = $bonsai->user->id;

        $pendaftaran = PendaftaranKontes::where('kontes_id', $kontes->id)
            ->where('bonsai_id', $bonsaiId)
            ->firstOrFail();

        // Hapus nilai lama juri ini untuk bonsai ini
        Nilai::where('id_bonsai', $bonsaiId)
            ->where('id_juri', $juriId)
            ->where('id_kontes', $kontes->id)
            ->delete();

        foreach ($request->nilai as $idSubKriteria => $angka) {
            $domains = HelperDomain::where('id_sub_kriteria', $idSubKriteria)->get();

            foreach ($domains as $domain) {
                $mu = $fuzzy->hitungDerajatKeanggotaan($angka, $domain);

                Nilai::create([
                    'id_kontes'             => $kontes->id,
                    'id_pendaftaran'        => $pendaftaran->id,
                    'id_peserta'            => $pesertaId,
                    'id_juri'               => $juriId,
                    'id_bonsai'             => $bonsai->id,
                    'id_kriteria'           => $domain->id_kriteria,
                    'kriteria'              => $domain->kriteria,
                    'id_sub_kriteria'       => $domain->id_sub_kriteria,
                    'sub_kriteria'          => $domain->sub_kriteria,
                    'nilai_awal'            => $angka,
                    'derajat_anggota'       => $mu,
                    'himpunan'              => $domain->himpunan,
                ]);
            }
        }

        // Hitung hasil fuzzy untuk juri ini & rekap akhir
        $hasilJuri = $fuzzy->hitungFuzzyPerJuri($bonsai->id, $juriId, $kontes->id);
        $hasilTotal = $fuzzy->hitungRekapAkhir($bonsai->id, $kontes->id);

        return redirect()
            ->route('nilai.index')
            ->with('success', 'Nilai berhasil diperbarui. Skor juri: ' . $hasilJuri . ' | Rata²: ' . $hasilTotal);
    }

    public function destroy(Nilai $nilai)
    {
        // belum digunakan
    }

    public function indexAdmin()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $juriAktif = Juri::with('user')->get();

        return view('admin.nilai.index', compact('juriAktif'));
    }

    public function showAdmin($juriId)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $kontes = Kontes::where('status', 1)->firstOrFail();
        $bonsaiIds = Nilai::where('id_juri', $juriId)
            ->where('id_kontes', $kontes->id)
            ->pluck('id_bonsai')
            ->unique();

        $pendaftarans = PendaftaranKontes::with(['user', 'bonsai'])
            ->whereIn('bonsai_id', $bonsaiIds)
            ->where('kontes_id', $kontes->id)
            ->get();

        $juri = Juri::with('user')->findOrFail($juriId);

        return view('admin.nilai.show', compact('pendaftarans', 'kontes', 'juri'));
    }

    public function detailAdmin($juriId, $bonsaiId)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $bonsai = Bonsai::with('user')->findOrFail($bonsaiId);
        $kontes = Kontes::where('status', 1)->firstOrFail();
        $juri = Juri::with('user')->findOrFail($juriId);

        $nilaiAwal = Nilai::where('id_bonsai', $bonsaiId)
            ->where('id_juri', $juriId)
            ->where('id_kontes', $kontes->id)
            ->get();

        $defuzzifikasiPerKriteria = Defuzzifikasi::where('id_bonsai', $bonsaiId)
            ->where('id_juri', $juriId)
            ->where('id_kontes', $kontes->id)
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
            ->where('kontes_id', $kontes->id)
            ->first();

        $ruleAktif = HasilFuzzyRule::with(['rule.details'])
            ->where('id_kontes', $kontes->id)
            ->where('id_bonsai', $bonsaiId)
            ->where('id_juri', $juriId)
            ->get()
            ->groupBy('id_kriteria');

        $hasilAgregasi = HasilFuzzyRule::where('id_kontes', $kontes->id)
            ->where('id_bonsai', $bonsaiId)
            ->where('id_juri', $juriId)
            ->get()
            ->groupBy('id_kriteria');

        return view('admin.nilai.detail', compact(
            'bonsai',
            'nilaiAwal',
            'defuzzifikasiPerKriteria',
            'pendaftaran',
            'ruleAktif',
            'hasilAgregasi',
            'juri'
        ));
    }

    public function riwayatIndex(Request $request)
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

        $kontesList = $query->orderByDesc('tanggal_mulai_kontes')->get();

        return view('admin.riwayat.index', compact('kontesList'));
    }

    public function riwayatJuri(Request $request, $kontesId)
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

        $juriList = $juriQuery->get();

        return view('admin.riwayat.juri', compact('kontes', 'juriList'));
    }

    public function riwayatPeserta(Request $request, $kontesId, $juriId)
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

        $pendaftarans = $query->get();

        return view('admin.riwayat.peserta', compact('kontes', 'juri', 'pendaftarans'));
    }

    public function riwayatDetail($kontesId, $juriId, $bonsaiId)
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
