@extends('layouts.app')

@section('title', 'Daftar Juri Aktif')

@section('content')
    <div class="container py-4">
        <div class="card shadow-sm rounded-4">
            <div class="card-header rounded-top-4 align-items-center">
                <h3>Daftar Juri yang Aktif</h3>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nama Juri</th>
                            <th>Email</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($juriAktif as $juri)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $juri->user->name }}</td>
                                <td>{{ $juri->user->email }}</td>
                                <td>
                                    <a href="{{ route('admin.nilai.show', $juri->id) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Lihat Peserta
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        @if ($juriAktif->isEmpty())
                            <tr>
                                <td colspan="3" class="text-center text-muted">Belum ada juri yang terdaftar.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
