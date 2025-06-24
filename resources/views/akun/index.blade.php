@extends('layouts.app')

@section('title', 'Akun Saya')

@section('content')
    <div class="card">

        {{-- Kartu Informasi Akun Utama --}}
        <div class="card shadow-sm rounded-4 mb-4">
            <div class="card-body">
                <div class="row align-items-center"> {{-- Menggunakan align-items-center untuk foto dan teks sejajar vertikal --}}

                    {{-- Bagian Kiri: Foto Profil Lebih Besar dan Jelas --}}
                    <div class="col-md-4 text-center mb-4 mb-md-0 me-md-12"> {{-- Tambahkan me-md-5 di sini --}}
                        <img class="border border-secondary border-3" src="{{ asset('assets/media/avatars/blank.png') }}"
                            alt="Foto Profil" style="width: 10cm; height: 10cm; object-fit: cover;"> {{-- Ukuran lebih besar dan border jelas --}}
                        <h4 class="mt-3 text-primary">{{ Auth::user()->nama }}</h4> {{-- Nama di bawah foto, lebih menonjol --}}
                    </div>

                    {{-- Bagian Kanan: Informasi Akun dengan Teks Lebih Besar --}}
                    <div class="col-md-7"> {{-- Sesuaikan col-md-8 menjadi col-md-7 --}}
                        <h3 class="mb-3 text-secondary">Detail Akun Anda</h3> {{-- Judul lebih menonjol --}}
                        <div class="row g-2"> {{-- g-2 untuk jarak antar item yang sedikit lebih lega --}}
                            <div class="col-12 mb-2">
                                <p class="mb-0 fs-5"><strong>Username:</strong> {{ Auth::user()->username }}</p>
                                {{-- fs-5 untuk ukuran font lebih besar --}}
                            </div>
                            <div class="col-12 mb-2">
                                <p class="mb-0 fs-5"><strong>No. Anggota:</strong> {{ Auth::user()->no_anggota }}</p>
                            </div>
                            <div class="col-12 mb-2">
                                <p class="mb-0 fs-5"><strong>Cabang:</strong> {{ Auth::user()->cabang }}</p>
                            </div>
                            <div class="col-12 mb-2">
                                <p class="mb-0 fs-5"><strong>No. HP:</strong> {{ Auth::user()->no_hp }}</p>
                            </div>
                            <div class="col-12 mb-2">
                                <p class="mb-0 fs-5"><strong>Alamat:</strong> {{ Auth::user()->alamat }}</p>
                            </div>
                            <div class="col-12 mb-2">
                                <p class="mb-0 fs-5"><strong>Email:</strong> {{ Auth::user()->email }}</p>
                            </div>
                            <div class="col-12 mb-2">
                                <p class="mb-0 fs-5">
                                    <strong>Role:</strong>
                                    <span class="badge bg-success fs-6 py-2 px-3">{{ Auth::user()->role }}</span>
                                    {{-- Badge lebih besar dan warna menonjol --}}
                                </p>
                            </div>
                        </div>
                        <button class="btn btn-primary btn-lg mt-4 w-100" id="toggle-password-form"> {{-- Tombol lebih besar dan lebar penuh --}}
                            🔒 Ubah Password
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Ubah Password --}}
        <div class="card shadow-sm rounded-4 d-none" id="password-form-card">
            <div class="card-body">
                <h4 class="card-title mb-4 text-center">🔐 Form Ubah Password</h4> {{-- Judul di tengah --}}
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
                    <div class="d-grid gap-2"> {{-- Menggunakan d-grid untuk tombol penuh lebar dan ada jarak --}}
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
