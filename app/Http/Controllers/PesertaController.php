<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class PesertaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = User::where('role', 'anggota');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('no_anggota', 'like', "%{$search}%")
                    ->orWhere('cabang', 'like', "%{$search}%")
                    ->orWhere('no_hp', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        $dataRender = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $province = config('province.obj');

        // → Tambahkan ini:
        $cities = json_decode(
            file_get_contents(resource_path('js/regencies.json')),
            true
        );

        return view('admin.peserta.index', compact(
            'dataRender',
            'province',
            'search',
            'cities'    // ← variabel baru
        ));
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
        // Persiapan data
        $data = $request->all();
        $data['name'] = $data['nama'];
        $data['role'] = 'anggota';
        $data['password'] = bcrypt($data['username']); // password = username
        $data['foto'] = $this->handleImageUpload($request, 'store');
        $data['email_verified_at'] = now();

        // Simpan user
        User::create($data);

        return redirect()->back()->with('success', 'Peserta berhasil ditambahkan.');
    }


    /**
     * Display the specified resource.
     */
    public function show()
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // 1. Validasi
        $validated = $request->validate([
            'nama'       => 'required|string|max:255',
            'username'   => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'username')->ignore($id)
            ],
            'email'      => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($id)
            ],
            'no_anggota' => 'required|string|max:50',
            'cabang'     => 'required|string|max:100',
            'no_hp'      => 'required|string|max:20',
            'alamat'     => 'required|string',
            'password'   => 'nullable|string|min:6',
            'foto'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // 2. Cari user
        $user = User::findOrFail($id);

        // 3. Mapping & assign
        $user->name       = $validated['nama'];
        $user->username   = $validated['username'];
        $user->email      = $validated['email'];
        $user->no_anggota = $validated['no_anggota'];
        $user->cabang     = $validated['cabang'];
        $user->no_hp      = $validated['no_hp'];
        $user->alamat     = $validated['alamat'];

        // 4. Jika password diubah
        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }

        // 5. Jika ada upload foto baru, hapus yang lama & simpan yang baru
        if ($request->hasFile('foto')) {
            // hapus file lama jika perlu
            if ($user->foto && file_exists(public_path("assets/images/peserta/{$user->foto}"))) {
                unlink(public_path("assets/images/peserta/{$user->foto}"));
            }
            // simpan yang baru
            $user->foto = $this->handleImageUpload($request, 'update');
        }

        // 6. Simpan semua perubahan
        $user->save();

        // 7. Redirect dengan pesan sukses
        return redirect()->back()->with('success', 'Peserta berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // ini udah bisa, tinggal tambahin aja delete kaya biasa atau mau pake soft delete juga bisa
        // kalau mau pake soft delete, contohnya kaya yang juri, tambah "delete-" di depan username
        // atau lait di database, jika ada yang unique, contohnya email, tambah "delete-" di depan email
        // begitu seterusnya, cari id/slug nya, opsinya update data atau delete data
        // coba liat web.php untuk contoh parameter route nya
        // karena ini campuran pakai js, kalau mau cek data nya ke kirim atau ga
        // pakai return json, contohnya return response()->json(data);
        // cek di app.blade.php terus nyalain console.log(response) kalau mau cek datanya
        // kalo udah buka console di inspect element, comment swalnya dulu

        $user = User::where('id', $id)->first();

        // ini null safety, sebenernya sama kaya try() catch() yang aku bkin di controller lain, cuman ini cek manual pake if
        if (!$user) {
            return response()->json([
                'message' => "Peserta tidak ditemukan."
            ], 404);
        }

        $user->delete();
        return response()->json([
            // 'data' => $user,
            'message' => "Peserta berhasil dihapus."
        ], 200);
    }


    protected function handleImageUpload($request, $typeInput)
    {
        if ($request->hasFile('foto') && $typeInput) {
            $image = $request->file('foto');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('assets/images/peserta');

            // Buat folder jika belum ada (opsional)
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            if ($typeInput === 'store') {
                $image->move($destinationPath, $imageName);
                return $imageName;
            } elseif ($typeInput === 'update') {
                $fotoLama = $request->input('foto_lama');
                $oldImagePath = $destinationPath . '/' . $fotoLama;

                if (!empty($fotoLama) && file_exists($oldImagePath) && is_file($oldImagePath)) {
                    unlink($oldImagePath);
                }

                $image->move($destinationPath, $imageName);
                return $imageName;
            }
        }

        return null;
    }
}
