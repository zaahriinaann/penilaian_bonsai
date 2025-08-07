@extends('layouts.app')
@section('title', "Peringkat Kontes • {$kontes->nama_kontes}")

@section('content')
    <div class="container py-4">
        <div class="card shadow-sm rounded-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>Peringkat Peserta: {{ $kontes->nama_kontes }}</h3>
                <a href="{{ route('admin.riwayat.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                @if ($rekapList->isEmpty())
                    <div class="alert alert-warning">Belum ada data peringkat.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Peringkat</th>
                                    <th>No Pendaftaran</th>
                                    <th>No Juri</th>
                                    <th>Nama Bonsai</th>
                                    <th>Pemilik</th>
                                    <th>Skor Akhir</th>
                                    <th>Himpunan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rekapList as $i => $item)
                                    @php
                                        $pd = $item->bonsai->pendaftaranKontes;
                                        $usr = optional($pd)->user;
                                    @endphp
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $item->peringkat }}</td>
                                        <td>{{ $pd->nomor_pendaftaran ?? '-' }}</td>
                                        <td>{{ $pd->nomor_juri ?? '-' }}</td>
                                        <td>{{ $item->bonsai->nama_pohon }}</td>
                                        <td>{{ $usr->name ?? '-' }}</td>
                                        <td>{{ number_format($item->skor_akhir, 2) }}</td>
                                        <td>{{ $item->himpunan_akhir }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
