<?php

namespace App\Http\Controllers;

use App\Models\Penilaian;
use App\Http\Controllers\Controller;
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

        $isEmpty = false;
        if ($penilaians->isEmpty()) {
            $isEmpty = true;
            return view('admin.penilaian.index', [
                'kategori' => [],
                'himpunan' => [],
                'penilaians' => [],
                'isEmpty' => $isEmpty,
            ]);
        }

        // [Kriteria => [Sub_kriteria1, Sub_kriteria2]]
        $kategori = [];

        // [slug_sub_kriteria => [A => ['min'=>x, 'max'=>y], ...]]
        $penilaianGrouped = [];

        // [A => [min, max], ...] – hanya satu contoh range himpunan untuk form batas input
        $himpunanRange = [];

        foreach ($penilaians as $item) {
            $kategori[$item->kriteria][] = $item->sub_kriteria;

            $slug = Str::slug($item->sub_kriteria, '_');

            $penilaianGrouped[$slug][$item->himpunan] = [
                'min' => $item->min,
                'max' => $item->max
            ];
            // Catat range global (semesta pembicaraan per huruf)
            $himpunanRange[$item->himpunan] = [$item->min, $item->max];
        }

        // Unikkan sub-kriteria per kriteria
        foreach ($kategori as &$subs) {
            $subs = array_unique($subs);
        }

        // dd($kategori, $penilaianGrouped, $himpunanRange);
        return view('admin.penilaian.index', [
            'kategori' => $kategori,
            'himpunan' => $himpunanRange,
            'penilaians' => $penilaianGrouped,
            'isEmpty' => $isEmpty,
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
        // Proses penilaian reguler (dari form matrix input)
        foreach ($request->all() as $kriteria => $himpunanSet) {
            // Abaikan input yang bukan array (seperti _token)
            if (!is_array($himpunanSet)) continue;

            foreach ($himpunanSet as $huruf => $nilai) {
                if (!empty($nilai['min']) && !empty($nilai['max'])) {
                    Penilaian::create([
                        'kriteria' => $kriteria,
                        'himpunan' => $huruf,
                        'min' => $nilai['min'],
                        'max' => $nilai['max'],
                    ]);
                }
            }
        }

        // Jika menambahkan kriteria dan sub-kriteria baru
        if ($request->has('add_kriteria')) {
            $kriteria = $request->input('kriteria');
            $subKriteria = $request->input('sub_kriteria');

            $hurufList = ['a', 'b', 'c', 'd'];

            foreach ($hurufList as $huruf) {
                $himpunan = $request->input("himpunan_$huruf");
                $min = $request->input("min_$huruf");
                $max = $request->input("max_$huruf");

                if (!empty($himpunan) && !empty($min) && !empty($max)) {
                    Penilaian::create([
                        'kriteria' => $kriteria,
                        'sub_kriteria' => $subKriteria,
                        'himpunan' => $himpunan,
                        'min' => $min,
                        'max' => $max,
                    ]);
                }
            }
        }

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
    public function destroy(Penilaian $penilaian)
    {
        //
    }
}
