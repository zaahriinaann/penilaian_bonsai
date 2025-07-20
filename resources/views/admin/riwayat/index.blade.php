@extends('layouts.app')
@section('title', 'Riwayat Kontes')

@section('content')
    <div class="container py-4">
        <div class="card shadow-sm rounded-4">
            <div class="card-header align-items-center">
                <h3>Daftar Kontes</h3>
            </div>
            <div class="card-body">
                {{-- Form Pencarian dan Filter --}}
                <form method="GET" class="mb-3">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Cari Nama Kontes / Tahun</label>
                            <input type="text" name="search" id="search" class="form-control"
                                value="{{ request('search') }}" placeholder="Contoh: Nasional atau 2025">
                        </div>
                        <div class="col-md-2">
                            <label for="tahun" class="form-label">Filter Tahun</label>
                            <select name="tahun" id="tahun" class="form-select">
                                <option value="">Semua Tahun</option>
                                @foreach ($kontesList->pluck('tanggal_mulai_kontes')->map(fn($tgl) => \Carbon\Carbon::parse($tgl)->year)->unique() as $tahun)
                                    <option value="{{ $tahun }}" {{ request('tahun') == $tahun ? 'selected' : '' }}>
                                        {{ $tahun }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('admin.riwayat.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                        </div>
                    </div>
                </form>

                {{-- Tabel --}}
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Nama Kontes</th>
                                <th>Periode</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($kontesList as $kontes)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $kontes->nama_kontes }}</td>
                                    <td>{{ $kontes->tanggal_mulai_kontes }} s/d {{ $kontes->tanggal_selesai_kontes }}</td>
                                    <td>
                                        <a href="{{ route('admin.riwayat.juri', $kontes->id) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-list"></i> Daftar Juri
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Tidak ada data kontes.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $kontesList->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
