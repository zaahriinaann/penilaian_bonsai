@extends('layouts.app')
@section('title', 'Juri dalam Kontes')

@section('content')
    <div class="container py-4">
        <div class="card shadow-sm rounded-4">
            <div class="card-header align-items-center">
                <h3>Daftar Juri dalam Kontes: {{ $kontes->nama_kontes }}</h3>
            </div>
            <div class="card-body">
                {{-- Form Filter --}}
                <form method="GET" class="mb-3">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Cari Nama / Email Juri</label>
                            <input type="text" id="search" name="search" class="form-control"
                                value="{{ request('search') }}" placeholder="contoh: 'Andi' atau 'email@contoh.com'">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('admin.riwayat.juri', $kontes->id) }}"
                                class="btn btn-outline-secondary w-100">Reset</a>
                        </div>
                    </div>
                </form>

                {{-- Tabel Juri --}}
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Nama Juri</th>
                                <th>Email</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($juriList as $juri)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $juri->user->name }}</td>
                                    <td>{{ $juri->user->email }}</td>
                                    <td>
                                        <a href="{{ route('admin.riwayat.peserta', [$kontes->id, $juri->id]) }}"
                                            class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-people-fill"></i> Lihat Peserta
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">Tidak ada juri ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $juriList->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
