@extends('layouts.app')

@section('title', 'Akun Saya')

@section('content')
    <div class="row g-4">
        {{-- Profil Foto --}}
        <div class="col-md-4">
            <div class="card shadow-sm h-100 rounded-4">
                <div class="card-body text-center">
                    @php
                        $user = Auth::user();
                        $folder = in_array($user->role, ['admin', 'anggota']) ? 'peserta' : 'juri';
                        $fotoPath = $user->foto
                            ? asset("images/{$folder}/{$user->foto}")
                            : asset('assets/media/avatars/blank.png');
                    @endphp

                    <img class="rounded-circle border shadow-sm mb-3" src="{{ $fotoPath }}" alt="Foto Profil"
                        style="width:150px;height:150px;object-fit:cover;">

                    <h5 class="mt-2">{{ Auth::user()->name }}</h5>
                    <p class="text-muted mb-0">{{ Auth::user()->email }}</p>
                    <!-- Tombol Modal Ubah Foto -->
                    <button class="btn btn-outline-primary btn-sm mt-3 w-100" data-bs-toggle="modal"
                        data-bs-target="#modalUbahFoto">
                        Ubah Foto Profil
                    </button>
                </div>
            </div>
        </div>

        {{-- Info Akun --}}
        <div class="col-md-8">
            <div class="card shadow-sm h-100 rounded-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0">Detail Akun Anda</h4>
                        <span class="badge bg-success text-uppercase px-3 py-2">{{ Auth::user()->role }}</span>
                    </div>
                    <table class="table table-borderless mb-4">
                        <tbody class="fs-6">
                            <tr>
                                <th class="text-nowrap">Nama</th>
                                <td>: {{ Auth::user()->name }}</td>
                            </tr>
                            <tr>
                                <th class="text-nowrap">Username</th>
                                <td>: {{ Auth::user()->username }}</td>
                            </tr>
                            <tr>
                                <th class="text-nowrap">No. Anggota</th>
                                <td>: {{ Auth::user()->no_anggota }}</td>
                            </tr>
                            <tr>
                                <th class="text-nowrap">Cabang</th>
                                <td>: {{ Auth::user()->cabang }}</td>
                            </tr>
                            <tr>
                                <th class="text-nowrap">No. HP</th>
                                <td>: {{ Auth::user()->no_hp }}</td>
                            </tr>
                            <tr>
                                <th class="text-nowrap">Alamat</th>
                                <td>: {{ Auth::user()->alamat }}</td>
                            </tr>
                            <tr>
                                <th class="text-nowrap">Email</th>
                                <td>: {{ Auth::user()->email }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- Tombol Modal Edit Akun -->
                    <button class="btn btn-outline-secondary w-100 mb-2" data-bs-toggle="modal"
                        data-bs-target="#modalEditAkun">
                        Edit Data Akun
                    </button>

                    <!-- Tombol Modal Ubah Password -->
                    <button class="btn btn-outline-dark w-100" data-bs-toggle="modal" data-bs-target="#modalUbahPassword">
                        Ubah Password
                    </button>

                </div>
            </div>
        </div>
    </div>
@endsection


<!-- Modal Edit Data Akun -->
<div class="modal fade" id="modalEditAkun" tabindex="-1" aria-labelledby="modalEditAkunLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form action="{{ route('akun.update', Auth::user()->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content rounded-4">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditAkunLabel">Edit Data Akun</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama</label>
                        <input type="text" name="name" class="form-control" value="{{ Auth::user()->name }}"
                            required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" value="{{ Auth::user()->username }}"
                            required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">No. Anggota</label>
                        <input type="text" name="no_anggota" class="form-control"
                            value="{{ Auth::user()->no_anggota }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Cabang</label>
                        <input type="text" name="cabang" class="form-control" value="{{ Auth::user()->cabang }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">No. HP</label>
                        <input type="text" name="no_hp" class="form-control" value="{{ Auth::user()->no_hp }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Alamat</label>
                        <input type="text" name="alamat" class="form-control" value="{{ Auth::user()->alamat }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ Auth::user()->email }}"
                            required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Ubah Foto Profil -->
<div class="modal fade" id="modalUbahFoto" tabindex="-1" aria-labelledby="modalUbahFotoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('akun.update', Auth::user()->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="modal-content rounded-4">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalUbahFotoLabel">Ubah Foto Profil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Pilih Foto Baru</label>
                        <input type="file" name="foto" class="form-control" accept="image/*" required>
                        <small class="text-muted">Maksimum 2MB, format: jpg/jpeg/png/webp.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Ubah Password -->
<div class="modal fade" id="modalUbahPassword" tabindex="-1" aria-labelledby="modalUbahPasswordLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('akun.update', Auth::user()->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content rounded-4">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalUbahPasswordLabel">Ubah Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body row g-3">
                    <div class="col-12">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="new_password" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" name="new_password_confirmation" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>



@section('script')
    <script>
        const passwordCard = document.getElementById('password-form-card');
        const toggleBtn = document.getElementById('toggle-password-form');
        const cancelBtn = document.getElementById('cancel-password-form');

        toggleBtn.addEventListener('click', () => {
            passwordCard.classList.remove('d-none');
            toggleBtn.classList.add('d-none');
        });

        cancelBtn.addEventListener('click', () => {
            passwordCard.classList.add('d-none');
            toggleBtn.classList.remove('d-none');
        });
    </script>
@endsection
