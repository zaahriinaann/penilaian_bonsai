<?php

namespace App\Http\Controllers;

use App\Models\Bonsai;
use App\Models\Juri;
use App\Models\Kontes;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $tahunSekarang = now()->year;
        $tahunRange = range($tahunSekarang - 4, $tahunSekarang);

        $kontesPerTahun = [];
        $pesertaPerTahun = [];
        $bonsaiPerTahun = [];

        foreach ($tahunRange as $tahun) {
            $kontesPerTahun[] = \App\Models\Kontes::whereYear('created_at', $tahun)->count();
            $pesertaPerTahun[] = \App\Models\User::where('role', 'anggota')->whereYear('created_at', $tahun)->count();
            $bonsaiPerTahun[] = \App\Models\Bonsai::whereYear('created_at', $tahun)->count();
        }

        $dataRender = [
            'Kontes' => [\App\Models\Kontes::count(), '00b894'],
            'Juri' => [\App\Models\User::where('role', 'juri')->count(), '0984e3'],
            'Peserta' => [\App\Models\User::where('role', 'anggota')->count(), 'fdcb6e'],
            'Bonsai' => [\App\Models\Bonsai::count(), 'd63031'],
        ];

        return view('dashboard.index', [
            'dataRender' => $dataRender,
            'tahun' => $tahunRange,
            'data_kontes' => $kontesPerTahun,
            'data_peserta' => $pesertaPerTahun,
            'data_bonsai' => $bonsaiPerTahun
        ]);
    }
}
