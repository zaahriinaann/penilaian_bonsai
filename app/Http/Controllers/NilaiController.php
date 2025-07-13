<?php

namespace App\Http\Controllers;

use App\Models\Bonsai;
use App\Models\Juri;
use App\Models\Kontes;
use App\Models\Nilai;
use App\Models\PendaftaranKontes;
use App\Models\Penilaian;
use App\Models\User;
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

        // dd($pendaftaran, $kontes, $bonsai, $pesertaId, $juriId);
        foreach ($request->nilai as $idKriteriaPenilaian => $angka) {
            $kriteria = Penilaian::findOrFail($idKriteriaPenilaian);

            // hitung defuzzifikasi di sini
            $nilai = Nilai::hitungFuzzy($angka, $kriteria);

            dd($nilai);
            Nilai::create([
                'id_kontes'             => $kontes->id,
                'id_pendaftaran'        => $pendaftaran->id,
                'id_peserta'            => $pesertaId,
                'id_juri'               => $juriId,
                'id_bonsai'             => $bonsai->id,
                'id_kriteria_penilaian' => $idKriteriaPenilaian,
                'nilai_awal'            => $angka,
                'derajat_anggota'       => $nilai,      // dihitung nanti
            ]);
        }

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
        $penilaians = Penilaian::all()
            ->groupBy(['kriteria', 'sub_kriteria']);

        $bonsai = Bonsai::with('user')->findOrFail($bonsaiId);

        return view('juri.nilai.show', compact('bonsai', 'penilaians'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Nilai $nilai)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    // Langkah 3b: Update nilai lama
    public function update(Request $request, $id)
    {
        $request->validate([
            'nilai' => 'required|array',
        ]);

        foreach ($request->nilai as $id_kriteria_penilaian => $angka) {
            $nilai = Nilai::where('id_bonsai', $id)
                ->where('id_juri', Auth::id())
                ->where('id_kriteria_penilaian', $id_kriteria_penilaian)
                ->first();

            if ($nilai) {
                $nilai->update([
                    'nilai_awal' => $angka,
                    'defuzzifikasi' => 0,
                ]);
            }
        }

        return redirect()->route('nilai.index')->with('success', 'Nilai diperbarui.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Nilai $nilai)
    {
        //
    }
}
