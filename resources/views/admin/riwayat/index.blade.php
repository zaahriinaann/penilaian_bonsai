@extends('layouts.app')
@section('title', 'Riwayat Kontes')

@section('content')
    <div class="container py-4">
        <div class="card shadow-sm rounded-4">
            <div class="card-header bg-primary text-white rounded-top-4">
                <strong>📋 Daftar Kontes</strong>
            </div>
            <div class="card-body table-responsive">
                <form method="GET" class="mb-3">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Cari Nama Kontes / Tahun</label>
                            <input type="text" id="search" name="search" class="form-control"
                                value="{{ request('search') }}" placeholder="contoh: 2025 atau 'Nasional'">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('admin.riwayat.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                        </div>
                    </div>
                </form>

                <table class="table table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Kontes</th>
                            <th>Periode</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($kontesList as $kontes)
                            <tr>
                                <td>{{ $kontes->nama_kontes }}</td>
                                <td>{{ $kontes->tanggal_mulai_kontes }} s/d {{ $kontes->tanggal_selesai_kontes }}</td>
                                <td>
                                    <a href="{{ route('admin.riwayat.juri', $kontes->id) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        📑 Daftar Juri
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
