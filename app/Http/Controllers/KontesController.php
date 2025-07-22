<?php

namespace App\Http\Controllers;

use App\Models\Kontes;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class KontesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /** Display a listing of the resource. */
    public function index(Request $request)
    {
        $query = Kontes::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama_kontes', 'like', "%{$search}%")
                ->orWhere('tempat_kontes', 'like', "%{$search}%")
                ->orWhere('tingkat_kontes', 'like', "%{$search}%");
        }
        $dataRender = $query->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.kontes.index', compact('dataRender'));
    }

    /** Store a newly created resource in storage. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_kontes'            => 'required|string|max:255',
            'tempat_kontes'          => 'required|string|max:255',
            'tingkat_kontes'         => 'required|string|in:Bahan,Pratama,Madya,Bintang',
            'link_gmaps'             => 'nullable|url|max:255',
            'tanggal_mulai_kontes'   => 'required|date',
            'tanggal_selesai_kontes' => 'required|date|after_or_equal:tanggal_mulai_kontes',
            'jumlah_peserta'         => 'required|integer|min:0',
            'harga_tiket_kontes'     => 'nullable|integer|min:0',
            'poster_kontes'          => 'nullable|image|max:2048',
        ]);

        // Non-aktifkan semua kontes lama
        Kontes::query()->update(['status' => '0']);
        $data['status'] = '1';

        // Generate slug from name
        $data['slug'] = Str::slug(trim($data['nama_kontes']));

        // Sync peserta limits
        $data['limit_peserta'] = $data['jumlah_peserta'];

        // poster upload
        if ($request->hasFile('poster_kontes')) {
            $data['poster_kontes'] = $this->handleImageUpload($request->file('poster_kontes'));
        }

        Kontes::create($data);
        Session::flash('message', "Kontes {$data['nama_kontes']} berhasil disimpan.");
        return redirect()->route('master.kontes.index');
    }

    /** Display the specified resource. */
    public function show(string $slug)
    {
        $kontes = Kontes::where('slug', $slug)->firstOrFail();
        return view('admin.kontes.show', compact('kontes'));
    }

    /** Update the specified resource in storage. */
    public function update(Request $request, string $slug)
    {
        $kontes = Kontes::where('slug', $slug)->firstOrFail();

        // Toggle active status
        if ($request->has('setActive')) {
            Kontes::where('slug', $slug)->update(['status' => '1']);
            Kontes::where('slug', '!=', $slug)->update(['status' => '0']);
            Session::flash('message', "Kontes {$kontes->nama_kontes} berhasil diaktifkan.");
            return back();
        }

        $data = $request->validate([
            'edit_nama_kontes'           => 'required|string|max:255',
            'edit_tempat_kontes'         => 'required|string|max:255',
            'edit_tingkat_kontes'        => 'required|string|in:Bahan,Pratama,Madya,Bintang',
            'edit_link_gmaps'            => 'nullable|url|max:255',
            'edit_tanggal_mulai_kontes'  => 'required|date',
            'edit_tanggal_selesai_kontes' => 'required|date|after_or_equal:edit_tanggal_mulai_kontes',
            'edit_jumlah_peserta'        => 'required|integer|min:0',
            'edit_harga_tiket_kontes'    => 'nullable|integer|min:0',
            'edit_poster_kontes'         => 'nullable|image|max:2048',
            'poster_lama'                => 'nullable|string',
        ]);

        // Map fields directly as strings
        $update = [
            'nama_kontes'           => $data['edit_nama_kontes'],
            'tempat_kontes'         => $data['edit_tempat_kontes'],
            'tingkat_kontes'        => $data['edit_tingkat_kontes'],
            'link_gmaps'            => $data['edit_link_gmaps'] ?? null,
            'tanggal_mulai_kontes'  => $data['edit_tanggal_mulai_kontes'],
            'tanggal_selesai_kontes' => $data['edit_tanggal_selesai_kontes'],
            'jumlah_peserta'        => $data['edit_jumlah_peserta'],
            'limit_peserta'         => $data['edit_jumlah_peserta'],
            'harga_tiket_kontes'    => $data['edit_harga_tiket_kontes'] ?? null,
            'slug'                  => Str::slug(trim($data['edit_nama_kontes'])),
        ];

        // Handle poster upload if new file provided
        if ($request->hasFile('edit_poster_kontes') && $request->file('edit_poster_kontes') instanceof UploadedFile) {
            if (!empty($data['poster_lama'])) {
                @unlink(public_path('assets/images/kontes/' . $data['poster_lama']));
            }
            $update['poster_kontes'] = $this->handleImageUpload($request->file('edit_poster_kontes'));
        }

        $kontes->update($update);
        Session::flash('message', "Kontes {$update['nama_kontes']} berhasil diperbarui.");
        return back();
    }

    /** Remove the specified resource from storage. */
    public function destroy(string $slug)
    {
        $kontes = Kontes::where('slug', $slug)->firstOrFail();
        $kontes->update(['slug' => $kontes->slug . '-deleted-' . uniqid()]);
        $kontes->delete();
        return response()->json(['message' => "Kontes {$kontes->nama_kontes} berhasil dihapus."]);
    }

    /** Handle poster upload. */
    protected function handleImageUpload(UploadedFile $file): string
    {
        $filename = time() . '.' . $file->getClientOriginalExtension();
        $path = public_path('assets/images/kontes');
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }
        $file->move($path, $filename);
        return $filename;
    }
}
