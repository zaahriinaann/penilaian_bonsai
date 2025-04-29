<?php

namespace App\Http\Controllers;

use App\Models\Kontes;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class KontesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Kontes $kontes)
    {
        $role = Auth::user()->role;

        $dataRender = $kontes::all();

        if ($role == 'admin') {
            return view('admin.kontes.index', compact('dataRender'));
        }
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

            $slug = strtolower(str_replace(' ', '-', $data['nama_kontes']));
            // Normalize request
            $slug = $slug; // slug
            $data['slug'] = $slug;
            $price = str_replace(['Rp', '.'], '', $data['harga_tiket_kontes']);
            $data['harga_tiket_kontes'] = (int) $price;
            if (
                strpos($data['link_gmaps'], 'http://') !== 0 &&
                strpos($data['link_gmaps'], 'https://') !== 0
            ) {
                $data['link_gmaps'] = 'https://' . $data['link_gmaps'];
            }
            // End normalize request

            // Store data
            // dd($data);
            $kontes = Kontes::create($data);
            // End store data

            Session::flash('message', "Kontes {$kontes->nama_kontes} berhasil disimpan.");
            return redirect()->back();
        } catch (\Exception $e) {
            Session::flash('error', "Terdapat kesalahan pada saat menyimpan data, silahkan hubungi admin atau coba lain kali.");
            return redirect()->back();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Kontes $kontes)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kontes $kontes)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kontes $kontes)
    {
        try {
            $data = $request->all();

            $slug = strtolower(str_replace(' ', '-', $data['nama_kontes']));
            // Normalize request
            $data['slug'] = $slug;
            $price = str_replace(['Rp', '.'], '', $data['harga_tiket_kontes']);
            $data['harga_tiket_kontes'] = (int) $price;
            if (
                strpos($data['link_gmaps'], 'http://') !== 0 &&
                strpos($data['link_gmaps'], 'https://') !== 0
            ) {
                $data['link_gmaps'] = 'https://' . $data['link_gmaps'];
            }

            $data['id'] = $data['kontes_id'];
            unset($data['kontes_id']);
            // End normalize request


            // dd($data);
            // Update data
            $kontes->update($data);
            // End update data

            Session::flash('message', "Kontes {$kontes->nama_kontes} berhasil diperbarui.");
            return redirect()->back();
        } catch (\Exception $e) {
            Session::flash('error', "Terdapat kesalahan pada saat memperbarui data, silahkan hubungi admin atau coba lain kali. " . $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kontes $kontes)
    {
        \Log::info('Masuk ke destroy: ' . $kontes->id);
        try {
            $kontes->delete(); // Soft delete
            return response()->json(['message' => 'Kontes berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menghapus data.'], 500);
        }
    }
}
