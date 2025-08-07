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
    /* ======== 1. TAMPILKAN FORM REGISTER ======== */
    public function showRegister()
    {
        // 1. Ambil data kota/kabupaten dari JSON
        $cities = json_decode(
            file_get_contents(resource_path('js/regencies.json')),
            true
        );

        // 2. Urutkan alfabet berdasarkan `name`
        $cities = collect($cities)
            ->sortBy('name')
            ->values()
            ->all();

        // 3. Render view register dengan $cities
        return view('auth.register', compact('cities'));
    }

    /* ======== 2. PROSES REGISTRASI ======== */
    public function register(Request $request)
    {
        // validasi input
        $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'username'     => ['required', 'string', 'max:255', 'unique:users'],
            'no_anggota'   => ['nullable', 'string', 'max:100'],
            'cabang'       => ['required', 'string', 'max:255'],
            'no_hp'        => ['nullable', 'string', 'max:25'],
            'alamat'       => ['nullable', 'string'],
            'email'        => ['required', 'email', 'max:255', 'unique:users'],
            'password'     => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // buat user baru
        $user = User::create([
            'name'       => $request->name,
            'username'   => $request->username,
            'no_anggota' => $request->no_anggota,
            'cabang'     => $request->cabang,
            'no_hp'      => $request->no_hp,
            'alamat'     => $request->alamat,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role'       => 'peserta',
            'foto'       => null,
        ]);

        // langsung login
        Auth::login($user);

        return redirect()
            ->route('akun.index')
            ->with('success', 'Registrasi berhasil! Selamat datang, ' . $user->name);
    }

    /* ======== 3. HALAMAN AKUN (INDEX) ======== */
    public function index()
    {
        $user = Auth::user();
        $data = compact('user');

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

    /* ======== 4. HANDLE UPLOAD FOTO ======== */
    protected function handleImageUpload(Request $request, User $user): ?string
    {
        if (! $request->hasFile('foto')) {
            return $user->foto;
        }

        $file        = $request->file('foto');
        $newFileName = time() . '.' . $file->getClientOriginalExtension();
        $roleFolder  = in_array($user->role, ['admin', 'anggota']) ? 'peserta' : 'juri';
        $destPath    = public_path("images/{$roleFolder}");

        if (! is_dir($destPath)) {
            mkdir($destPath, 0755, true);
        }

        if ($user->foto) {
            $old = public_path("images/{$roleFolder}/{$user->foto}");
            if (is_file($old)) @unlink($old);
        }

        $file->move($destPath, $newFileName);
        return $newFileName;
    }

    /* ======== 5. PROSES UPDATE AKUN ======== */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // a) edit profil
        if ($request->has('name')) {
            $request->validate([
                'name'     => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
                'email'    => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
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

        // b) ganti foto
        if ($request->hasFile('foto')) {
            $request->validate([
                'foto' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            ]);
            $new = $this->handleImageUpload($request, $user);
            $user->update(['foto' => $new]);
            return back()->with('success', 'Foto profil berhasil diperbarui.');
        }

        // c) ganti password
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
