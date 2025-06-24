<?php

namespace App\Http\Controllers;

use App\Models\Bonsai;
use App\Models\Juri;
use App\Models\Kontes;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AkunController extends Controller
{
    public function index()
    {
        // Mengambil data yang diperlukan untuk ditampilkan di halaman akun sesuai role
        $user = Auth::user();
        // Inisialisasi array data
        if ($user->role === 'peserta') {
            $data['bonsai'] = Bonsai::where('user_id', $user->id)->get();
            $data['kontes'] = Kontes::all();
        } elseif ($user->role === 'juri') {
            $data['juri'] = Juri::where('user_id', $user->id)->first();
            $data['kontes'] = Kontes::all();
        } elseif ($user->role === 'admin') {
            $data['users'] = User::all();
            $data['kontes'] = Kontes::all();
        }
        return view('akun.index');
    }
}
