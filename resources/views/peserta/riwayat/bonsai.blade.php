@extends('layouts.app')
@section('title', 'Bonsai Anda di Kontes')

@section('content')
    <div class="container py-4">
        <div class="card shadow-sm rounded-4">
            <div class="card-header align-items-center">
                <h3>Bonsai Anda pada Kontes: {{ $kontes->nama_kontes }}</h3>
            </div>
            <div class="card-body">
                <form method="GET" class="mb-3">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Cari Nama Bonsai</label>
                            <input type="text" id="search" name="search" class="form-control"
                                value="{{ request('search') }}" placeholder="Contoh: 'Beringin'">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('peserta.riwayat.bonsai', $kontes->id) }}"
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
                                <th>Nama Bonsai</th>
                                <th>Status Penilaian</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pendaftarans as $item)
                                <tr>
                                    <td>{{ $pendaftarans->firstItem() + $loop->index }}</td>
                                    <td>{{ $item->nomor_pendaftaran }}</td>
                                    <td>{{ $item->bonsai->nama_pohon }}</td>
                                    <td>
                                        @if ($item->bonsai->rekapNilai)
                                            <span class="badge bg-success">Sudah Dinilai</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Belum Dinilai</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item->bonsai->rekapNilai)
                                            <a href="{{ route('rekap-nilai.show', $item->bonsai_id) }}"
                                                class="btn btn-sm btn-outline-info">
                                                <i class="bi bi-graph-up"></i> Lihat Nilai Rekap
                                            </a>
                                        @else
                                            <span class="text-muted">Menunggu penilaian</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada bonsai Anda di kontes ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 d-flex justify-content-center">
                    {{ $pendaftarans->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
