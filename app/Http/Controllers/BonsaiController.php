<?php

namespace App\Http\Controllers;

use App\Models\Bonsai;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class BonsaiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Bonsai $bonsai)
    {
        $role = Auth::user()->role;

        $dataRender = $bonsai::all();

        // get only pemilik
        $pemilik = $bonsai::select('pemilik', 'no_anggota', 'cabang')->get();

        if (isset($pemilik)) {
            $user = User::where('role', 'anggota')->get();
            $pemilik = $user->map(function ($item) {
                return (object)[
                    'pemilik' => $item->name,
                    'no_anggota' => $item->no_anggota,
                    'cabang' => $item->cabang,
                ];
            });
        }

        // get only pemilik unique
        $pemilik = $pemilik->unique(function ($item) {
            return $item->pemilik . '-' . $item->no_anggota . '-' . $item->cabang;
        });

        $province = config('province.obj');
        // dd($province);
        return view('admin.bonsai.index', compact('dataRender', 'pemilik', 'province'));
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

            if ($request->has('cabang2')) {
                $data['cabang'] = $data['cabang2'];
                unset($data['cabang2']);
            }

            if ($request->has('cabang-input')) {
                $data['cabang'] = $data['cabang-input'];
                unset($data['cabang-input']);
            }

            // Gabungkan masa pemeliharaan
            $data['masa_pemeliharaan'] = "{$data['masa_pemeliharaan']} {$data['format_masa']}";

            // Mapping ukuran_1 ke label
            $ukuranMap = [
                1 => 'Small',
                2 => 'Medium',
                3 => 'Large'
            ];
            $ukuranLabel = $ukuranMap[$data['ukuran_1']] ?? 'Unknown';

            // Gabungkan ukuran
            $data['ukuran'] = "{$ukuranLabel} ( {$data['ukuran_2']} {$data['format_ukuran']} )";

            // Generate no_induk_pohon
            $data['no_induk_pohon'] = 'BONSAI' . date('Y') . random_int(1000, 9999);

            // Generate slug
            $slugSource = "{$data['nama_pohon']}-{$data['pemilik']}-{$ukuranLabel}-ppbi-{$data['cabang']}";
            $data['slug'] = Str::slug($slugSource, '-');

            // Ganti nilai null dengan '-'
            $data = Arr::map($data, fn($value) => $value ?? '-');
            $data['tingkatan'] = 'madya';
            // Buat akun user jika belum pernah daftar
            if ($request->has('pernahDaftar')) {
                unset($data['pernahDaftar']);
                try {
                    $existingUser = User::where('no_anggota', $data['no_anggota'])->first();
                    if (!$existingUser) {
                        User::create([
                            'name' => $data['pemilik'],
                            'username' => $data['pemilik'],
                            'password' => bcrypt($data['no_anggota']),
                            'role' => 'anggota',
                            'no_anggota' => $data['no_anggota'],
                            'cabang' => $data['cabang'],
                        ]);
                    }
                } catch (\Exception $e) {
                    return back()->with('error', 'Gagal membuat akun pengguna: ' . $e->getMessage());
                }
            }

            $data['foto'] = $this->handleImageUpload($request, 'store');
            // dd($data);
            // Simpan data bonsai
            $bonsai = Bonsai::create($data);

            return back()->with('message', "Bonsai {$bonsai->nama_pohon} berhasil disimpan.");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Bonsai $bonsai)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bonsai $bonsai)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $slug)
    {
        try {
            // Ambil data bonsai berdasarkan slug
            $bonsai = Bonsai::where('slug', $slug)->firstOrFail();
            $data = $request->all();

            // Gabungkan masa pemeliharaan
            $data['masa_pemeliharaan'] = $data['masa_pemeliharaan'] . ' ' . $data['format_masa'];

            // Mapping ukuran
            $ukuranMap = [
                1 => 'Small',
                2 => 'Medium',
                3 => 'Large',
            ];
            $ukuranLabel = $ukuranMap[$data['ukuran_1']] ?? 'Unknown';
            $data['ukuran'] = "{$ukuranLabel} ( {$data['ukuran_2']} {$data['format_ukuran']} )";

            // Slug baru (dibersihkan)
            $slugBaru = strtolower(str_replace(' ', '-', $data['nama_pohon'] . '-' . $bonsai->pemilik . '-' . $ukuranLabel . '-ppbi-' . $bonsai->cabang));
            $data['slug'] = preg_replace('/[^a-z0-9\-]/', '', $slugBaru);

            // Tetapkan ulang data yang tidak boleh berubah
            $data['pemilik'] = $bonsai->pemilik;
            $data['cabang'] = $bonsai->cabang;
            $data['no_anggota'] = $bonsai->no_anggota;
            $data['tingkatan'] = 'madya';

            $data['foto'] = $this->handleImageUpload($request, 'update');
            // dd($data);
            unset($data['foto_lama']);
            // Update
            $bonsai->update($data);

            Session::flash('message', "Bonsai {$bonsai->nama_pohon} berhasil diperbarui.");
            return redirect()->back();
        } catch (\Exception $e) {
            Session::flash('error', 'Terjadi kesalahan saat memperbarui data bonsai: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($slug)
    {
        try {
            $bonsai = Bonsai::where('slug', $slug)->firstOrFail();

            // Hindari konflik slug dengan menambahkan suffix unik
            $bonsai->update([
                'slug' => $bonsai->slug . '-deleted-' . uniqid()
            ]);

            $bonsai->delete();

            return response()->json([
                'message' => "Kontes {$bonsai->nama_kontes} berhasil dihapus."
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
            $destinationPath = public_path('images/bonsai');

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
