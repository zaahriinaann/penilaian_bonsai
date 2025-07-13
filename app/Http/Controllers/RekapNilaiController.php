<?php

namespace App\Http\Controllers;

use App\Models\RekapNilai;
use Illuminate\Http\Request;

class RekapNilaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('rekap_nilai.index');
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
    public function show(RekapNilai $rekapNilai)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RekapNilai $rekapNilai)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RekapNilai $rekapNilai)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RekapNilai $rekapNilai)
    {
        //
    }
}
