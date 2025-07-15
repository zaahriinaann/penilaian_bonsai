<?php

namespace App\Http\Controllers;

use App\Models\Bonsai;
use App\Models\Kontes;
use App\Models\Nilai;
use App\Models\PendaftaranKontes;
use App\Models\RekapNilai;
use App\Models\Defuzzifikasi;
use App\Models\HelperKriteria;
use App\Models\HelperDomain;
use App\Models\HelperSubKriteria;
use App\Models\Juri;
use App\Services\FuzzyMamDaniService;
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

        // Ambil id_juri berdasarkan user login (users.id = juri.user_id)
        $juri = Juri::where('user_id', Auth::id())->first();
        if (!$juri) {
            abort(403, 'Anda bukan juri yang terdaftar.');
        }

        $nilaiAwal = Nilai::where('id_bonsai', $id)
            ->where('id_juri', $juri->id)
            ->whereNotNull('nilai_awal')
            ->get();

        $nilaiPerJuri = Defuzzifikasi::where('id_bonsai', $id)
            ->where('id_juri', $juri->id)
            ->get();

        return view('juri.nilai.show', compact('bonsai', 'nilaiAwal', 'nilaiPerJuri'));
    }


    public function edit($id)
    {
        $kriterias = HelperKriteria::with('subKriterias')->get();
        $nilai = Nilai::where('id_bonsai', $id)
            ->where('id_juri', Auth::id())
            ->get()
            ->keyBy('id_sub_kriteria');

        $data = [];
        foreach ($kriterias as $kriteria) {
            $subs = [];
            foreach ($kriteria->subKriterias as $sub) {
                $subs[] = [
                    'id_sub_kriteria' => $sub->id_sub_kriteria,
                    'nama_sub_kriteria' => $sub->sub_kriteria,
                    'nilai_awal' => $nilai[$sub->id_sub_kriteria]->nilai_awal ?? null,
                ];
            }
            $data[] = [
                'kriteria' => $kriteria->kriteria,
                'sub_kriterias' => $subs,
            ];
        }

        $bonsai = Bonsai::with('user')->findOrFail($id);

        return view('juri.nilai.edit', compact('data', 'bonsai'));
    }

    public function update(Request $request, FuzzyMamDaniService $fuzzy, $bonsaiId)
    {
        $request->validate([
            'nilai' => 'required|array',
        ]);

        $bonsai = Bonsai::with('user')->findOrFail($bonsaiId);
        $juriId = Juri::where('user_id', Auth::id())->value('id');

        $kontes = Kontes::where('status', 1)->first();
        if (!$kontes) {
            return redirect()->back()->withErrors('Tidak ada kontes yang sedang berlangsung.');
        }

        $pesertaId = $bonsai->user->id;
        $pendaftaran = PendaftaranKontes::where('bonsai_id', $bonsaiId)->firstOrFail();

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
}
