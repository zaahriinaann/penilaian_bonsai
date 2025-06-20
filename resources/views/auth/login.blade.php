@extends('auth.auth-layouts')

@section('content')
    <div id="kt_content_container" class="d-flex flex-column-fluid align-items-center container-xl">

        <div class="card m-auto">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif
                <div class="card-body">
                    <div class="text-center mb-5">
                        <img src="{{ asset('assets/media/logos/logo-ppbi-small-nobg.png') }}"
                            alt="" class="w-50">
                    </div>
                    <h2 class="text-center mb-5 fs-1">Login</h2>
                    <div class="form-group mb-2">
                        <label for="username">Email atau Username</label>
                        <input id="username" type="text" class="form-control @error('username') is-invalid @enderror"
                            name="username" value="{{ old('username') }}" required autocomplete="username" autofocus
                            placeholder="Masukkan email atau username">

                        @error('username')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group mb-2">
                        <label for="password">Password</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                            name="password" required autocomplete="current-password" placeholder="********">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="d-flex gap-1 my-4">
                        <span>Belum punya akun?</span>
                        <a href="{{ route('register') }}">Daftar disini</a>
                    </div>
                    <button class="w-100 btn btn-primary btn-sm">Login</button>
                </div>
            </form>
        </div>
    </div>
@endsection
