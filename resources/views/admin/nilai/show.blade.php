@extends('layouts.app')

@section('title', 'Peserta yang Dinilai oleh Juri')

@section('content')
    <div class="container py-4">
        <div class="card shadow-sm rounded-4">
            <div class="card-header bg-secondary text-white rounded-top-4">
                <strong>Daftar Peserta yang Dinilai oleh {{ $juri->user->name }}</strong>
            </div>
            <div class="card-body table-responsive">
                @if ($pendaftarans->isEmpty())
                    <div class="alert alert-warning">Belum ada peserta yang dinilai oleh juri ini.</div>
                @else
                    <table class="table table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No Daftar</th>
                                <th>Nama Peserta</th>
                                <th>Bonsai</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pendaftarans as $item)
                                <tr>
                                    <td>{{ $item->nomor_pendaftaran }}</td>
                                    <td>{{ $item->user->name }}</td>
                                    <td>{{ $item->bonsai->nama_pohon }}</td>
                                    <td>
                                        <a href="{{ route('admin.nilai.detail', [$juri->id, $item->bonsai_id]) }}"
                                            class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-eye"></i> Lihat Nilai
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        {{-- Tombol Kembali ke daftar juri --}}
        <div class="text-end mt-3">
            <a href="{{ route('admin.nilai.index') }}" class="btn btn-secondary rounded-pill">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Juri
            </a>
        </div>
    </div>
@endsection
