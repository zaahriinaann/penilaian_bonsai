<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kontes;
use App\Models\Bonsai;
use App\Models\Defuzzifikasi;
use App\Models\Hasil;
use App\Models\Juri;
use App\Models\Nilai;
use App\Models\Kriteria;
use App\Models\PendaftaranKontes;
use App\Models\RekapNilai;
use App\Models\SubKriteria;
use App\Models\User;

class RiwayatController extends Controller
{
    public function index()
    {
        $kontes = Kontes::all();
        return view('juri.riwayat.index', compact('kontes'));
    }

    public function show($id)
    {
        $kontes = Kontes::findOrFail($id);
        $bonsai = $kontes->bonsai()->with('pemilik')->get(); // relasi many-to-many via pendaftaran_kontes

        return view('juri.riwayat.show', compact('kontes', 'bonsai'));
    }


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
}
