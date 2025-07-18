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
    public function index(Bonsai $bonsai)
    {
        $dataRender = $bonsai::all();
        $user = User::where('role', 'anggota')->get();
        $province = config('province.obj');
        $kontes = $this->kontes;
        return view('admin.bonsai.index', compact('dataRender', 'province', 'user', 'kontes'));
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

            $user = User::where('id', $data['peserta'])->firstOrFail();

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
            $slugSource = "{$data['nama_pohon']}-{$user['username']}-{$ukuranLabel}-ppbi-{$user['cabang']}";
            $data['slug'] = Str::slug($slugSource, '-');

            // Ganti nilai null dengan '-'
            $data = Arr::map($data, fn($value) => $value ?? '-');

            $data['foto'] = $this->handleImageUpload($request, 'store');

            // dd($data);
            $data = [
                'user_id' => $data['peserta'],
                'slug' => $data['slug'],
                'nama_pohon' => $data['nama_pohon'],
                'nama_lokal' => $data['nama_lokal'],
                'nama_latin' => $data['nama_latin'],
                'ukuran' => $data['ukuran'],
                'ukuran_1' => $data['ukuran_1'],
                'ukuran_2' => $data['ukuran_2'],
                'format_ukuran' => $data['format_ukuran'],
                'no_induk_pohon' => $data['no_induk_pohon'],
                'masa_pemeliharaan' => $data['masa_pemeliharaan'],
                'format_masa' => $data['format_masa'],
                'kelas' => $this->kontes->tingkat_kontes,
                'foto' => $data['foto'],
            ];

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

            // dd($request->all());
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
            $data['foto'] = $this->handleImageUpload($request, 'update');
            $data = [
                'slug' => $data['slug'],
                'nama_pohon' => $data['nama_pohon'],
                'nama_lokal' => $data['nama_lokal'],
                'nama_latin' => $data['nama_latin'],
                'ukuran' => $data['ukuran'],
                'ukuran_1' => $data['ukuran_1'],
                'ukuran_2' => $data['ukuran_2'],
                'format_ukuran' => $data['format_ukuran'],
                'masa_pemeliharaan' => $data['masa_pemeliharaan'],
                'format_masa' => $data['format_masa'],
                'kelas' => $this->kontes->tingkat_kontes,
                'foto' => $data['foto'],
            ];

            // dd($data);
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
