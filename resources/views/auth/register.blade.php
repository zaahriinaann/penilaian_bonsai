@extends('auth.auth-layouts')

@section('content')
    <div id="kt_content_container" class="d-flex flex-column-fluid align-items-center justify-content-center py-5">
        {{-- KUNCI UTAMA: max-width dan m-auto --}}
        <div class="card m-auto shadow-lg" style="max-width: 550px;"> {{-- Meningkatkan max-width sedikit dan menambahkan shadow --}}
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="card-body p-5 p-md-5">
                    <div class="text-center mb-5 mt-3">
                        <img src="{{ asset('assets/media/logos/logo-ppbi-small-nobg.png') }}" alt="Logo PPBI"
                            class="w-50 mx-auto d-block">
                    </div>
                    <h2 class="text-center mb-5 fs-2 fw-bold text-dark border-bottom pb-3">Daftar Akun Peserta</h2>

                    {{-- Grup Nama Lengkap dan No. Anggota --}}
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0"> {{-- Kolom untuk Nama Lengkap, mb-3 untuk mobile, mb-md-0 untuk desktop --}}
                            <label for="name" class="form-label fw-semibold">{{ __('Nama Lengkap') }}</label>
                            <input id="name" type="text"
                                class="form-control form-control-lg @error('name') is-invalid @enderror" name="name"
                                value="{{ old('name') }}" required autocomplete="name" autofocus
                                placeholder="Masukkan nama lengkap">
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-md-6"> {{-- Kolom untuk No. Anggota --}}
                            <label for="no_anggota" class="form-label fw-semibold">{{ __('No. Anggota') }}</label>
                            <input id="no_anggota" type="text"
                                class="form-control form-control-lg @error('no_anggota') is-invalid @enderror"
                                name="no_anggota" value="{{ old('no_anggota') }}" placeholder="Masukkan nomor anggota">
                            @error('no_anggota')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    {{-- Grup Username dan Cabang --}}
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0"> {{-- Kolom untuk Username --}}
                            <label for="username" class="form-label fw-semibold">{{ __('Username') }}</label>
                            <input id="username" type="text"
                                class="form-control form-control-lg @error('username') is-invalid @enderror" name="username"
                                value="{{ old('username') }}" required autocomplete="username"
                                placeholder="Buat username unik">
                            @error('username')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-md-6"> {{-- Kolom untuk Cabang --}}
                            <label for="cabang" class="form-label fw-semibold">{{ __('Cabang') }}</label>
                            <select id="cabang" name="cabang" class="form-control form-control-lg">
                                <option value="">Pilih Cabang</option>
                                <option value="jakarta" {{ old('cabang') == 'jakarta' ? 'selected' : '' }}>Jakarta</option>
                                <option value="surabaya" {{ old('cabang') == 'surabaya' ? 'selected' : '' }}>Surabaya
                                </option>
                                <option value="bandung" {{ old('cabang') == 'bandung' ? 'selected' : '' }}>Bandung</option>
                                {{-- Tambahkan opsi cabang lainnya sesuai kebutuhan --}}
                            </select>
                        </div>
                    </div>

                    {{-- Grup No. HP dan Email --}}
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0"> {{-- Kolom untuk No. HP --}}
                            <label for="no_hp" class="form-label fw-semibold">{{ __('No. HP') }}</label>
                            <input id="no_hp" type="tel"
                                class="form-control form-control-lg @error('no_hp') is-invalid @enderror" name="no_hp"
                                value="{{ old('no_hp') }}" placeholder="Contoh: 081234567890">
                            @error('no_hp')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-md-6"> {{-- Kolom untuk Email --}}
                            <label for="email" class="form-label fw-semibold">{{ __('Alamat Email') }}</label>
                            <input id="email" type="email"
                                class="form-control form-control-lg @error('email') is-invalid @enderror" name="email"
                                value="{{ old('email') }}" required autocomplete="email" placeholder="contoh@domain.com">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    {{-- Alamat (tetap satu baris penuh) --}}
                    <div class="row mb-4">
                        <div class="col-12"> {{-- Kolom tunggal untuk Alamat --}}
                            <label for="alamat" class="form-label fw-semibold">{{ __('Alamat Lengkap') }}</label>
                            <textarea id="alamat" class="form-control form-control-lg" name="alamat" rows="3"
                                placeholder="Masukkan alamat lengkap Anda"></textarea>
                        </div>
                    </div>

                    {{-- Grup Password dan Konfirmasi Password --}}
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0"> {{-- Kolom untuk Password --}}
                            <label for="password" class="form-label fw-semibold">{{ __('Kata Sandi') }}</label>
                            <div class="input-group input-group-lg">
                                <input id="password" type="password"
                                    class="form-control @error('password') is-invalid @enderror" name="password" required
                                    autocomplete="new-password" placeholder="Minimal 8 karakter">
                                <button type="button" class="btn btn-outline-secondary"
                                    onclick="togglePassword('password')">
                                    <i class="bi bi-eye-fill" id="password-eye"></i>
                                    <i class="bi bi-eye-slash-fill d-none" id="password-slash-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-md-6"> {{-- Kolom untuk Konfirmasi Password --}}
                            <label for="password-confirm"
                                class="form-label fw-semibold">{{ __('Konfirmasi Kata Sandi') }}</label>
                            <div class="input-group input-group-lg">
                                <input id="password-confirm" type="password" class="form-control"
                                    name="password_confirmation" required autocomplete="new-password"
                                    placeholder="Ulangi kata sandi">
                                <button type="button" class="btn btn-outline-secondary"
                                    onclick="togglePassword('password-confirm')">
                                    <i class="bi bi-eye-fill" id="password-confirm-eye"></i>
                                    <i class="bi bi-eye-slash-fill d-none" id="password-confirm-slash-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 my-5 justify-content-center fs-6">
                        <span class="text-secondary">Sudah punya akun?</span>
                        <a href="{{ route('login') }}" class="text-decoration-none fw-bold text-primary">Login di
                            sini</a>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 fs-4 fw-bold">
                        {{ __('Daftar Sekarang') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function togglePassword(id) {
            const input = document.getElementById(id);
            const eyeIcon = document.getElementById(id + '-eye');
            const slashEyeIcon = document.getElementById(id + '-slash-eye');

            if (input.type === "password") {
                input.type = "text";
                eyeIcon.classList.add("d-none");
                slashEyeIcon.classList.remove("d-none");
            } else {
                input.type = "password";
                eyeIcon.classList.remove("d-none");
                slashEyeIcon.classList.add("d-none");
            }
        }
    </script>
@endsection

@section('script')
    <script>
        function togglePassword(e) {
            const x = document.getElementById(e);

            if (x.type === "password") {
                x.type = "text";
                document.getElementById(e + '-eye').classList.add('d-none');
                document.getElementById(e + '-slash-eye').classList.remove('d-none');
            } else {
                x.type = "password";
                document.getElementById(e + '-eye').classList.remove('d-none');
                document.getElementById(e + '-slash-eye').classList.add('d-none');
            }
        }
    </script>
@endsection
