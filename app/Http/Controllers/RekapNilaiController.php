<?php

namespace App\Http\Controllers;

use App\Models\Bonsai;
use App\Models\Defuzzifikasi;
use App\Models\HelperKriteria;
use App\Models\Kontes;
use App\Models\PendaftaranKontes;
use App\Models\RekapNilai;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class RekapNilaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rekapNilais = RekapNilai::with('bonsai.user')->get();

        $rekapData = collect();

        foreach ($rekapNilais as $rekap) {
            $bonsai = $rekap->bonsai;
            $pendaftaran = PendaftaranKontes::where('bonsai_id', $rekap->id_bonsai)->first();
            if (!$pendaftaran) continue;

            $kategori = [];
            $defuzz = Defuzzifikasi::where('id_bonsai', $rekap->id_bonsai)->get();

            foreach ($defuzz as $d) {
                $namaKriteria = HelperKriteria::find($d->id_kriteria)?->kriteria ?? 'Tanpa Nama';
                $kategori[$namaKriteria][] = [
                    'hasil' => $d->hasil_defuzzifikasi,
                    'himpunan' => $d->hasil_himpunan,
                    'juri_id' => $d->id_juri,
                ];
            }

            $rekapData->push([
                'nomor_pendaftaran' => $pendaftaran->nomor_pendaftaran,
                'nomor_juri' => $pendaftaran->nomor_juri,
                'nama_pohon' => $bonsai->nama_pohon,
                'pemilik' => $bonsai->user->name ?? '-',
                'skor_akhir' => $this->hitungSkorAkhir($kategori),
                'kategori' => $kategori,
            ]);
        }

        $rekapSorted = $rekapData->sortByDesc('skor_akhir')->values();
        $bestTen = $rekapSorted->take(10);

        session()->put('rekap_export_data', $rekapSorted->toArray());

        return view('juri.rekap.index', compact('rekapSorted', 'bestTen'));
    }

    private function hitungSkorAkhir(array $kategori): float
    {
        $total = 0;

        foreach ($kategori as $list) {
            $avg = collect($list)->avg('hasil');
            $total += $avg;
        }

        return round(min(360, max(0, $total)), 2);
    }

    public function exportPdf($nama_pohon)
    {
        $data = session()->get('rekap_export_data') ?? [];
        $detail = collect($data)->firstWhere('nama_pohon', urldecode($nama_pohon));
        if (!$detail) abort(404);

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('juri.rekap.pdf', compact('detail'))->setPaper('A4', 'portrait');
        return $pdf->download('Rekap_Nilai_Bonsai_' . $detail['nama_pohon'] . '.pdf');
    }

    private function kontesAktifId()
    {
        return Kontes::where('status', '1')->value('id');
    }



    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(RekapNilai $rekapNilai)
    {
        //
    }

    public function edit(RekapNilai $rekapNilai)
    {
        //
    }

    public function update(Request $request, RekapNilai $rekapNilai)
    {
        //
    }

    public function destroy(RekapNilai $rekapNilai)
    {
        //
    }
}
