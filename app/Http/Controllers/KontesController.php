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

            $tingkatLabel = $data['tingkat_kontes'] ?? 'Unknown';
            $data['tingkat_kontes'] = $tingkatLabel;

            // Buat slug manual
            $slug = strtolower(str_replace(' ', '-', $data['nama_kontes']));
            $slug = preg_replace('/[^a-z0-9\-]/', '', $slug); // hanya huruf, angka, dan tanda -
            $data['slug'] = $slug;

            // Bersihkan dan konversi harga tiket
            $price = str_replace(['Rp', '.', ','], '', $data['harga_tiket_kontes']);
            $data['harga_tiket_kontes'] = (int) $price;

            // Alihkan jumlah_peserta ke limit_peserta (jika memang ada kolom ini)
            $data['limit_peserta'] = (int) $data['jumlah_peserta'];

            // Tambahkan https jika tidak ada
            if (
                !empty($data['link_gmaps']) &&
                strpos($data['link_gmaps'], 'http://') !== 0 &&
                strpos($data['link_gmaps'], 'https://') !== 0
            ) {
                $data['link_gmaps'] = 'https://' . $data['link_gmaps'];
            }

            // Handle image dengan function handleImageUpload
            $data['poster_kontes'] = $this->handleImageUpload($request, 'store');

            /*  dd($data); */
            // Simpan data
            $kontes = Kontes::create($data);

            Session::flash('message', "Kontes {$kontes->nama_kontes} berhasil disimpan.");
            return redirect()->back();
        } catch (\Exception $e) {
            Session::flash('error', "Terdapat kesalahan pada saat menyimpan data, silakan hubungi admin atau coba lagi." . $e->getMessage());
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
        try {
            $kontes = Kontes::where('slug', $slug)->firstOrFail();

            $data = $request->all();

            // Buat slug manual dan aman
            $slugBaru = strtolower(str_replace(' ', '-', $data['nama_kontes']));
            $slugBaru = preg_replace('/[^a-z0-9\-]/', '', $slugBaru);
            $data['slug'] = $slugBaru;

            // Konversi harga tiket
            $price = str_replace(['Rp', '.', ','], '', $data['harga_tiket_kontes']);
            $data['harga_tiket_kontes'] = (int) $price;

            // Periksa link GMaps jika diisi
            if (
                !empty($data['link_gmaps']) &&
                strpos($data['link_gmaps'], 'http://') !== 0 &&
                strpos($data['link_gmaps'], 'https://') !== 0
            ) {
                $data['link_gmaps'] = 'https://' . $data['link_gmaps'];
            }

            $data['poster_kontes'] = $this->handleImageUpload($request, 'update');
            unset($data['poster_kontes_lama']);
            $kontes->update($data);

            Session::flash('message', "Kontes {$kontes->nama_kontes} berhasil diperbarui.");
            return redirect()->route('kontes.index');
        } catch (\Exception $e) {
            Session::flash('error', "Terdapat kesalahan saat memperbarui data. Silakan coba lagi atau hubungi admin." . $e->getMessage());
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
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    protected function handleImageUpload($request, $typeInput)
    {
        if ($request->hasFile('poster_kontes') && $typeInput) {
            $image = $request->file('poster_kontes');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('images/kontes');

            // Buat folder jika belum ada (opsional)
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            if ($typeInput === 'store') {
                $image->move($destinationPath, $imageName);
                return $imageName;
            } elseif ($typeInput === 'update') {
                $fotoLama = $request->input('poster_kontes_lama');
                $oldImagePath = $destinationPath . '/' . $fotoLama;

                if (!empty($fotoLama) && file_exists($oldImagePath) && is_file($oldImagePath)) {
                    unlink($oldImagePath);
                }

                $image->move($destinationPath, $imageName);
                return $imageName;
            }
        }
        return null;
    }
}
