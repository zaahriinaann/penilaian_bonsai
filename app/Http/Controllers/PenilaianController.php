<?php

namespace App\Http\Controllers;

use App\Models\Penilaian;
use App\Http\Controllers\Controller;
use App\Models\HelperKriteria;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PenilaianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $penilaians = Penilaian::all();
        $helperKriteria = HelperKriteria::all()->map(function ($item) {
            return [
                'kriteria'      => $item->kriteria,
                'sub_kriteria'  => $item->sub_kriteria,
                'himpunan'      => $item->himpunan,
                'min'           => $item->min,
                'max'           => $item->max,
            ];
        });

        $kriteria = HelperKriteria::distinct('kriteria')->pluck('kriteria')->toArray();

        if ($penilaians->isEmpty()) {
            return view('admin.penilaian.index', [
                'kategori'       => [],
                'himpunan'       => [],
                'penilaians'     => [],
                'isEmpty'        => true,
                'helperKriteria' => $helperKriteria,
                'kriteria'       => $kriteria,
            ]);
        }

        $kategori         = [];
        $penilaianGrouped = [];
        $himpunanRange    = [];

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
            'kategori'       => $kategori,
            'himpunan'       => $himpunanRange,
            'penilaians'     => $penilaianGrouped,
            'isEmpty'        => false,
            'helperKriteria' => $helperKriteria,
            'kriteria'       => $kriteria,
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
    public function store(Request $request, HelperKriteria $helperKriteria)
    {
        $data = $request->only(['kriteria', 'sub_kriteria']);
        $helperData = $helperKriteria->where('kriteria', $data['kriteria'])->get(['himpunan', 'min', 'max']);

        $penilaianData = $helperData->map(function ($item) use ($data) {
            return array_merge($data, $item->only(['himpunan', 'min', 'max']));
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
        //
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
