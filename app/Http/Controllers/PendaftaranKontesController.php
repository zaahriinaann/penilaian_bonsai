<?php

namespace App\Http\Controllers;

use App\Models\PendaftaranKontes;
use App\Http\Controllers\Controller;
use App\Models\Bonsai;
use App\Models\Kontes;
use App\Models\User;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;

class PendaftaranKontesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $kontes;

    public function __construct()
    {
        $this->middleware('auth');
        $this->kontes = Kontes::where('status', 1)->first();
    }

    public function index()
    {
        $peserta = User::where('role', 'anggota')->get();
        $pendaftaran = PendaftaranKontes::all();

        return view('admin.pendaftaran.index', compact('peserta', 'pendaftaran'));
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
                'user_id' => 'required',
                'bonsai_id' => 'required',
                'kelas' => 'required',
            ]);

            if (!$this->kontes) {
                return redirect()->back()->with('error', 'Tidak ada kontes yang sedang aktif.');
            }

            $data = $request->all();
            $data['kontes_id'] = $this->kontes->id;

            // Cek duplikat pendaftaran
            $exists = PendaftaranKontes::where('kontes_id', $this->kontes->id)
                ->where('user_id', $data['user_id'])
                ->where('bonsai_id', $data['bonsai_id'])
                ->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Peserta dan bonsai ini sudah terdaftar pada kontes.');
            }

            // Tentukan nomor juri dan nomor pendaftaran
            $lastPendaftaran = PendaftaranKontes::where('kontes_id', $this->kontes->id)->latest()->first();
            $lastKelas = PendaftaranKontes::where('kontes_id', $this->kontes->id)
                ->where('kelas', $data['kelas'])
                ->latest()
                ->first();

            if ($lastPendaftaran) {
                $data['nomor_pendaftaran'] = $lastKelas ? $lastKelas->nomor_pendaftaran + 1 : $lastPendaftaran->nomor_pendaftaran + 1;
                $data['nomor_juri'] = $lastKelas ? $lastKelas->nomor_juri + 1 : 1;
            } else {
                $data['nomor_pendaftaran'] = 1;
                $data['nomor_juri'] = 1;
            }

            PendaftaranKontes::create($data);
            return redirect()->back()->with('message', 'Pendaftaran peserta berhasil!');
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
            $pendaftaran = PendaftaranKontes::findOrFail($id);
            $pendaftaran->delete(); // Soft delete\

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
        return response()->json($bonsai);
    }
}
