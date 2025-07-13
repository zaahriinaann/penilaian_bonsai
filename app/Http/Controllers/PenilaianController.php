<?php

namespace App\Http\Controllers;

use App\Models\Penilaian;
use App\Http\Controllers\Controller;
use App\Models\HelperDomain;
use App\Models\HelperHimpunan;
use App\Models\HelperKriteria;
use App\Models\HelperSubKriteria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PenilaianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $penilaians = Penilaian::all();
        $kriteria = HelperKriteria::all()->toArray();

        if ($penilaians->isEmpty()) {
            return view('admin.penilaian.index', [
                'kategori' => [],
                'himpunan' => [],
                'penilaians' => [],
                'isEmpty' => true,
                'kriteria' => $kriteria,
            ]);
        }

        $kategori = [];
        $penilaianGrouped = [];
        $himpunanRange = [];

        foreach ($penilaians as $item) {
            $kategori[$item->kriteria][$item->sub_kriteria] = true;

            $slug = Str::slug($item->sub_kriteria, '_');

            $penilaianGrouped[$slug][$item->himpunan] = [
                'min' => $item->min,
                'max' => $item->max,
            ];

            // Only store one representative min/max per himpunan
            if (!isset($himpunanRange[$item->himpunan])) {
                $himpunanRange[$item->himpunan] = [$item->min, $item->max];
            }
        }

        // Flatten kategori to unique sub_kriteria lists
        foreach ($kategori as $key => $subs) {
            $kategori[$key] = array_keys($subs);
        }

        return view('admin.penilaian.index', [
            'kategori' => $kategori,
            'himpunan' => $himpunanRange,
            'penilaians' => $penilaianGrouped,
            'isEmpty' => false,
            'kriteria' => $kriteria,
        ]);
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
        // Tambah data baru
        $kriteriaId = $request->input('kriteria');
        $subKriteria = $request->input('sub_kriteria');
        $himpunanList = $request->input('himpunan', []);
        $minList = $request->input('min', []);
        $maxList = $request->input('max', []);

        // Ambil nama kriteria
        $kriteriaModel = HelperKriteria::findOrFail($kriteriaId);
        $data = [
            'id' => $kriteriaModel->id,
            'kriteria' => $kriteriaModel->kriteria,
            'sub_kriteria' => $subKriteria,
        ];

        // Validasi dan Simpan
        $subKriteriaResult = HelperSubKriteria::ValidateSubKriteria($data);
        $himpunanResult = HelperHimpunan::ValidateHimpunan($subKriteriaResult['data'], $himpunanList);
        $domainResult = HelperDomain::ValidateDomain($himpunanResult['data'], $minList, $maxList);

        // Simpan ke tabel penilaian
        $penilaianData = collect($domainResult['data'])->map(function ($item) {
            return [
                'kriteria' => $item['kriteria'],
                'sub_kriteria' => $item['sub_kriteria'],
                'himpunan' => $item['himpunan'],
                'min' => $item['domain_min'],
                'max' => $item['domain_max'],
                'created_at' => now(),
            ];
        })->toArray();

        Penilaian::insert($penilaianData);

        return redirect()->back()->with('success', 'Data penilaian berhasil disimpan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Penilaian $penilaian)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Penilaian $penilaian)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Penilaian $penilaian)
    {
        $kriteria = $request->input('kategori');
        $subKriteria = $request->input('sub_kriteria');
        $slug = $request->input('slug');
        $dataUpdate = $request->input($slug, []);

        $penilaianList = DB::table('penilaian')
            ->join('helper_sub_kriteria', function ($join) {
                $join->on('penilaian.kriteria', '=', 'helper_sub_kriteria.kriteria')
                    ->on('penilaian.sub_kriteria', '=', 'helper_sub_kriteria.sub_kriteria');
            })
            ->join('helper_himpunan', function ($join) {
                $join->on('helper_sub_kriteria.id_sub_kriteria', '=', 'helper_himpunan.id_sub_kriteria')
                    ->on('penilaian.himpunan', '=', 'helper_himpunan.himpunan');
            })
            ->where('penilaian.kriteria', $kriteria)
            ->where('penilaian.sub_kriteria', $subKriteria)
            ->select(
                'penilaian.*',
                'helper_sub_kriteria.id_kriteria',
                'helper_sub_kriteria.id_sub_kriteria',
                'helper_himpunan.id_himpunan'
            )
            ->get();

        if ($penilaianList->isEmpty()) {
            return redirect()->back()->with('error', 'Data penilaian tidak ditemukan!');
        }

        foreach ($penilaianList as $item) {
            DB::table('penilaian')
                ->where('id', $item->id)
                ->update([
                    'min' => $dataUpdate[$item->himpunan]['min'] ?? null,
                    'max' => $dataUpdate[$item->himpunan]['max'] ?? null,
                ]);
        }

        // Perbaiki struktur array agar sesuai dengan kebutuhan ValidateDomain
        $formattedData = $penilaianList->map(function ($item) {
            return [
                'id_kriteria'     => $item->id_kriteria,
                'kriteria'        => $item->kriteria,
                'id_sub_kriteria' => $item->id_sub_kriteria,
                'sub_kriteria'    => $item->sub_kriteria,
                'id_himpunan'     => $item->id_himpunan,
                'himpunan'        => $item->himpunan,
            ];
        })->toArray();

        HelperDomain::ValidateDomain(
            $formattedData,
            collect($dataUpdate)->pluck('min')->values()->toArray(),
            collect($dataUpdate)->pluck('max')->values()->toArray()
        );

        return redirect()->back()->with('success', 'Data penilaian berhasil diperbarui!');
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Penilaian $penilaian)
    {
        $kriteria = $request->input('kriteria');
        $subKriteria = $request->input('sub_kriteria');
        $himpunan = $request->input('himpunan');

        // Query dasar
        $query = $penilaian->newQuery();

        if ($himpunan && $subKriteria) {
            $query->where('himpunan', $himpunan)->where('sub_kriteria', $subKriteria);
        } elseif ($subKriteria) {
            $query->where('sub_kriteria', $subKriteria);
        } elseif ($kriteria) {
            $query->where('kriteria', $kriteria);
        } else {
            return redirect()->back()->with('error', 'Parameter penghapusan tidak lengkap!');
        }

        $deletedCount = $query->delete();

        if ($deletedCount > 0) {
            return redirect()->back()->with('success', 'Data penilaian berhasil dihapus!');
        } else {
            return redirect()->back()->with('warning', 'Tidak ada data yang dihapus.');
        }
    }
}
