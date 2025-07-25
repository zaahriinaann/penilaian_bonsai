<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Setelah registrasi, redirect ke login.
     *
     * @var string
     */
    protected $redirectTo = '/login';

    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Tampilkan form registrasi dengan daftar kota/kabupaten terurut.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        // Ambil daftar regencies dari resource/js/regencies.json
        $cities = json_decode(
            file_get_contents(resource_path('js/regencies.json')),
            true
        );

        // Urutkan alfabet berdasarkan 'name'
        $cities = collect($cities)
            ->sortBy('name')
            ->values()
            ->all();

        return view('auth.register', compact('cities'));
    }

    /**
     * Validasi input registrasi.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name'       => ['required', 'string', 'max:255'],
            'username'   => ['required', 'string', 'max:255', 'unique:users'],
            'email'      => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'   => ['required', 'string', 'min:6', 'confirmed'],
            'cabang'     => ['required', 'string', 'max:255'],
            'no_hp'      => ['nullable', 'string', 'max:25', 'unique:users'],
            'alamat'     => ['nullable', 'string', 'max:255'],
        ]);
    }

    /**
     * Buat instance user baru setelah validasi.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        // Generate nomor anggota acak
        $data['no_anggota'] = rand(1000, 9999);

        return User::create([
            'name'       => $data['name'],
            'username'   => $data['username'],
            'email'      => $data['email'],
            'password'   => Hash::make($data['password']),
            'role'       => 'anggota',
            'no_anggota' => $data['no_anggota'],
            'cabang'     => $data['cabang'],
            'no_hp'      => $data['no_hp']   ?? null,
            'alamat'     => $data['alamat']  ?? null,
        ]);
    }

    /**
     * Proses registrasi (override), tanpa auto-login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        // 1. Validasi
        $this->validator($request->all())->validate();

        // 2. Simpan user
        event(new Registered($user = $this->create($request->all())));

        // 3. Redirect kembali ke login dengan pesan sukses
        return redirect($this->redirectTo)
            ->with('status', 'Registrasi berhasil! Silakan login.');
    }
}
