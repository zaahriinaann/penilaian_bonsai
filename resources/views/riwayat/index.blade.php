@extends('layouts.app')
@section('title', 'Riwayat Penilaian - Daftar Kontes')

@section('content')
    <div class="card">
        <div class="card-header">
            <h4>Daftar Kontes</h4>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Nama Kontes</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($kontes as $k)
                        <tr>
                            <td>{{ $k->nama_kontes }}</td>
                            <td>{{ $k->tanggal_mulai_kontes }} sd. {{ $k->tanggal_selesai_kontes }}</td>
                            <td>
                                <a href="{{ route('riwayat.show', $k->id) }}" class="btn btn-primary">Lihat Bonsai</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
