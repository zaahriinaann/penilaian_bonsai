<?php

namespace App\Http\Controllers;

use App\Models\Kontes;
use App\Models\Nilai;
use App\Models\PendaftaranKontes;
use App\Models\User;
use Illuminate\Http\Request;

class NilaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil data nilai lengkap dengan relasi-relasi
        // $dataRender = Nilai::with([
        //     'kontes',
        //     'peserta',
        //     'juri',
        //     'bonsai',
        //     'kriteriaPenilaian',
        //     'pendaftaran'
        // ])->get();

        $dataRender = PendaftaranKontes::with(['user', 'bonsai'])->get();

        return view('juri.nilai.index', compact('dataRender'));
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
    public function show($id)
    {
        $data = PendaftaranKontes::where('id', $id)->first();

        return view('juri.nilai.show', compact('data'));
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
