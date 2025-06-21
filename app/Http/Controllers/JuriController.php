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
            $data['password'] = bcrypt($data['username']);

            // Tangani upload foto & sertifikat sekaligus
            $uploadedFiles = $this->handleImageUpload($request, 'store');

            if ($uploadedFiles) {
                if (isset($uploadedFiles['foto'])) {
                    $data['foto'] = $uploadedFiles['foto'];
                    unset($data['foto_lama']);
                }
                if (isset($uploadedFiles['sertifikat'])) {
                    $data['sertifikat'] = $uploadedFiles['sertifikat'];
                    unset($data['sertifikat_lama']);
                }
            }

            // Simpan ke DB
            $juri = Juri::create($data);

            // simpan ke db user
            $user = User::create([
                'name' => $data['nama_juri'],
                'username' => $data['username'],
                'no_anggota' => $data['no_induk_juri'],
                'email' => $data['email'],
                'password' => $data['username'],
                'role' => 'juri',
            ]);

            // Berikan pesan sukses setelah menyimpan data
            Session::flash('message', "Juri dengan Nomor Induk: ({$juri->no_induk_juri}) berhasil disimpan.");
            return redirect()->back();
        } catch (\Exception $e) {
            // Tangani error jika terjadi kesalahan saat menyimpan
            Session::flash('error', "Gagal menyimpan data, silahkan coba lagi." . $e->getMessage(), 500);
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
                $passwordUser = $data['password'];
            }

            // Upload gambar jika ada
            if ($request->hasFile('foto')) {
                $data['foto'] = $this->handleImageUpload($request, 'update');
                unset($data['foto_lama']);
                unset($data['sertifikat_lama']);
            } else {
                unset($data['foto_lama']);
                unset($data['sertifikat_lama']);
            }

            // Update data juri di database
            $juri->update($data);

            // Find user
            $user = User::where('no_anggota', $juri['no_induk_juri'])->first();
            if ($user) {
                // dd($user);
                $user->update([
                    'name' => $data['nama_juri'],
                    'username' => $data['username'],
                    'password' => $passwordUser,
                    'email' => $data['email'],
                    'role' => 'juri',
                ]);
            }

            // Berikan pesan sukses setelah update
            Session::flash('message', "Juri dengan Nomor Induk: ({$juri->no_induk_juri}) berhasil diperbarui.");
            return redirect()->back();
        } catch (\Exception $e) {
            // Tangani error jika terjadi kesalahan saat menyimpan
            Session::flash('error', "Gagal memperbarui data, silakan hubungi admin atau coba lagi." . $e->getMessage(), 500);
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
        if ((!$request->hasFile('foto') && !$request->hasFile('sertifikat')) || !$typeInput) {
            return null;
        }

        $imageName = null;
        $imageNameSertifikat = null;

        $destinationPath = public_path('images/juri');
        $destinationPathSertifikat = public_path('images/sertifikat');

        // Buat folder jika belum ada
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        if (!file_exists($destinationPathSertifikat)) {
            mkdir($destinationPathSertifikat, 0755, true);
        }

        if ($typeInput === 'update') {
            $fotoLama = $request->input('foto_lama');
            $sertifikatLama = $request->input('sertifikat_lama');

            if (!empty($fotoLama)) {
                $oldImagePath = $destinationPath . '/' . $fotoLama;
                if (file_exists($oldImagePath) && is_file($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            if (!empty($sertifikatLama)) {
                $oldImagePathSertifikat = $destinationPathSertifikat . '/' . $sertifikatLama;
                if (file_exists($oldImagePathSertifikat) && is_file($oldImagePathSertifikat)) {
                    unlink($oldImagePathSertifikat);
                }
            }
        }

        if ($request->hasFile('foto')) {
            $image = $request->file('foto');
            $imageName = time() . '_foto.' . $image->getClientOriginalExtension();
            $image->move($destinationPath, $imageName);
        }

        if ($request->hasFile('sertifikat')) {
            $imageSertifikat = $request->file('sertifikat');
            $imageNameSertifikat = time() . '_sertifikat.' . $imageSertifikat->getClientOriginalExtension();
            $imageSertifikat->move($destinationPathSertifikat, $imageNameSertifikat);
        }

        return [
            'foto' => $imageName,
            'sertifikat' => $imageNameSertifikat,
        ];
    }
}
