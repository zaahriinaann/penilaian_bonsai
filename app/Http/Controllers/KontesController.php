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
    public function index(Request $request)
    {
        $role = Auth::user()->role;

        $query = Kontes::query();

        // Filter pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('nama_kontes', 'like', "%$search%")
                ->orWhere('tempat_kontes', 'like', "%$search%")
                ->orWhere('tingkat_kontes', 'like', "%$search%");
        }

        $dataRender = $query->orderBy('created_at', 'desc')->paginate(10);

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

            // Sinkronkan jumlah dan limit peserta
            if (isset($data['edit_jumlah_peserta'])) {
                $data['jumlah_peserta'] = (int) $data['edit_jumlah_peserta'];
                $data['limit_peserta'] = $data['jumlah_peserta']; // <- penting
            }

            // Mapping input edit_ ke nama kolom di database
            $data['nama_kontes'] = $data['edit_nama_kontes'] ?? $kontes->nama_kontes;
            $data['tempat_kontes'] = $data['edit_tempat_kontes'] ?? $kontes->tempat_kontes;
            $data['tingkat_kontes'] = $data['edit_tingkat_kontes'] ?? $kontes->tingkat_kontes;
            $data['link_gmaps'] = $data['edit_link_gmaps'] ?? $kontes->link_gmaps;
            $data['tanggal_mulai_kontes'] = $data['edit_tanggal_mulai_kontes'] ?? $kontes->tanggal_mulai_kontes;
            $data['tanggal_selesai_kontes'] = $data['edit_tanggal_selesai_kontes'] ?? $kontes->tanggal_selesai_kontes;

            // Slug baru
            $slugBaru = strtolower(preg_replace('/[^a-z0-9\-]/', '', str_replace(' ', '-', $data['nama_kontes'])));
            $data['slug'] = $slugBaru;

            // Normalisasi link GMaps
            if (!empty($data['link_gmaps']) && !preg_match('/^https?:\/\//', $data['link_gmaps'])) {
                $data['link_gmaps'] = 'https://' . $data['link_gmaps'];
            }

            // Poster baru (jika ada)
            if ($request->hasFile('edit_poster_kontes')) {
                $image = $request->file('edit_poster_kontes');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('assets/images/kontes');

                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }

                $image->move($destinationPath, $imageName);
                $data['poster_kontes'] = $imageName;

                // Hapus poster lama jika ada
                $posterLama = $request->input('poster_lama');
                if ($posterLama) {
                    $oldPath = $destinationPath . '/' . $posterLama;
                    if (file_exists($oldPath)) {
                        @unlink($oldPath);
                    }
                }
            }

            // Hapus key yang tidak ada di DB
            unset($data['edit_nama_kontes'], $data['edit_tempat_kontes'], $data['edit_link_gmaps']);
            unset($data['edit_tingkat_kontes'], $data['edit_tanggal_mulai_kontes'], $data['edit_tanggal_selesai_kontes']);
            unset($data['edit_jumlah_peserta'], $data['edit_poster_kontes'], $data['poster_lama']);
            unset($data['_token'], $data['_method']);

            $kontes->update($data);

            Session::flash('message', "Kontes {$data['nama_kontes']} berhasil diperbarui.");
            return redirect()->back();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            Session::flash('error', "Terdapat kesalahan saat memperbarui data.");
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
        if (!$request->hasFile('poster_kontes')) {
            return null;
        }

        $image = $request->file('poster_kontes');
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $destinationPath = public_path('assets/images/kontes');

        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        // Hapus file lama jika mode update
        if ($typeInput === 'update') {
            $fotoLama = $request->input('poster_lama');
            $oldImagePath = $destinationPath . '/' . $fotoLama;

            if (!empty($fotoLama) && file_exists($oldImagePath) && is_file($oldImagePath)) {
                unlink($oldImagePath);
            }
        }

        $image->move($destinationPath, $imageName);
        return $imageName;
    }
}
