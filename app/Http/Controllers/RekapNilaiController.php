<?php

namespace App\Http\Controllers;

use App\Models\Bonsai;
use App\Models\Defuzzifikasi;
use App\Models\Hasil;
use App\Models\HelperDomain;
use App\Models\HelperKriteria;
use App\Models\Kontes;
use App\Models\PendaftaranKontes;
use App\Models\RekapNilai;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
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
                'id' => $bonsai->id,
                'kontes_id' => $pendaftaran->kontes_id,
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

        // Ambil salah satu kontes dari data (ambil kontes pertama saja)
        $first = $rekapData->first();
        $kontes = null;

        if ($first && isset($first['kontes_id'])) {
            $kontes = Kontes::find($first['kontes_id']);
        }

        return view('juri.rekap.index', [
            'rekapSorted' => $rekapData,
            'bestTen' => $bestTen,
            'kontes' => $kontes, // ini dia yang ditambahkan
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

    public function cetakRekapPerBonsai($id)
    {
        $bonsai = Bonsai::with('user')->findOrFail($id);

        $rekap = RekapNilai::where('id_bonsai', $id)->latest()->first();
        if (!$rekap) {
            abort(404, 'Rekap nilai tidak ditemukan.');
        }

        $pendaftaran = PendaftaranKontes::where('bonsai_id', $id)
            ->where('kontes_id', $rekap->id_kontes)
            ->first();

        $hasil = Hasil::where('id_bonsai', $id)
            ->where('id_kontes', $rekap->id_kontes)
            ->get()
            ->groupBy('id_kriteria');

        $kategori = [];

        foreach ($hasil as $idKriteria => $list) {
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

        $detail = [
            'id' => $bonsai->id,
            'nama_pohon' => $bonsai->nama_pohon,
            'kelas' => $bonsai->kelas,
            'pemilik' => $bonsai->user->name,
            'nomor_juri' => $pendaftaran->nomor_juri ?? '—',
            'nomor_pendaftaran' => $pendaftaran->nomor_pendaftaran ?? '—',
            'skor_akhir' => $rekap->skor_akhir,
            'himpunan_akhir' => $rekap->himpunan_akhir,
            'kategori' => $kategori,
        ];

        $pdf = FacadePdf::loadView('juri.rekap.cetak_per_bonsai', compact('detail'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream('rekap-bonsai-' . $bonsai->id . '.pdf');
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
                $rata2 = $list->avg('hasil');
                $mayoritas = $list->countBy('himpunan')->sortDesc()->keys()->first();

                // Ambil nama kriteria dari helper domain
                $kriteria = HelperDomain::where('id_kriteria', $idKriteria)
                    ->whereNull('id_sub_kriteria')
                    ->first();

                $kategori[] = [
                    'nama_kriteria' => $kriteria->kriteria ?? '—',
                    'rata2' => round($rata2, 2),
                    'himpunan' => $mayoritas,
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

        $pdf = FacadePdf::loadView('admin.riwayat.cetak', compact('kontes', 'rekapData'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream('laporan-rekap-nilai.pdf');
    }
}
