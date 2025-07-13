<?php

namespace App\Http\Controllers;

use App\Models\Juri;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Container\Attributes\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

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

    public function store(Request $request)
    {
        try {
            $data = $request->except(['foto', 'sertifikat']);

            $data['no_induk_juri'] = $this->generateNoIndukJuri($request->no_telepon);
            $data['slug'] = Str::slug($request->nama_juri) . '-juri-' . strtolower($data['no_induk_juri']);
            $password = bcrypt($request->username);
            $data['password'] = $password;

            $imageUpload = $this->handleImageUpload($request, 'create');

            if ($request->hasFile('foto')) {
                $data['foto'] = $imageUpload;
            }

            $data['sertifikat'] = $request->file('sertifikat')->store('sertifikat');

            $sertifikat = $this->handleFileSertifikat($request);

            if ($sertifikat) {
                $data['sertifikat'] = $sertifikat;
            } else {
                $data['sertifikat'] = null; // Atau bisa diisi dengan default value jika tidak ada file
            }

            $juri = Juri::create($data);

            User::create([
                'name'       => $data['nama_juri'],
                'username'   => $data['username'],
                'email'      => $data['email'],
                'no_anggota' => $data['no_induk_juri'],
                'password'   => $password,
                'role'       => 'juri',
            ]);

            Session::flash('message', "Juri dengan Nomor Induk: ({$juri->no_induk_juri}) berhasil disimpan.");
            return redirect()->back();
        } catch (\Exception $e) {
            // Log::error('Error saving Juri: ' . $e->getMessage());
            Session::flash('error', "Gagal menyimpan data. Silakan coba lagi." . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    private function handleFileSertifikat(Request $request)
    {
        // validasi file sertifikat dengan ekstensi pdf
        if ($request->hasFile('sertifikat') && $request->file('sertifikat')->isValid()) {
            $sertifikat = $request->file('sertifikat');
            $sertifikatExtension = $sertifikat->getClientOriginalExtension();
            if ($sertifikatExtension !== 'pdf') {
                Session::flash('error', 'File sertifikat harus berupa PDF.');
                return redirect()->back()->withInput();
            }
        }

        if ($request->hasFile('sertifikat')) {
            $sertifikat = $request->file('sertifikat');
            $sertifikatName = $sertifikat->getClientOriginalName();
            $sertifikat->move(public_path('sertifikat'), $sertifikatName);
            return $sertifikatName;
        }

        return null;
    }

    private function generateNoIndukJuri($no_telepon)
    {
        $tahun = date('Y');
        $lastDigits = substr(preg_replace('/\D/', '', $no_telepon), -4);
        $random = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        return "JURI{$tahun}{$lastDigits}{$random}";
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

            // Slug baru
            $slugBaru = Str::slug($data['nama_juri']) . '-juri-' . strtolower(str_replace('JURI', '', $juri->no_induk_juri));
            $data['slug'] = $slugBaru;

            // Handle password
            if (empty($data['password'])) {
                unset($data['password']);
            } else {
                $data['password'] = bcrypt($data['password']);
            }

            // Handle Foto (jika upload baru)
            if ($request->hasFile('foto')) {
                $fotoLama = $request->input('foto_lama');
                if (!empty($fotoLama)) {
                    $oldPath = public_path('images/juri/' . $fotoLama);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }

                $foto = $request->file('foto');
                $fotoName = time() . '_foto.' . $foto->getClientOriginalExtension();
                $foto->move(public_path('images/juri'), $fotoName);
                $data['foto'] = $fotoName;
            } else {
                $data['foto'] = $request->input('foto_lama'); // penting ini
            }

            // Handle Sertifikat (jika upload baru)
            if ($request->hasFile('sertifikat')) {
                $sertifikatLama = $request->input('sertifikat_lama');
                if (!empty($sertifikatLama)) {
                    $oldSertifikatPath = public_path('sertifikat/' . $sertifikatLama);
                    if (file_exists($oldSertifikatPath)) {
                        unlink($oldSertifikatPath);
                    }
                }

                $sertifikat = $request->file('sertifikat');
                $sertifikatName = time() . '_sertifikat.' . $sertifikat->getClientOriginalExtension();
                $sertifikat->move(public_path('sertifikat'), $sertifikatName);
                $data['sertifikat'] = $sertifikatName;
            } else {
                $data['sertifikat'] = $request->input('sertifikat_lama'); // inilah kuncinya
            }

            // Hapus input bantuan yang tidak ada di kolom DB
            unset($data['foto_lama'], $data['sertifikat_lama']);

            // Update juri
            $juri->update($data);


            // Update User
            $user = User::where('username', $juri->username)->first();
            if ($user) {
                $user->update([
                    'name' => $data['nama_juri'],
                    'username' => $data['username'],
                    'email' => $data['email'],
                    'role' => 'juri',
                ]);
            }

            Session::flash('message', "Juri dengan Nomor Induk: ({$juri->no_induk_juri}) berhasil diperbarui.");
            return redirect()->back();
        } catch (\Exception $e) {
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
            $user = User::where('username', $juri->username)->firstOrFail();

            // Hindari konflik slug dengan menambahkan suffix unik
            $juri->update([
                'slug' => $juri->slug . '-deleted-' . uniqid(),
                'username' => $juri->username . '-deleted-' . uniqid()
            ]);

            $user->delete();
            $juri->delete();

            return response()->json([
                'message' => "juri {$juri->nama_juri} berhasil dihapus."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menghapus data, silakan hubungi admin atau coba lagi.'
            ], 500);
        }
    }

    protected function handleImageUpload($request, $typeInput)
    {
        // Jika tidak ada salah satu file, jangan lanjut
        if (!$request->hasFile('foto') || !$typeInput) {
            return null;
        }

        $imageName = null;

        $destinationPath = public_path('images/juri');

        // Buat folder jika belum ada
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }


        if ($typeInput === 'update') {
            $fotoLama = $request->input('foto_lama');

            if (!empty($fotoLama)) {
                $oldImagePath = $destinationPath . '/' . $fotoLama;
                if (file_exists($oldImagePath) && is_file($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
        }

        if ($request->hasFile('foto')) {
            $image = $request->file('foto');
            $imageName = time() . '_foto.' . $image->getClientOriginalExtension();
            $image->move($destinationPath, $imageName);
        }


        return $imageName;
    }
}
