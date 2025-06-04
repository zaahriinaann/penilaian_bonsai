<?php

namespace App\Http\Controllers;

use App\Models\Juri;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class JuriController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
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

            // Nomor Induk Juri
            $tahun = date('Y');
            $data['no_induk_juri'] = "JURI{$tahun}" . substr(preg_replace('/\D/', '', $data['no_telepon']), -4);
            // Buat slug dasar dari nama juri
            $slug = strtolower(trim($data['nama_juri']));
            $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);  // Hapus karakter yang tidak diinginkan
            $slug = preg_replace('/[\s\-]+/', '-', $slug);      // Ganti spasi dan tanda - lebih dari satu dengan -
            $slug = trim($slug, '-');                            // Hapus - di awal atau akhir

            // Gabungkan dengan 'juri' dan nomor induk juri
            $slug .= '-juri-' . strtolower($data['no_induk_juri']);
            // Assign slug yang telah dibuat ke data
            $data['slug'] = $slug;

            // Password default = no induk
            $data['password'] = bcrypt($data['password']);

            $data['foto'] = $this->handleImageUpload($request, 'store');

            // Simpan ke DB
            $juri = Juri::create($data);

            // simpan ke db user
            $user = User::create([
                'name' => $data['nama_juri'],
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => 'juri',
            ]);

            // Berikan pesan sukses setelah menyimpan data
            Session::flash('message', "Juri dengan Nomor Induk: ({$juri->no_induk_juri}) berhasil disimpan.");
            return redirect()->back();
        } catch (\Exception $e) {
            // Tangani error jika terjadi kesalahan saat menyimpan
            Session::flash('error', "Gagal menyimpan data: " . $e->getMessage());
            return redirect()->back()->withInput();
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
            // Ambil data juri yang akan diupdate
            $juri = Juri::where('slug', $slug)->firstOrFail();

            // Ambil data dari form
            $data = $request->all();

            // Buat slug dasar dari nama juri
            $slug = strtolower(trim($data['nama_juri']));
            $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);  // Hapus karakter yang tidak diinginkan
            $slug = preg_replace('/[\s\-]+/', '-', $slug);      // Ganti spasi dan tanda - lebih dari satu dengan -
            $slug = trim($slug, '-');                            // Hapus - di awal atau akhir

            // Gabungkan dengan 'juri' dan nomor induk juri
            $slug .= '-juri-' . strtolower(str_replace('JURI', '', $juri['no_induk_juri']));

            // Assign slug yang telah dibuat ke data
            $data['slug'] = $slug;

            // Update Nomor Induk Juri
            // $tahun = date('Y');
            // $data['no_induk_juri'] = "JURI{$tahun}" . substr(preg_replace('/\D/', '', $data['no_telepon']), -4);

            // Jika password kosong, biarkan password sebelumnya
            if (empty($data['password'])) {
                unset($data['password']);  // Jangan update password jika tidak diubah
            } else {
                // Jika password ada, hash dan update
                $data['password'] = bcrypt($data['password']);
            }

            // dd($data);
            $data['foto'] = $this->handleImageUpload($request, 'update');
            unset($data['foto_lama']);

            // Update data juri di database
            $juri->update($data);

            // Berikan pesan sukses setelah update
            Session::flash('message', "Juri dengan Nomor Induk: ({$juri->no_induk_juri}) berhasil diperbarui.");
            return redirect()->back();
        } catch (\Exception $e) {
            // Tangani error jika terjadi kesalahan saat menyimpan
            Session::flash('error', "Gagal memperbarui data: " . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($slug)
    {
        try {
            $juri = Juri::where('slug', $slug)->firstOrFail();

            // Hindari konflik slug dengan menambahkan suffix unik
            $juri->update([
                'slug' => $juri->slug . '-deleted-' . uniqid()
            ]);

            $juri->delete();

            return response()->json([
                'message' => "juri {$juri->nama_juri} berhasil dihapus."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    protected function handleImageUpload($request, $typeInput)
    {
        if ($request->hasFile('foto') && $typeInput) {
            $image = $request->file('foto');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('images/juri');

            // Buat folder jika belum ada (opsional)
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            if ($typeInput === 'store') {
                $image->move($destinationPath, $imageName);
                return $imageName;
            } elseif ($typeInput === 'update') {
                $fotoLama = $request->input('foto_lama');
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
