@extends('auth.auth-layouts')

@section('content')
    <div id="kt_content_container" class="d-flex flex-column-fluid align-items-center justify-content-center py-5">
        <div class="card shadow-lg" style="max-width: 400px; width: 100%;">
            <form method="POST" action="{{ route('register') }}" class="p-4">
                @csrf

                {{-- Logo + Title --}}
                <div class="text-center mb-4">
                    <img src="{{ asset('assets/media/logos/logo-ppbi-small-nobg.png') }}" alt="Logo PPBI" style="height:50px;"
                        class="mb-3">
                    <h3 class="fw-bold">Daftar Akun Peserta</h3>
                </div>

                @php
                    $cities = collect($cities)->sortBy('name')->values()->all();
                @endphp

                <div class="row g-3">
                    {{-- Nama Lengkap --}}
                    <div class="col-md-6">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                            class="form-control @error('name') is-invalid @enderror" placeholder="Masukkan nama lengkap">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- No. Anggota --}}
                    <div class="col-md-6">
                        <label for="no_anggota" class="form-label">No. Anggota</label>
                        <input id="no_anggota" type="text" name="no_anggota" value="{{ old('no_anggota') }}"
                            class="form-control @error('no_anggota') is-invalid @enderror"
                            placeholder="Masukkan no. anggota">
                        @error('no_anggota')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Username --}}
                    <div class="col-md-6">
                        <label for="username" class="form-label">Username</label>
                        <input id="username" type="text" name="username" value="{{ old('username') }}" required
                            class="form-control @error('username') is-invalid @enderror" placeholder="Buat username unik">
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Cabang --}}
                    <div class="col-md-6">
                        <label for="cabang" class="form-label">Cabang</label>
                        <input list="citiesList" id="cabang" name="cabang" value="{{ old('cabang') }}" required
                            class="form-control @error('cabang') is-invalid @enderror" placeholder="Pilih kota/kabupaten">
                        <datalist id="citiesList">
                            @foreach ($cities as $c)
                                <option value="{{ $c['name'] }}">
                            @endforeach
                        </datalist>
                        @error('cabang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- No. HP --}}
                    <div class="col-md-6">
                        <label for="no_hp" class="form-label">No. HP</label>
                        <input id="no_hp" type="tel" name="no_hp" value="{{ old('no_hp') }}"
                            class="form-control @error('no_hp') is-invalid @enderror" placeholder="081234567890">
                        @error('no_hp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required
                            class="form-control @error('email') is-invalid @enderror" placeholder="contoh@domain.com">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Alamat Lengkap (full-width) --}}
                    <div class="col-12">
                        <label for="alamat" class="form-label">Alamat Lengkap</label>
                        <textarea id="alamat" name="alamat" rows="3" class="form-control @error('alamat') is-invalid @enderror"
                            placeholder="Masukkan alamat lengkap">{{ old('alamat') }}</textarea>
                        @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Kata Sandi --}}
                    <div class="col-md-6">
                        <label for="password" class="form-label">Kata Sandi</label>
                        <div class="input-group">
                            <input id="password" type="password" name="password" required
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="Minimal 8 karakter">
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password')">
                                <i class="bi bi-eye-fill" id="password-eye"></i>
                                <i class="bi bi-eye-slash-fill d-none" id="password-slash-eye"></i>
                            </button>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Ulangi Kata Sandi --}}
                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label">Ulangi Kata Sandi</label>
                        <div class="input-group">
                            <input id="password_confirmation" type="password" name="password_confirmation" required
                                class="form-control @error('password_confirmation') is-invalid @enderror"
                                placeholder="Ulangi kata sandi">
                            <button type="button" class="btn btn-outline-secondary"
                                onclick="togglePassword('password_confirmation')">
                                <i class="bi bi-eye-fill" id="password_confirmation-eye"></i>
                                <i class="bi bi-eye-slash-fill d-none" id="password_confirmation-slash-eye"></i>
                            </button>
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary w-100">Daftar Sekarang</button>
                </div>

                {{-- Link ke login --}}
                <div class="text-center mt-3">
                    <small class="text-muted">
                        Sudah punya akun?
                        <a href="{{ route('login') }}">Login di sini</a>
                    </small>
                </div>
            </form>
        </div>
    </div>

    {{-- Toggle password visibility --}}
    <script>
        function togglePassword(fieldId) {
            const inp = document.getElementById(fieldId);
            const eye = document.getElementById(fieldId + '-eye');
            const slash = document.getElementById(fieldId + '-slash-eye');
            if (inp.type === 'password') {
                inp.type = 'text';
                eye.classList.add('d-none');
                slash.classList.remove('d-none');
            } else {
                inp.type = 'password';
                eye.classList.remove('d-none');
                slash.classList.add('d-none');
            }
        }
    </script>
@endsection
