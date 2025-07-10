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
        $totalKontes = Kontes::count();
        $totalJuri = Juri::count();
        $totalPeserta = User::where('role', 'anggota')->count();
        $totalBonsai = Bonsai::count();

        $dataRender = [
            'Kontes' => [$totalKontes, '328E6E'],
            'Juri' => [$totalJuri, '67AE6E'],
            'Peserta' => [$totalPeserta, '90C67C'],
            'Bonsai' => [$totalBonsai, '347433']
        ];
        return view('dashboard.index', compact('dataRender'));
    }
}
