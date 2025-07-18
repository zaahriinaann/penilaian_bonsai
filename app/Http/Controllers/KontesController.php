<?php

namespace App\Http\Controllers;

use App\Models\Kontes;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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

            // --- STATUS AKTIF OTOMATIS -----------------------------
            // 1. Non‑aktifkan semua kontes lama
            Kontes::query()->update(['status' => 0]);

            // 2. Status kontes baru = aktif
            $data['status'] = 1;
            // --------------------------------------------------------

            // Buat slug manual
            $slug = strtolower(preg_replace('/[^a-z0-9\-]/', '', str_replace(' ', '-', $data['nama_kontes'])));
            $data['slug'] = $slug;

            // Alihkan jumlah_peserta ke limit_peserta
            $data['limit_peserta'] = (int) ($data['jumlah_peserta'] ?? 0);

            // Tambahkan https jika perlu
            if (!empty($data['link_gmaps']) && !preg_match('/^https?:\/\//', $data['link_gmaps'])) {
                $data['link_gmaps'] = 'https://' . $data['link_gmaps'];
            }

            // Upload poster, jika ada
            $data['poster_kontes'] = $this->handleImageUpload($request, 'store');

            // Simpan data
            $kontes = Kontes::create($data);

            Session::flash('message', "Kontes {$kontes->nama_kontes} berhasil disimpan & di‑aktifkan.");
            return redirect()->back();
        } catch (\Exception $e) {
            Session::flash('error', "Terdapat kesalahan pada saat menyimpan data, silakan hubungi admin atau coba lagi.");
            return redirect()->back()->withInput();
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $kontes = Kontes::where('slug', $slug)->firstOrFail();
        return view('admin.kontes.show', compact('kontes'));
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
    public function update(Request $request, $slug)
    {
        $kontes = Kontes::where('slug', $slug)->firstOrFail();

        if ($request->has('setActive')) {
            Kontes::where('slug', $slug)->update(['status' => 1]);
            Kontes::where('slug', '!=', $slug)->update(['status' => 0]);
            Session::flash('message', "Kontes {$kontes->nama_kontes} berhasil diaktifkan.");
            return redirect()->back();
        }

        try {
            $data = $request->all();

            // Slug baru dari nama kontes
            $slugBaru = strtolower(preg_replace('/[^a-z0-9\-]/', '', str_replace(' ', '-', $data['nama_kontes'])));
            $data['slug'] = $slugBaru;

            // // Konversi harga tiket
            // $data['harga_tiket_kontes'] = (int) str_replace(['Rp', '.', ','], '', $data['harga_tiket_kontes']);

            // Validasi dan normalkan link GMaps
            if (
                !empty($data['link_gmaps']) &&
                !preg_match('/^https?:\/\//', $data['link_gmaps'])
            ) {
                $data['link_gmaps'] = 'https://' . $data['link_gmaps'];
            }

            // Upload gambar jika ada
            if ($request->hasFile('poster_kontes')) {
                $data['poster_kontes'] = $this->handleImageUpload($request, 'update');
                unset($data['poster_kontes_lama']);
            } else {
                unset($data['poster_kontes_lama']);
            }

            $kontes->update($data);

            Session::flash('message', "Kontes {$kontes->nama_kontes} berhasil diperbarui.");
            return redirect()->back();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            Session::flash('error', "Terdapat kesalahan saat memperbarui data, silakan hubungi admin atau coba lagi.");
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($slug)
    {
        try {
            $kontes = Kontes::where('slug', $slug)->firstOrFail();

            // Hindari konflik slug dengan menambahkan suffix unik
            $kontes->update([
                'slug' => $kontes->slug . '-deleted-' . uniqid()
            ]);

            $kontes->delete();

            return response()->json([
                'message' => "Kontes {$kontes->nama_kontes} berhasil dihapus."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menghapus data, silakan hubungi admin atau coba lagi.'
            ], 500);
        }
    }

    protected function handleImageUpload($request, $typeInput)
    {
        if (!$request->hasFile('poster_kontes') || !$typeInput) {
            return null;
        }

        $image = $request->file('poster_kontes');
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $destinationPath = public_path('assets/images/kontes');

        // Buat folder jika belum ada
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        if ($typeInput === 'update') {
            $fotoLama = $request->input('poster_kontes_lama');
            $oldImagePath = $destinationPath . '/' . $fotoLama;

            if (!empty($fotoLama) && file_exists($oldImagePath) && is_file($oldImagePath)) {
                unlink($oldImagePath);
            }

            unset($request['poster_kontes_lama']);
        }

        // Pindahkan file baru
        $image->move($destinationPath, $imageName);
        return $imageName;
    }
}
