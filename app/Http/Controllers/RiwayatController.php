<?php

namespace App\Http\Controllers;

use App\Models\Kontes;
use App\Models\RekapNilai;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    public function index()
    {
        $kontes = Kontes::where('status', 1)->first();

        $ranking = [];

        if ($kontes) {
            $ranking = RekapNilai::with(['pendaftaran.user', 'pendaftaran.bonsai'])
                ->where('id_kontes', $kontes->id)
                ->orderBy('skor_akhir', 'desc')
                ->get();
        }

        // dd($ranking);

        return view('riwayat.index', compact('ranking'));
    }
}
