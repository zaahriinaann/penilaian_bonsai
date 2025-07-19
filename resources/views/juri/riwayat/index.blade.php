@extends('layouts.app')
@section('title', 'Riwayat Kontes Dinilai')

@section('content')
    <div class="container py-4">
        <div class="card shadow-sm rounded-4">
            <div class="card-header bg-dark text-white rounded-top-4">
                <strong>📋 Kontes yang Pernah Dinilai</strong>
            </div>
            <div class="card-body">

                {{-- Form Pencarian dan Filter Tahun --}}
                <form method="GET" class="mb-4">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Cari Nama Kontes</label>
                            <input type="text" name="search" id="search" class="form-control"
                                placeholder="Contoh: Festival Bonsai" value="{{ request('search') }}">
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
                            <a href="{{ route('juri.riwayat.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                        </div>
                    </div>
                </form>

                {{-- Tabel Kontes --}}
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Kontes</th>
                                <th>Periode</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($kontesList as $kontes)
                                <tr>
                                    <td>{{ $kontes->nama_kontes }}</td>
                                    <td>{{ $kontes->tanggal_mulai_kontes }} s/d {{ $kontes->tanggal_selesai_kontes }}</td>
                                    <td>
                                        <a href="{{ route('juri.riwayat.peserta', $kontes->id) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            👥 Lihat Peserta
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Belum ada kontes yang Anda nilai.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
@endsection
