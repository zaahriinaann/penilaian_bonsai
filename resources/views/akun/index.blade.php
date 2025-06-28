@extends('layouts.app')

@section('title', 'Akun Saya')

@section('content')
    <div class="card">

        {{-- Kartu Informasi Akun Utama --}}
        <div class="card shadow-sm rounded-4 mb-4">
            <div class="card-body">
                <div class="row align-items-center">

                    {{-- Bagian Kiri: Foto Profil --}}
                    <div class="col-md-4 text-center mb-4 mb-md-0 me-md-12">
                        <img class="border border-secondary border-3" src="{{ asset('assets/media/avatars/blank.png') }}"
                            alt="Foto Profil" style="width: 10cm; height: 10cm; object-fit: cover;">
                        <h4 class="mt-3 text-primary">{{ Auth::user()->nama }}</h4>
                    </div>

                    {{-- Bagian Kanan: Informasi Akun --}}
                    <div class="col-md-7">
                        <h3 class="mb-3 text-secondary">Detail Akun Anda</h3> {{-- Judul lebih menonjol --}}
                        <table class="table table-borderless fs-5">
                            <tbody>
                                <tr>
                                    <td class="fw-bold text-nowrap">Username</td>
                                    <td class="text-nowrap">:</td>
                                    <td>{{ Auth::user()->username }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-nowrap">No. Anggota</td>
                                    <td class="text-nowrap">:</td>
                                    <td>{{ Auth::user()->no_anggota }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-nowrap">Cabang</td>
                                    <td class="text-nowrap">:</td>
                                    <td>{{ Auth::user()->cabang }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-nowrap">No. HP</td>
                                    <td class="text-nowrap">:</td>
                                    <td>{{ Auth::user()->no_hp }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-nowrap">Alamat</td>
                                    <td class="text-nowrap">:</td>
                                    <td>{{ Auth::user()->alamat }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-nowrap">Email</td>
                                    <td class="text-nowrap">:</td>
                                    <td>{{ Auth::user()->email }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-nowrap">Role</td>
                                    <td class="text-nowrap">:</td>
                                    <td>
                                        <span class="badge bg-success fs-6 py-2 px-3">{{ Auth::user()->role }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <button class="btn btn-primary btn-lg mt-4 w-100" id="toggle-password-form">
                            🔒 Ubah Password
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Ubah Password --}}
        <div class="card shadow-sm rounded-4 d-none" id="password-form-card">
            <div class="card-body">
                <h4 class="card-title mb-4 text-center">🔐 Form Ubah Password</h4>
                <form action="" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="current_password" class="form-label fs-5">Password Saat Ini</label>
                        {{-- Label lebih besar --}}
                        <input type="password" name="current_password" class="form-control form-control-lg" required>
                        {{-- Input lebih besar --}}
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label fs-5">Password Baru</label>
                        <input type="password" name="new_password" class="form-control form-control-lg" required>
                    </div>
                    <div class="mb-4">
                        <label for="new_password_confirmation" class="form-label fs-5">Konfirmasi Password Baru</label>
                        <input type="password" name="new_password_confirmation" class="form-control form-control-lg"
                            required>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success btn-lg">Simpan Perubahan</button>
                        <button type="button" class="btn btn-outline-danger btn-lg"
                            id="cancel-password-form">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        document.getElementById('toggle-password-form').addEventListener('click', function() {
            document.getElementById('password-form-card').classList.remove('d-none');
            this.classList.add('d-none');
        });

        document.getElementById('cancel-password-form').addEventListener('click', function() {
            document.getElementById('password-form-card').classList.add('d-none');
            document.getElementById('toggle-password-form').classList.remove('d-none');
        });
    </script>
@endsection
