<?php

namespace App\Http\Controllers;

use App\Models\Bonsai;
use App\Models\Defuzzifikasi;
use App\Models\HelperDomain;
use App\Models\HelperKriteria;
use App\Models\Kontes;
use App\Models\PendaftaranKontes;
use App\Models\RekapNilai;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class RekapNilaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil semua bonsai yang punya skor akhir di tabel rekap_nilai
        $rekapNilais = RekapNilai::with('bonsai.user')->get();

        $rekapData = collect();

        foreach ($rekapNilais as $rekap) {
            $bonsai = $rekap->bonsai;

            // Ambil data pendaftaran (untuk nomor juri dan nomor pendaftaran)
            $pendaftaran = PendaftaranKontes::where('bonsai_id', $rekap->id_bonsai)->first();
            if (!$pendaftaran) continue;

            $kategori = [];

            // Ambil semua defuzzifikasi untuk bonsai ini (boleh dari juri manapun)
            $defuzz = Defuzzifikasi::where('id_bonsai', $rekap->id_bonsai)->get();
            foreach ($defuzz as $d) {
                $namaKriteria = HelperKriteria::find($d->id_kriteria)?->kriteria ?? 'Tanpa Nama';
                $kategori[$namaKriteria] = [
                    'hasil' => $d->hasil_defuzzifikasi,
                    'himpunan' => $d->hasil_himpunan,
                ];
            }

            $rekapData->push([
                'nomor_pendaftaran' => $pendaftaran->nomor_pendaftaran,
                'nomor_juri' => $pendaftaran->nomor_juri,
                'nama_pohon' => $bonsai->nama_pohon,
                'pemilik' => $bonsai->user->name ?? '-',
                'skor_akhir' => $rekap->skor_akhir,
                'kategori' => $kategori,
            ]);
        }

        // Urutkan berdasarkan skor akhir
        $rekapSorted = $rekapData->sortByDesc('skor_akhir')->values();
        $bestTen = $rekapSorted->take(10);

        return view('juri.rekap.index', compact('rekapSorted', 'bestTen'));
    }
    private function kontesAktifId()
    {
        return Kontes::where('status', '1')->value('id');
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(RekapNilai $rekapNilai)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RekapNilai $rekapNilai)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RekapNilai $rekapNilai)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RekapNilai $rekapNilai)
    {
        //
    }
}
