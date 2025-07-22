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
use App\Services\d;
use App\Services\FuzzyMamdaniService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NilaiController extends Controller
{
    public function index()
    {
        $kontes = Kontes::where('status', 1)->first();
        $pendaftarans = collect(); // default empty collection

        if ($kontes) {
            $pendaftarans = PendaftaranKontes::with(['user', 'bonsai'])
                ->where('kontes_id', $kontes->id)
                ->paginate(10); // PAGINATION
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

    public function store(Request $request, FuzzyMamdaniService $fuzzy)
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
            ->route('juri.nilai.index')
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

        // Ambil Defuzzifikasi sekaligus helperDomain, lalu groupBy kriteria
        $defuzzifikasiPerKriteria = Defuzzifikasi::with('helperDomain')
            ->where('id_bonsai', $id)
            ->where('id_juri', $juriModelId)
            ->where('id_kontes', $kontesId)
            ->get()
            ->groupBy('id_kriteria');

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

        // 1) Ambil proses agregasi (sudah groupBy di id_kriteria)
        $hasilAgregasi = HasilFuzzyRule::where('id_kontes', $kontesId)
            ->where('id_bonsai', $id)
            ->where('id_juri', $juriModelId)
            ->get()
            ->groupBy('id_kriteria');

        // 2) Ambil data defuzzifikasi model, eager-load helperDomain, keyBy id_kriteria
        $defuzzMap = Defuzzifikasi::with('helperDomain')
            ->where('id_bonsai', $id)
            ->where('id_juri', $juriModelId)
            ->where('id_kontes', $kontesId)
            ->get()
            ->keyBy('id_kriteria');


        return view('juri.nilai.show', compact(
            'bonsai',
            'nilaiAwal',
            'defuzzMap',
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
            ->route('juri.nilai.index')
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
        // 1. Guard: hanya admin
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        // 2. Entities: Bonsai, Kontes aktif, Juri
        $bonsai = Bonsai::with('user')->findOrFail($bonsaiId);
        $kontes = Kontes::where('status', 1)->firstOrFail();
        $juri   = Juri::with('user')->findOrFail($juriId);

        // 3. Nilai awal oleh juri ini untuk bonsai & kontes
        $nilaiAwal = Nilai::where([
            ['id_kontes', $kontes->id],
            ['id_bonsai', $bonsaiId],
            ['id_juri',   $juriId],
        ])->get();

        // 4. Defuzzifikasi per kriteria → eager load helperDomain + keyBy(kriteria)
        $defuzzMap = Defuzzifikasi::with(['helperDomain' => function ($q) {
            $q->whereNull('id_sub_kriteria');
        }])
            ->where([
                ['id_kontes', $kontes->id],
                ['id_bonsai', $bonsaiId],
                ['id_juri',   $juriId],
            ])
            ->get()
            ->keyBy('id_kriteria');

        // 5. Data pendaftaran (nomor pendaftaran & nomor juri)
        $pendaftaran = PendaftaranKontes::where([
            ['kontes_id', $kontes->id],
            ['bonsai_id', $bonsaiId],
        ])->first();

        // 6. Rule Inferensi Aktif per kriteria
        $ruleAktif = HasilFuzzyRule::with('rule.details')
            ->where([
                ['id_kontes', $kontes->id],
                ['id_bonsai', $bonsaiId],
                ['id_juri',   $juriId],
            ])
            ->get()
            ->groupBy('id_kriteria');

        // 7. Hasil Agregasi per kriteria
        $hasilAgregasi = HasilFuzzyRule::where([
            ['id_kontes', $kontes->id],
            ['id_bonsai', $bonsaiId],
            ['id_juri',   $juriId],
        ])
            ->get()
            ->groupBy('id_kriteria');

        // 8. Rekap nilai akhir (atk tabel rekap_nilai)
        $rekap = RekapNilai::where([
            ['id_kontes', $kontes->id],
            ['id_bonsai', $bonsaiId],
        ])
            ->first();

        // 9. Return view dengan semua data
        return view('admin.nilai.detail', compact(
            'bonsai',
            'kontes',
            'juri',
            'nilaiAwal',
            'defuzzMap',
            'pendaftaran',
            'ruleAktif',
            'hasilAgregasi',
            'rekap'
        ));
    }
}
