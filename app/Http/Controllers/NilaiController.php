<?php

namespace App\Http\Controllers;

use App\Models\Bonsai;
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
            'bonsai_id' => 'required|exists:bonsai,id',
            'nilai'     => 'required|array',
        ]);

        /** 1. Ambil data Bonsai + pemilik (peserta) */
        $bonsai   = Bonsai::with('user')->findOrFail($request->bonsai_id);
        $pesertaId = $bonsai->user->id;

        /** 2. Kontes aktif  */
        $kontes = Kontes::where('status', 1)->firstOrFail();

        /** 3. Cek pendaf­taran bonsai di kontes aktif */
        $pendaftaran = PendaftaranKontes::where('kontes_id', $kontes->id)
            ->where('bonsai_id', $bonsai->id)
            ->firstOrFail();          // error 404 kalau belum terdaftar

        /** 4. ID juri yang login */
        $juriId = Auth::id();                     // ✔️ tidak error

        /** 5. Simpan tiap nilai sub‑kriteria */
        foreach ($request->nilai as $idKriteriaPenilaian => $angka) {
            Nilai::create([
                'id_kontes'             => $kontes->id,
                'id_pendaftaran'        => $pendaftaran->id,
                'id_peserta'            => $pesertaId,
                'id_juri'               => $juriId,
                'id_bonsai'             => $bonsai->id,
                'id_kriteria_penilaian' => $idKriteriaPenilaian,
                'd_keanggotaan'         => $angka,
                'defuzzifikasi'         => 0,      // dihitung nanti
            ]);
        }

        /** 6. Tandai bonsai “Sudah Dinilai” */
        $pendaftaran->update(['status' => '1']);

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

        $nilaiTersimpan = Nilai::where('id_bonsai', $bonsaiId)
            ->where('id_juri', Auth::id())
            ->get()
            ->keyBy('id_kriteria_penilaian');

        return view('juri.nilai.show', compact('bonsai', 'penilaians', 'nilaiTersimpan'));
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
                    'd_keanggotaan' => $angka,
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
