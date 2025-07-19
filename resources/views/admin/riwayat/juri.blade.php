@extends('layouts.app')
@section('title', 'Juri dalam Kontes')

@section('content')
    <div class="container py-4">
        <div class="card shadow-sm rounded-4">
            <div class="card-header bg-secondary text-white rounded-top-4">
                <strong>🧑‍⚖️ Juri dalam Kontes: {{ $kontes->nama_kontes }}</strong>
            </div>
            <div class="card-body">
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

                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Juri</th>
                                <th>Email</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($juriList as $juri)
                                <tr>
                                    <td>{{ $juri->user->name }}</td>
                                    <td>{{ $juri->user->email }}</td>
                                    <td>
                                        <a href="{{ route('admin.riwayat.peserta', [$kontes->id, $juri->id]) }}"
                                            class="btn btn-sm btn-outline-info">
                                            👁️ Lihat Peserta
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
            </div>
        </div>
    </div>
@endsection
