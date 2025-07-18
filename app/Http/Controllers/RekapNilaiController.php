<?php

namespace App\Http\Controllers;

use App\Models\Bonsai;
use App\Models\Defuzzifikasi;
use App\Models\HelperKriteria;
use App\Models\PendaftaranKontes;
use App\Models\RekapNilai;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;

class RekapNilaiController extends Controller
{
    public function index(RekapNilai $rekap)
    {
        $rekapData = $rekap->with('bonsai.user')->get()->map(function ($item) {
            $bonsai = $item->bonsai;
            $pendaftaran = PendaftaranKontes::where('bonsai_id', $item->id_bonsai)->first();
            if (!$pendaftaran) return null;

            $kategori = Defuzzifikasi::where('id_bonsai', $item->id_bonsai)
                ->get()
                ->groupBy('id_kriteria')
                ->mapWithKeys(function ($group, $id_kriteria) {
                    $namaKriteria = HelperKriteria::find($id_kriteria)?->kriteria ?? 'Tanpa Nama';
                    return [
                        $namaKriteria => $group->map(function ($d) {
                            return [
                                'hasil' => $d->hasil_defuzzifikasi,
                                'himpunan' => $d->hasil_himpunan,
                                'juri_id' => $d->id_juri,
                            ];
                        })->all()
                    ];
                })->toArray();

            return [
                'nomor_pendaftaran' => $pendaftaran->nomor_pendaftaran,
                'nomor_juri' => $pendaftaran->nomor_juri,
                'nama_pohon' => $bonsai->nama_pohon,
                'pemilik' => $bonsai->user->name ?? '-',
                'skor_akhir' => $item->skor_akhir,
                'himpunan_akhir' => $item->himpunan_akhir,
                'kategori' => $kategori,
            ];
        })->filter()->sortByDesc('skor_akhir')->values();

        $bestTen = $rekapData->take(10);

        return view('juri.rekap.index', [
            'rekapSorted' => $rekapData,
            'bestTen' => $bestTen,
        ]);
    }

    public function show($nama_pohon, $nomor_juri)
    {
        $rekapData = $this->index(new RekapNilai())->getData()['rekapSorted'];

        $detail = $rekapData->first(function ($item) use ($nama_pohon, $nomor_juri) {
            return $item['nama_pohon'] === urldecode($nama_pohon) && $item['nomor_juri'] == $nomor_juri;
        });

        if (!$detail) {
            abort(404, 'Data tidak ditemukan');
        }

        return view('juri.rekap.show', compact('detail'));
    }

    public function exportPdf($nama_pohon)
    {
        $rekapData = $this->index(new RekapNilai())->getData()['rekapSorted'];

        $detail = $rekapData->firstWhere('nama_pohon', urldecode($nama_pohon));
        if (!$detail) abort(404);

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('juri.rekap.pdf', compact('detail'))->setPaper('A4', 'portrait');
        return $pdf->download('Rekap_Nilai_Bonsai_' . $detail['nama_pohon'] . '.pdf');
    }

    public function cetak()
    {
        $rekapData = RekapNilai::with('bonsai.user')->get()->map(function ($item) {
            $bonsai = $item->bonsai;
            $pemilik = $bonsai->user->name ?? '-';

            return [
                'nama_pohon' => $bonsai->nama_pohon,
                'pemilik' => $pemilik,
                'skor_akhir' => $item->skor_akhir,
                'himpunan_akhir' => $item->himpunan_akhir,
            ];
        })->sortByDesc('skor_akhir')->values();

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('juri.rekap.cetak_laporan', compact('rekapData'))->setPaper('A4', 'portrait');
        return $pdf->download('Laporan_Rekap_Nilai_Semua_Bonsai.pdf');
    }
}
