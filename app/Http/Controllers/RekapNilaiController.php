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
use App\Services\PeringkatNilaiService;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class RekapNilaiController extends Controller
{
    public function index(RekapNilai $rekap)
    {
        $kontesAktif = Kontes::where('status', '1')->first();
        if (! $kontesAktif) {
            // ... (kamu sudah menangani kasus ini)
        }

        $search = request('search');

        // 1) Ambil semua, map ke objek + include peringkat dari DB
        $all = $rekap->with('bonsai.user')
            ->where('id_kontes', $kontesAktif->id)
            ->get()
            ->map(function ($item) {
                $bonsai      = $item->bonsai;
                $pendaftaran = PendaftaranKontes::where('bonsai_id', $item->id_bonsai)->first();
                if (! $pendaftaran) {
                    return null;
                }
                return (object) [
                    'id'                => $bonsai->id,
                    'kontes_id'         => $pendaftaran->kontes_id,
                    'nomor_pendaftaran' => $pendaftaran->nomor_pendaftaran,
                    'nomor_juri'        => $pendaftaran->nomor_juri,
                    'nama_pohon'        => $bonsai->nama_pohon,
                    'kelas'             => $bonsai->kelas,
                    'pemilik'           => $bonsai->user->name ?? '-',
                    'skor_akhir'        => $item->skor_akhir,
                    'himpunan_akhir'    => $item->himpunan_akhir,
                    'peringkat'         => $item->peringkat,        // ← ambil dari DB
                ];
            })
            ->filter();

        // 2) Sort by skor_akhir DESC
        $all = $all->sortByDesc('skor_akhir')->values();

        // 3) Set fallback peringkat kalau belum di‐generate
        $all = $all->map(function ($b, $index) {
            $b->peringkat = $b->peringkat ?? ($index + 1);
            return $b;
        });

        // 4) Apply filter pencarian (tanpa memengaruhi peringkat)
        if ($search) {
            $all = $all->filter(/* … */)->values();
        }

        // 5) Paginate manual
        $perPage     = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $items       = $all->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $paginated   = new LengthAwarePaginator(
            $items,
            $all->count(),
            $perPage,
            $currentPage,
            [
                'path'  => LengthAwarePaginator::resolveCurrentPath(),
                'query' => request()->query(),
            ]
        );

        // 6) Top 10 tanpa filter
        $bestTen = $all->take(10);

        return view('rekap-nilai.index', [
            'rekap'   => $paginated,
            'bestTen' => $bestTen,
            'kontes'  => $kontesAktif,
        ]);
    }

    public function show($id)
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
                'rata_defuzzifikasi' => round($first->rata_defuzzifikasi, 2),
                'rata_himpunan' => $first->rata_himpunan,
            ];
        }

        $detail = [
            'id' => $bonsai->id,
            'nama_pohon' => $bonsai->nama_pohon,
            'kelas' => $bonsai->kelas,
            'ukuran_2' => $bonsai->ukuran_2,
            'pemilik' => $bonsai->user->name,
            'nomor_juri' => $pendaftaran->nomor_juri ?? '—',
            'nomor_pendaftaran' => $pendaftaran->nomor_pendaftaran ?? '—',
            'skor_akhir' => $rekap->skor_akhir,
            'himpunan_akhir' => $rekap->himpunan_akhir,
            'kategori' => collect($kategori)->mapWithKeys(function ($item) {
                return [$item['nama_kriteria'] => [
                    'rata_defuzzifikasi' => $item['rata_defuzzifikasi'],
                    'rata_himpunan' => $item['rata_himpunan'],
                ]];
            })->toArray(),
        ];

        return view('rekap-nilai.show', compact('detail'));
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
            'ukuran_2' => $bonsai->ukuran_2,
            'pemilik' => $bonsai->user->name,
            'nomor_juri' => $pendaftaran->nomor_juri ?? '—',
            'nomor_pendaftaran' => $pendaftaran->nomor_pendaftaran ?? '—',
            'skor_akhir' => $rekap->skor_akhir,
            'himpunan_akhir' => $rekap->himpunan_akhir,
            'kategori' => $kategori,
        ];

        $pdf = FacadePdf::loadView('rekap-nilai.cetak_per_bonsai', compact('detail'))
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
                $first = $list->first();

                $rata2 = $first->rata_defuzzifikasi ?? 0;
                $mayoritas = $first->rata_himpunan ?? '—';

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

        $pdf = FacadePdf::loadView('rekap-nilai.cetak_laporan', compact('kontes', 'rekapData'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream('laporan-rekap-nilai.pdf');
    }

    public function indexAnggota()
    {
        if (Auth::user()->role !== 'anggota') {
            abort(403);
        }

        $kontesAktif = Kontes::where('status', 1)->first();

        if (! $kontesAktif) {
            $paginated = new LengthAwarePaginator([], 0, 10, 1, [
                'path'  => LengthAwarePaginator::resolveCurrentPath(),
                'query' => request()->query(),
            ]);

            return view('peserta.nilai.index', [
                'rekap'  => $paginated,
                'kontes' => null,
                'message' => 'Tidak ada kontes aktif.',
            ]);
        }

        $search = request('search');

        $pendaftarans = PendaftaranKontes::with(['bonsai.user', 'bonsai.rekapNilai'])
            ->where('kontes_id', $kontesAktif->id)
            ->where('user_id', Auth::id())
            ->get()
            ->map(function ($item) {
                $rekap = optional($item->bonsai->rekapNilai);

                return (object) [
                    'id'                => $item->bonsai->id,
                    'kontes_id'         => $item->kontes_id,
                    'nomor_pendaftaran' => $item->nomor_pendaftaran,
                    'nomor_juri'        => $item->nomor_juri,
                    'nama_pohon'        => $item->bonsai->nama_pohon,
                    'kelas'             => $item->bonsai->kelas,
                    'pemilik'           => $item->bonsai->user->name ?? '-',
                    'skor_akhir'        => $rekap->skor_akhir,
                    'himpunan_akhir'    => $rekap->himpunan_akhir,
                ];
            });

        // Filter pencarian
        if ($search) {
            $pendaftarans = $pendaftarans->filter(function ($item) use ($search) {
                return Str::contains(Str::lower($item->nomor_pendaftaran), Str::lower($search))
                    || Str::contains(Str::lower($item->nama_pohon), Str::lower($search))
                    || Str::contains(Str::lower($item->pemilik), Str::lower($search));
            })->values();
        }

        // Pagination manual
        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $pagedItems = $pendaftarans->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginated = new LengthAwarePaginator(
            $pagedItems,
            $pendaftarans->count(),
            $perPage,
            $currentPage,
            [
                'path'  => LengthAwarePaginator::resolveCurrentPath(),
                'query' => request()->query(),
            ]
        );

        return view('peserta.nilai.index', [
            'rekap'  => $paginated,
            'kontes' => $kontesAktif,
        ]);
    }

    public function generateRanking(Kontes $kontes)
    {
        // Hitung & simpan peringkat
        app(PeringkatNilaiService::class)
            ->updateRanking($kontes->id);

        // Redirect langsung ke halaman index rekap nilai
        return redirect()
            ->route('rekap-nilai.index')
            ->with(
                'success',
                "Peringkat kontes “{$kontes->nama_kontes}” berhasil disimpan."
            );
    }
}
