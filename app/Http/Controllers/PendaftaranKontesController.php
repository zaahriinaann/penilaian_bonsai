<?php

namespace App\Http\Controllers;

use App\Models\PendaftaranKontes;
use App\Http\Controllers\Controller;
use App\Models\Bonsai;
use App\Models\Kontes;
use App\Models\User;
use Illuminate\Http\Request;

class PendaftaranKontesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kontes = Kontes::all();
        $peserta = User::where('role', 'anggota')->get();

        $pendaftaran = PendaftaranKontes::all();
        return view('admin.pendaftaran.index', compact('kontes', 'peserta', 'pendaftaran'));
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
            $request->validate([
                'kontes_id' => 'required',
                'user_id' => 'required',
                'bonsai_id' => 'required',
                'kelas' => 'required',
            ]);

            $data = $request->all();

            $pendaftaran = PendaftaranKontes::where('kontes_id', $data['kontes_id'])->latest()->first();

            if ($pendaftaran) {

                $kelas = PendaftaranKontes::where('kontes_id', $data['kontes_id'])->where('kelas', $data['kelas'])->latest()->first();

                if ($kelas) {
                    $data['nomor_juri'] = $kelas->nomor_juri + 1;
                    $data['nomor_pendaftaran'] = $kelas->nomor_pendaftaran + 1;
                } else {
                    $data['nomor_juri'] = 1;
                    $data['nomor_pendaftaran'] = $pendaftaran->nomor_pendaftaran + 1;
                }
            } else {
                $data['nomor_pendaftaran'] = 1;
                $data['nomor_juri'] = 1;
            }

            // dd($data);
            PendaftaranKontes::create($data);
            return redirect()->back();
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PendaftaranKontes $pendaftaranKontes)
    {
        dd($pendaftaranKontes);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PendaftaranKontes $pendaftaranKontes)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PendaftaranKontes $pendaftaranKontes)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $pendaftaran = PendaftaranKontes::where('id', $id)->firstOrFail();

            $pendaftaran->update(['deleted_at' => now()]);

            return response()->json([
                'message' => "Kontes {$pendaftaran->nama_kontes} berhasil dihapus."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menghapus data, silakan hubungi admin atau coba lagi.'
            ], 500);
        }
    }

    public function getBonsaiPeserta($id)
    {
        $bonsai = Bonsai::where('user_id', $id)->get();

        // dd($bonsai);
        return $bonsai;
    }
}
