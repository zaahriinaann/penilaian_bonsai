<?php

namespace App\Http\Controllers;

use App\Models\PendaftaranKontes;
use App\Models\Bonsai;
use App\Models\Kontes;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PendaftaranKontesController extends Controller
{
    protected $kontes;

    public function __construct()
    {
        $this->middleware('auth');
        $this->kontes = Kontes::where('status', 1)->first();
    }

    public function index(Request $request)
    {
        // Kalau tidak ada kontes aktif → kirim data kosong tapi tetap paginator
        if (!$this->kontes) {
            $peserta = collect();
            $pendaftaran = new LengthAwarePaginator([], 0, 10, 1, [
                'path' => request()->url(),
                'query' => request()->query(),
            ]);

            return view('admin.pendaftaran.index', compact('peserta', 'pendaftaran'))
                ->with('kontesKosong', true);
        }

        $kelasKontes = $this->kontes->tingkat_kontes;

        // Ambil daftar peserta sesuai kelas
        $peserta = User::where('role', 'anggota')
            ->whereHas('bonsai', function ($query) use ($kelasKontes) {
                $query->where('kelas', $kelasKontes);
            })
            ->with(['bonsai' => function ($query) use ($kelasKontes) {
                $query->where('kelas', $kelasKontes);
            }])
            ->get();

        $search = $request->input('search');

        $pendaftaranQuery = PendaftaranKontes::with(['bonsai', 'user'])
            ->where('kontes_id', $this->kontes->id);

        if ($search) {
            $pendaftaranQuery->where(function ($query) use ($search) {
                $query->where('nomor_pendaftaran', 'like', "%{$search}%")
                    ->orWhere('nomor_juri', 'like', "%{$search}%")
                    ->orWhereHas('bonsai', function ($q) use ($search) {
                        $q->where('nama_pohon', 'like', "%{$search}%")
                            ->orWhere('no_induk_pohon', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $pendaftaran = $pendaftaranQuery
            ->orderBy('nomor_pendaftaran')
            ->paginate(10)
            ->withQueryString();

        return view('admin.pendaftaran.index', compact('peserta', 'pendaftaran'))
            ->with('kontesKosong', false);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required',
                'bonsai_id' => 'required',
            ]);

            if (!$this->kontes) {
                return redirect()->back()->with('error', 'Tidak ada kontes yang sedang aktif.');
            }

            $data = $request->all();
            $data['kontes_id'] = $this->kontes->id;
            $data['kelas'] = $this->kontes->tingkat_kontes;

            // Cek duplikat
            $exists = PendaftaranKontes::where('kontes_id', $this->kontes->id)
                ->where('user_id', $data['user_id'])
                ->where('bonsai_id', $data['bonsai_id'])
                ->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Peserta dan bonsai ini sudah terdaftar pada kontes.');
            }

            // Ambil data terakhir
            $lastPendaftaran = PendaftaranKontes::where('kontes_id', $this->kontes->id)
                ->orderByDesc('id')
                ->first();

            if ($lastPendaftaran) {
                $lastNoPendaftaran = (int) filter_var($lastPendaftaran->nomor_pendaftaran, FILTER_SANITIZE_NUMBER_INT);
                $lastNoJuri = (int) filter_var($lastPendaftaran->nomor_juri, FILTER_SANITIZE_NUMBER_INT);

                $data['nomor_pendaftaran'] = 'P' . ($lastNoPendaftaran + 1);
                $data['nomor_juri'] = 'J' . ($lastNoJuri + 1);
            } else {
                $data['nomor_pendaftaran'] = 'P1';
                $data['nomor_juri'] = 'J1';
            }

            PendaftaranKontes::create($data);
            return redirect()->back()->with('message', 'Pendaftaran peserta berhasil!');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function show($id)
    {
        $data = PendaftaranKontes::find($id);

        if (!$data) {
            return redirect()->back()->with('error', 'Data pendaftaran tidak ditemukan.');
        }

        return view('admin.pendaftaran.show', compact('data'));
    }

    public function destroy($id)
    {
        try {
            $pendaftaran = PendaftaranKontes::find($id);

            if (!$pendaftaran) {
                return response()->json([
                    'message' => 'Data tidak ditemukan.'
                ], 404);
            }

            $pendaftaran->delete();

            return response()->json([
                'message' => "Data pendaftaran berhasil dihapus."
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

        return response()->json($bonsai);
    }
}
