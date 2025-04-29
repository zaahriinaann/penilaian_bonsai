<?php

namespace App\Http\Controllers;

use App\Models\Juri;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class JuriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Juri $juri)
    {
        $role = Auth::user()->role;

        $dataRender = $juri::all();
        return view('admin.juri.index', compact('dataRender'));
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
        try {
            $data = $request->all();
            $slug = strtolower(str_replace(' ', '-', $data['nama_juri']));
            $data['slug'] = $slug;
            $dateNow = substr(str_replace('-', '', date('Y-m-d')), 0, 4);
            $notlp = substr($data['no_telepon'], -4);
            $no_induk_juri = "JURI$dateNow$notlp";
            $data['no_induk_juri'] = $no_induk_juri;
            $juri = Juri::create($data);
            Session::flash('message', "Juri dengan Nomor Induk : ({$juri->no_induk_juri}) berhasil disimpan.");
            return redirect()->back();
        } catch (\Exception $e) {
            Session::flash('error', "Terdapat kesalahan pada saat menyimpan data, silahkan hubungi admin atau coba lain kali. " . $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Juri $juri)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Juri $juri)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $slug)
    {

        try {
            $juri = Juri::where('slug', $slug)->firstOrFail();
            $data = $request->all();
            $slug = strtolower(str_replace(' ', '-', $data['nama_juri']));
            $data['slug'] = $slug;
            $dateNow = substr(str_replace('-', '', date('Y-m-d')), 0, 4);
            $notlp = substr($data['no_telepon'], -4);
            $no_induk_juri = "JURI$dateNow$notlp";
            $data['no_induk_juri'] = $no_induk_juri;
            $juri->update($data);
            Session::flash('message', "Juri dengan Nomor Induk : ({$juri->no_induk_juri}) berhasil diperbarui.");
            return redirect()->back();
        } catch (\Exception $e) {
            Session::flash('error', "Terdapat kesalahan pada saat menyimpan data, silahkan hubungi admin atau coba lain kali. " . $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($slug)
    {
        $juri = Juri::where('slug', $slug)->firstOrFail();
        try {
            $juri->delete();
            return response()->json(['message' => `Juri dengan Nomor Induk : ({$juri->no_induk_juri}) berhasil disimpan.`]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menghapus data.'], 500);
        }
    }
}
