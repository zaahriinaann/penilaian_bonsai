<?php

namespace App\Http\Controllers;

use App\Models\Nilai;
use Illuminate\Http\Request;

class NilaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //sesuaikan dengan index.blade.php dari juri/nilai/index.blade.php
        //jika ingin menampilkan data, bisa menggunakan model Nilai
        $nilais = Nilai::all();
        return view('juri.nilai.index', compact('nilais'));
        // Jika tidak ada data yang ingin ditampilkan, cukup kembalikan view
        // dengan view juri.nilai.index
        // Pastikan view tersebut sudah ada di resources/views/juri/nilai/index.blade.php
        return view('juri.nilai.index');
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
    public function show(Nilai $nilai)
    {
        //
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
    public function update(Request $request, Nilai $nilai)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Nilai $nilai)
    {
        //
    }
}
