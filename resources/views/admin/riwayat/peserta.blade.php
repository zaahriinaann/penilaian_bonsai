@extends('layouts.app')
@section('title', 'Peserta Dinilai oleh Juri')


@section('button-toolbar')
    {{-- Tombol Cetak Laporan --}}
    <div class="mb-3 text-end">
        <a href="{{ route('admin.riwayat.cetak', [$kontes->id]) }}" target="_blank" class="btn btn-danger rounded-pill">
            <i class="fas fa-print"></i>Cetak Laporan Rekap
        </a>
    </div>
@endsection

@section('content')
    <div class="container py-4">
        <div class="card shadow-sm rounded-4">
            <div class="card-header bg-dark text-white rounded-top-4 align-items-center">
                <strong>📋 Peserta Dinilai oleh {{ $juri->user->name }} pada {{ $kontes->nama_kontes }}</strong>
            </div>
            <div class="card-body">
                <form method="GET" class="mb-3">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Cari Peserta / Bonsai</label>
                            <input type="text" id="search" name="search" class="form-control"
                                value="{{ request('search') }}" placeholder="contoh: 'Ali' atau 'Beringin'">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('admin.riwayat.peserta', [$kontes->id, $juri->id]) }}"
                                class="btn btn-outline-secondary w-100">Reset</a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>No Daftar</th>
                                <th>Nama Peserta</th>
                                <th>Bonsai</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pendaftarans as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->nomor_pendaftaran }}</td>
                                    <td>{{ $item->user->name }}</td>
                                    <td>{{ $item->bonsai->nama_pohon }}</td>
                                    <td class="text-nowrap">
                                        <a href="{{ route('admin.riwayat.detail', [$kontes->id, $juri->id, $item->bonsai_id]) }}"
                                            class="btn btn-sm btn-outline-success">
                                            🔍 Lihat Nilai
                                        </a>
                                        <a href="{{ route('rekap.show', [$item->bonsai->nama_pohon, $item->nomor_juri]) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            📊 Lihat Rekap
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada peserta ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
