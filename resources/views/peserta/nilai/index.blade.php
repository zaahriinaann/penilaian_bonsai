@extends('layouts.app')

@section('title', 'Daftar Bonsai Anda di Kontes Aktif')

@section('content')
    <div class="container py-4">
        <div class="card shadow-sm rounded-4">
            <div class="card-header rounded-top-4 align-items-center d-flex justify-content-between flex-wrap gap-2">
                <h3 class="mb-0">Daftar Bonsai Anda di Kontes: {{ $kontes->nama_kontes ?? '-' }}</h3>
            </div>
            <div class="card-body table-responsive">
                @if ($rekap->isEmpty())
                    <div class="alert alert-warning">Anda belum mendaftarkan bonsai pada kontes ini.</div>
                @else
                    <table class="table table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>No Daftar</th>
                                <th>Nama Bonsai</th>
                                <th>Skor Akhir</th>
                                <th>Himpunan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rekap as $item)
                                <tr>
                                    <td>{{ $loop->iteration + ($rekap->currentPage() - 1) * $rekap->perPage() }}</td>
                                    <td>{{ $item->nomor_pendaftaran }}</td>
                                    <td>{{ $item->nama_pohon }}</td>
                                    <td>{{ $item->skor_akhir ?? '-' }}</td>
                                    <td>{{ $item->himpunan_akhir ?? '-' }}</td>
                                    <td>
                                        @if ($item->skor_akhir !== null)
                                            <a href="{{ route('rekap-nilai.show', $item->id) }}"
                                                class="btn btn-sm btn-outline-info">
                                                <i class="bi bi-bar-chart-line-fill"></i> Lihat Nilai Rekap
                                            </a>
                                        @else
                                            <span class="badge bg-warning text-dark">Belum Dinilai</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- Pagination --}}
                    <div class="mt-3">
                        {{ $rekap->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
