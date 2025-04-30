<?php

namespace App\Http\Controllers;

use App\Models\Bonsai;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

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

        // Jika pemilik ada lebih dari 1, ambil salah satu saja
        if ($pemilik->count() > 1) {
            $pemilik = $pemilik->unique('pemilik')->values()->all();
        } else {
            $pemilik = $pemilik->first();
        }

        return view('admin.bonsai.index', compact('dataRender', 'pemilik'));
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

            // Gabungkan masa pemeliharaan
            $data['masa_pemeliharaan'] = $data['masa_pemeliharaan'] . ' ' . $data['format_masa'];

            // Mapping ukuran_1
            $ukuranMap = [
                1 => 'Small',
                2 => 'Medium',
                3 => 'Large',
            ];
            $ukuranLabel = $ukuranMap[$data['ukuran_1']] ?? 'Unknown';

            // Gabungkan ukuran
            $data['ukuran'] = $ukuranLabel . ' ' . $data['ukuran_2'] . ' ' . $data['format_ukuran'];

            // Buat no induk pohon
            $year = date('Y');
            $random = random_int(1000, 9999);
            $data['no_induk_pohon'] = "BONSAI{$year}{$random}";

            // Buat slug manual
            $slug = strtolower(str_replace(' ', '-', $data['nama_pohon'] . '-' . $data['pemilik'] . '-' . $ukuranLabel . '-ppbi-' . $data['cabang']));
            $data['slug'] = preg_replace('/[^a-z0-9\-]/', '', $slug); // hanya huruf kecil, angka, strip

            // Simpan ke database
            // dd($data);
            $bonsai = Bonsai::create($data);

            Session::flash('message', "Bonsai {$bonsai->nama_pohon} berhasil disimpan.");
            return redirect()->back();
        } catch (\Exception $e) {
            // Tangani error
            Session::flash('error', 'Terjadi kesalahan saat menyimpan data bonsai: ' . $e->getMessage());
            return redirect()->back()->withInput();
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
            $data = $request->all();

            // Gabungkan masa pemeliharaan
            $data['masa_pemeliharaan'] = $data['masa_pemeliharaan'] . ' ' . $data['format_masa'];

            // Mapping ukuran_1
            $ukuranMap = [
                1 => 'Small',
                2 => 'Medium',
                3 => 'Large',
            ];
            $ukuranLabel = $ukuranMap[$data['ukuran_1']] ?? 'Unknown';

            // Gabungkan ukuran
            $data['ukuran'] = $ukuranLabel . ' ' . $data['ukuran_2'] . ' ' . $data['format_ukuran'];

            // Update slug (manual)
            $slug = strtolower(str_replace(' ', '-', $data['nama_pohon'] . '-' . $data['pemilik'] . '-' . $ukuranLabel . '-ppbi-' . $data['cabang']));
            $data['slug'] = preg_replace('/[^a-z0-9\-]/', '', $slug); // hanya huruf kecil, angka, strip

            // Update data bonsai
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
    public function destroy(Bonsai $bonsai)
    {
        //
    }
}
