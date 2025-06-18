<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class PesertaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dataRender = User::where('role', 'anggota')->get();
        return view('admin.peserta.index', compact('dataRender'));
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

        // Persiapan data
        $data = $request->all();
        $data['name'] = $data['nama'];
        $data['role'] = 'anggota';
        $data['password'] = bcrypt($data['username']); // password = username
        $data['foto'] = $this->handleImageUpload($request, 'store');
        $data['email_verified_at'] = now();

        // Simpan user
        User::create($data);

        return redirect()->route('peserta.index')->with('success', 'Peserta berhasil ditambahkan.');
    }


    /**
     * Display the specified resource.
     */
    public function show()
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $peserta = User::findOrFail($id);

            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'username' => 'required|string|max:255',
                'email' => 'required|email',
                'no_anggota' => 'nullable|string',
                'cabang' => 'nullable|string',
                'no_hp' => 'nullable|string',
                'alamat' => 'nullable|string',
                'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            if ($request->hasFile('foto')) {
                $filename = time() . '.' . $request->foto->extension();
                $request->foto->move(public_path('images/peserta'), $filename);
                $validated['foto'] = $filename;

                // Hapus foto lama kalau ada
                if ($request->foto_lama && file_exists(public_path('images/peserta/' . $request->foto_lama))) {
                    unlink(public_path('images/peserta/' . $request->foto_lama));
                }
            } else {
                $validated['foto'] = $peserta->foto;
            }

            $peserta->update($validated);
            // Berikan pesan sukses setelah update
            Session::flash('message', "peserta dengan Nomor Anggota: ({$peserta->no_anggota}) berhasil diperbarui.");
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
    public function destroy($slug) {}


    protected function handleImageUpload($request, $typeInput)
    {
        if ($request->hasFile('foto') && $typeInput) {
            $image = $request->file('foto');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('images/peserta');

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
