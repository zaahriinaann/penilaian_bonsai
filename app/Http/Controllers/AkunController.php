<?php

namespace App\Http\Controllers;

use App\Models\Bonsai;
use App\Models\Juri;
use App\Models\Kontes;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AkunController extends Controller
{
    /* ========================= INDEX ========================= */
    public function index()
    {
        $user  = Auth::user();
        $data  = compact('user');

        switch ($user->role) {
            case 'peserta':
                $data['bonsai'] = Bonsai::where('user_id', $user->id)->get();
                $data['kontes'] = Kontes::all();
                break;
            case 'juri':
                $data['juri']   = Juri::where('user_id', $user->id)->first();
                $data['kontes'] = Kontes::all();
                break;
            case 'admin':
                $data['users']  = User::all();
                $data['kontes'] = Kontes::all();
                break;
        }

        return view('akun.index', $data);
    }

    /* =============== HELPER: HANDLE IMAGE UPLOAD =============== */
    protected function handleImageUpload(Request $request, User $user): ?string
    {
        if (!$request->hasFile('foto')) {
            return $user->foto;                     // tetap pakai foto lama
        }

        $file         = $request->file('foto');
        $newFileName  = time() . '.' . $file->getClientOriginalExtension();

        // ▸ admin & anggota → peserta  |  juri → juri
        $roleFolder   = in_array($user->role, ['admin', 'anggota']) ? 'peserta' : 'juri';
        $destPath     = public_path("images/{$roleFolder}");

        // buat folder jika belum ada
        if (!is_dir($destPath)) {
            mkdir($destPath, 0755, true);
        }

        /* ----- hapus foto lama (jika ada) ----- */
        if ($user->foto) {
            $oldPath = public_path("images/{$roleFolder}/{$user->foto}");
            if (is_file($oldPath)) {
                @unlink($oldPath);
            }
        }

        /* ----- pindahkan file baru ----- */
        $file->move($destPath, $newFileName);

        // hanya nama file yang disimpan di DB
        return $newFileName;
    }

    /* ======================== UPDATE ========================= */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        /* ---------- 1. EDIT DATA PROFIL ---------- */
        if ($request->has('name')) {
            $request->validate([
                'name'     => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
                'email'    => ['required', 'email',  'max:255', Rule::unique('users')->ignore($user->id)],
                'no_hp'    => ['nullable', 'string', 'max:25'],
            ]);

            $user->update($request->only([
                'name',
                'username',
                'no_anggota',
                'cabang',
                'no_hp',
                'alamat',
                'email'
            ]));

            return back()->with('success', 'Data akun berhasil diperbarui.');
        }

        /* ---------- 2. UBAH FOTO PROFIL ---------- */
        if ($request->hasFile('foto')) {
            $request->validate([
                'foto' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            ]);

            $newFileName = $this->handleImageUpload($request, $user);
            $user->update(['foto' => $newFileName]);

            return back()->with('success', 'Foto profil berhasil diperbarui.');
        }

        /* ---------- 3. UBAH PASSWORD ---------- */
        if ($request->has('new_password')) {
            $request->validate([
                'new_password'              => ['required', 'min:6', 'confirmed'],
                'new_password_confirmation' => ['required'],
            ]);

            $user->update(['password' => Hash::make($request->new_password)]);

            return back()->with('success', 'Password berhasil diubah.');
        }

        return back()->with('warning', 'Tidak ada perubahan yang diproses.');
    }
}
