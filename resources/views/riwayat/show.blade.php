@extends('layouts.app')
@section('title', 'Riwayat Penilaian - Bonsai Kontes')

@section('content')
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Daftar Bonsai - {{ $kontes->nama }}</h4>
            <a href="{{ route('riwayat.index') }}" class="btn btn-secondary">Kembali ke Kontes</a>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Nama Bonsai</th>
                        <th>Kelas</th>
                        <th>Pemilik</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bonsai as $b)
                        <tr>
                            <td>{{ $b->nama_pohon }}</td>
                            <td>{{ $b->kelas }}</td>
                            <td>{{ $b->user->name ?? '-' }}</td>
                            <td>
                                <a href="{{ route('riwayat.detail', [$kontes->id, $b->id]) }}" class="btn btn-info">Lihat
                                    Detail</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
