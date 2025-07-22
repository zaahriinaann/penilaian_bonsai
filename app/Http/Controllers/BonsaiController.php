<?php

namespace App\Http\Controllers;

use App\Models\Bonsai;
use App\Http\Controllers\Controller;
use App\Models\Kontes;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class BonsaiController extends Controller
{
    protected $kontes;


    public function __construct()
    {
        $this->middleware('auth');
        $this->kontes = Kontes::where('status', 1)->first();
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Tangkap keyword pencarian (atau null kalau kosong)
        $search = $request->input('search');

        // Query dasar: ambil Bonsai + relasi User, skip yang soft-deleted
        $query = Bonsai::with('user')
            ->whereNull('deleted_at');

        // Jika ada keyword, tambahkan filter
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_pohon',     'like', "%{$search}%")
                    ->orWhere('no_induk_pohon', 'like', "%{$search}%");
            });
        }

        // Paginate, sertakan query string agar ?search=… tetap menempel di link pagination
        $dataRender = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Data pendukung untuk view
        $user     = User::where('role', 'anggota')->get();
        $province = config('province.obj');
        $kontes   = $this->kontes;

        // Kirim juga variable $search agar input di form bisa ter-isi ulang
        return view('admin.bonsai.index', compact(
            'dataRender',
            'user',
            'province',
            'kontes',
            'search'
        ));
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
            // Validasi input, termasuk kelas dan ukuran_1 numeric
            $request->validate([
                'peserta'           => 'required|exists:users,id',
                'nama_pohon'        => 'required|string',
                'ukuran_1'          => 'required|in:1,2,3',
                'ukuran_2'          => 'required|numeric',
                'format_ukuran'     => 'required|string',
                'masa_pemeliharaan' => 'nullable|string',
                'format_masa'       => 'nullable|string',
                'kelas'             => 'required|string',
                'foto'              => 'nullable|image',
            ]);

            $user = User::findOrFail($request->peserta);

            // Mapping ukuran_1 ke label
            $ukuranMap = [
                1 => 'Small',
                2 => 'Medium',
                3 => 'Large'
            ];
            $ukuranLabel = $ukuranMap[$request->ukuran_1] ?? 'Unknown';

            // Gabungkan ukuran
            $ukuranString = "{$ukuranLabel} ( {$request->ukuran_2} {$request->format_ukuran} )";

            // Menyiapkan data
            $data = [
                'user_id'           => $user->id,
                'nama_pohon'        => $request->nama_pohon,
                'nama_lokal'        => $request->nama_lokal,
                'nama_latin'        => $request->nama_latin,
                'ukuran'            => $ukuranString,
                'ukuran_1'          => $request->ukuran_1,
                'ukuran_2'          => $request->ukuran_2,
                'format_ukuran'     => $request->format_ukuran,
                'masa_pemeliharaan' => $request->masa_pemeliharaan,
                'format_masa'       => $request->format_masa,
                'kelas'             => $request->kelas,
            ];

            // Generate no_induk_pohon
            $data['no_induk_pohon'] = 'BONSAI' . date('Y') . random_int(1000, 9999);

            // Generate slug
            $slugSource = "{$data['nama_pohon']}-{$user->username}-{$ukuranLabel}-ppbi-{$user->cabang}";
            $data['slug'] = Str::slug($slugSource);

            // Handle upload foto
            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $name = time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('assets/images/bonsai'), $name);
                $data['foto'] = $name;
            }

            Bonsai::create($data);

            return back()->with('message', 'Bonsai berhasil disimpan.');
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
            $bonsai = Bonsai::where('slug', $slug)->firstOrFail();

            $request->validate([
                'nama_pohon'        => 'required|string',
                'ukuran_1'          => 'required|in:1,2,3',
                'ukuran_2'          => 'required|numeric',
                'format_ukuran'     => 'required|string',
                'masa_pemeliharaan' => 'nullable|string',
                'format_masa'       => 'nullable|string',
                'kelas'             => 'required|string',
                'foto'              => 'nullable|image',
            ]);

            // Mapping ukuran_1 ke label
            $ukuranMap = [
                1 => 'Small',
                2 => 'Medium',
                3 => 'Large'
            ];
            $ukuranLabel = $ukuranMap[$request->ukuran_1] ?? 'Unknown';

            // Gabungkan ukuran
            $ukuranString = "{$ukuranLabel} ( {$request->ukuran_2} {$request->format_ukuran} )";

            // Persiapkan data update
            $data = [
                'nama_pohon'        => $request->nama_pohon,
                'nama_lokal'        => $request->nama_lokal,
                'nama_latin'        => $request->nama_latin,
                'ukuran'            => $ukuranString,
                'ukuran_1'          => $request->ukuran_1,
                'ukuran_2'          => $request->ukuran_2,
                'format_ukuran'     => $request->format_ukuran,
                'masa_pemeliharaan' => $request->masa_pemeliharaan,
                'format_masa'       => $request->format_masa,
                'kelas'             => $request->kelas,
            ];

            // Generate slug baru (jika diperlukan)
            $data['slug'] = Str::slug("{$data['nama_pohon']}-{$bonsai->user->username}-{$ukuranLabel}-ppbi-{$data['kelas']}");

            // Handle foto baru
            if ($request->hasFile('foto')) {
                // Hapus foto lama jika ada
                $old = public_path('assets/images/bonsai/' . $bonsai->foto);
                if (file_exists($old)) unlink($old);

                $file = $request->file('foto');
                $name = time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('assets/images/bonsai'), $name);
                $data['foto'] = $name;
            }

            $bonsai->update($data);

            Session::flash('message', 'Bonsai berhasil diperbarui.');
            return back();
        } catch (\Exception $e) {
            Session::flash('error', 'Gagal memperbarui: ' . $e->getMessage());
            return back()->withInput();
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
            $destinationPath = public_path('assets/images/bonsai');

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
