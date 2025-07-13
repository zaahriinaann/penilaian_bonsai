<?php

namespace App\Http\Controllers;

use App\Models\Kontes;
use App\Models\RekapNilai;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    public function index()
    {
        $kontes = Kontes::where('status', 1)->firstOrFail();

        $ranking = RekapNilai::with('bonsai.user')
            ->where('id_kontes', $kontes->id)
            ->orderByDesc('skor_akhir')
            ->get();

        // dd($ranking);

        return view('riwayat.index', compact('ranking'));
    }
}
