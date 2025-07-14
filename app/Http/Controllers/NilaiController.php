<?php

namespace App\Http\Controllers;

use App\Models\Bonsai;
use App\Models\HelperDomain;
use App\Models\HelperKriteria;
use App\Models\HelperSubKriteria;
use App\Models\Juri;
use App\Models\Kontes;
use App\Models\Nilai;
use App\Models\PendaftaranKontes;
use App\Models\Penilaian;
use App\Models\RekapNilai;
use App\Models\User;
// use App\Helpers\FuzzyEngine;
use App\Services\FuzzyInferenceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NilaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // Langkah 1: Menampilkan daftar bonsai dari kontes yang sedang berlangsung
    public function index()
    {
        $kontes = Kontes::where('status', 1)->firstOrFail();
        $pendaftarans = PendaftaranKontes::with(['user', 'bonsai'])
            ->where('kontes_id', $kontes->id)
            ->get();
        $juriId = Juri::where('user_id', Auth::id())->firstOrFail()->id;

        // dd($pendaftarans, $kontes, $juriId);
        return view('juri.nilai.index', compact('pendaftarans', 'kontes'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    // Langkah 3a: Simpan nilai baru
    public function store(Request $request)
    {
        $request->validate([
            'bonsai_id' => 'required',
            'nilai'     => 'required',
        ]);

        $bonsai   = Bonsai::with('user')->findOrFail($request->bonsai_id);
        $pesertaId = $bonsai->user->id;
        $kontes = Kontes::where('status', 1)->firstOrFail();
        $pendaftaran = PendaftaranKontes::where('kontes_id', $kontes->id)
            ->where('bonsai_id', $bonsai->id)
            ->firstOrFail();
        $juriId = Auth::id();

        foreach ($request->nilai as $idSubKriteria => $angka) {
            // Ambil semua himpunan yang relevan untuk sub kriteria ini
            $domains = HelperDomain::where('id_sub_kriteria', $idSubKriteria)->get();

            foreach ($domains as $domain) {
                [$mu, $_z] = HelperDomain::getCentroidAndMu($angka, $domain->id_sub_kriteria, $domain->himpunan);

                // Simpan semua hasil fuzzy (termasuk yang μ = 0)
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


        $outputRules = FuzzyInferenceService::inferensi($bonsai->id, $juriId, $kontes->id);

        foreach ($outputRules as $himpunan => $mu) {
            Nilai::create([
                'id_kontes'      => $kontes->id,
                'id_pendaftaran' => $pendaftaran->id,
                'id_peserta'     => $pesertaId,
                'id_juri'        => $juriId,
                'id_bonsai'      => $bonsai->id,
                'sub_kriteria'   => 'Penampilan',
                'himpunan'       => $himpunan,
                'derajat_anggota' => $mu,
                'nilai_awal'     => null,
            ]);
        }

        $skorAkhir = Nilai::defuzzifikasi($bonsai->id, $juriId, $kontes->id);
        // rule

        // dd($skorAkhir); // defuzzifikasi
        RekapNilai::updateOrCreate(
            [
                'id_kontes' => $kontes->id,
                'id_bonsai' => $bonsai->id,
                'id_juri'   => $juriId,
            ],
            [
                'skor_akhir' => $skorAkhir,
            ]
        );


        return redirect()
            ->route('nilai.index')
            ->with('success', 'Nilai berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     */
    // Langkah 2: Tampilkan form nilai (edit kalau sudah pernah dinilai)
    public function show($bonsaiId)
    {
        $bonsai = Bonsai::with('user')->findOrFail($bonsaiId);

        // Ambil semua domain dan relasi ke sub_kriteria & kriteria
        $domains = HelperDomain::with('subKriteria') // pastikan ada relasi di model
            ->get()
            ->groupBy('subKriteria.id_kriteria');

        return view('juri.nilai.show', compact('bonsai', 'domains'));
    }


    /**
     * Show the form for editing the specified resource.
     */
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
        // $domains = HelperDomain::with('subKriteria') // pastikan ada relasi di model
        //     ->get()
        //     ->groupBy('subKriteria.id_kriteria');

        return view('juri.nilai.edit', compact('data', 'bonsai'));
    }

    /**
     * Update the specified resource in storage.
     */
    // Langkah 3b: Update nilai lama
    public function update(Request $request, $bonsaiId)
    {
        $request->validate([
            'nilai' => 'required|array',
            // pastikan field lain sesuai kebutuhan
        ]);

        $bonsai = Bonsai::with('user')->findOrFail($bonsaiId);
        $juriId = Auth::id();
        $kontes = Kontes::where('status', 1)->firstOrFail();
        $pesertaId = $bonsai->user->id;
        $pendaftaran = PendaftaranKontes::where('bonsai_id', $bonsaiId)->firstOrFail();

        // 1. Hapus semua nilai lama untuk bonsai-juri-kontes ini
        Nilai::where('id_bonsai', $bonsaiId)
            ->where('id_juri', $juriId)
            ->where('id_kontes', $kontes->id)
            ->delete();

        // 2. Simpan ulang hasil fuzzyfikasi input baru
        foreach ($request->nilai as $idSubKriteria => $angka) {
            $domains = HelperDomain::where('id_sub_kriteria', $idSubKriteria)->get();
            foreach ($domains as $domain) {
                [$mu, $_z] = HelperDomain::getCentroidAndMu($angka, $domain->id_sub_kriteria, $domain->himpunan);
                Nilai::create([
                    'id_kontes'      => $kontes->id,
                    'id_peserta'     => $pesertaId,
                    'id_pendaftaran' => $pendaftaran->id,
                    'id_juri'        => $juriId,
                    'id_bonsai'      => $bonsai->id,
                    'id_kriteria'    => $domain->id_kriteria,
                    'kriteria'       => $domain->kriteria,
                    'id_sub_kriteria' => $domain->id_sub_kriteria,
                    'sub_kriteria'   => $domain->sub_kriteria,
                    'nilai_awal'     => $angka,
                    'derajat_anggota' => $mu,
                    'himpunan'       => $domain->himpunan,
                ]);
            }
        }

        // 3. Proses inferensi dan simpan output rules baru
        $outputRules = \App\Services\FuzzyInferenceService::inferensi($bonsai->id, $juriId, $kontes->id);
        foreach ($outputRules as $himpunan => $mu) {
            Nilai::create([
                'id_kontes'      => $kontes->id,
                'id_peserta'     => $pesertaId,
                'id_juri'        => $juriId,
                'id_bonsai'      => $bonsai->id,
                'sub_kriteria'   => 'Penampilan',
                'himpunan'       => $himpunan,
                'derajat_anggota' => $mu,
                'nilai_awal'     => null,
            ]);
        }

        // 4. Defuzzifikasi dan update skor akhir
        $skorAkhir = Nilai::defuzzifikasi($bonsai->id, $juriId, $kontes->id);

        RekapNilai::updateOrCreate(
            [
                'id_kontes' => $kontes->id,
                'id_bonsai' => $bonsai->id,
                'id_juri'   => $juriId,
            ],
            [
                'skor_akhir' => $skorAkhir,
            ]
        );

        return redirect()
            ->route('nilai.index')
            ->with('success', 'Nilai berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Nilai $nilai)
    {
        //
    }
}
