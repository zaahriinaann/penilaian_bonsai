@extends('layouts.app')

@section('title', 'Rekap Nilai Bonsai')

@section('button-toolbar')
    @if (isset($kontes))
        <a href="{{ route('rekap.cetak', $kontes->id) }}" target="_blank" class="btn btn-danger btn-sm rounded-pill">
            <i class="fas fa-print"></i> Cetak Laporan Rekap
        </a>
    @endif
@endsection

@section('content')
    <div class="container-fluid">

        {{-- Tabel 10 Besar --}}
        <div class="card mb-4 shadow-sm rounded-4">
            <div class="card-header align-items-center d-flex">
                <h4 class="mb-0">🏆 10 Besar Nilai Bonsai</h4>
            </div>
            <div class="card-body table-responsive">
                @if (!empty($bestTen) && count($bestTen))
                    <table class="table table-hover table-bordered text-nowrap" id="top-ten-table">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>No Juri</th>
                                <th>No Pendaftaran</th>
                                <th>Nama Pohon</th>
                                <th>Pemilik</th>
                                <th>Skor Akhir</th>
                                <th>Himpunan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bestTen as $i => $b)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $b['nomor_juri'] }}</td>
                                    <td>{{ $b['nomor_pendaftaran'] }}</td>
                                    <td>{{ $b['nama_pohon'] }}</td>
                                    <td>{{ $b['pemilik'] }}</td>
                                    <td>{{ number_format($b['skor_akhir'], 2) }}</td>
                                    <td>{{ $b['himpunan_akhir'] }}</td>
                                    <td>
                                        <a href="{{ route('rekap.show', ['nama_pohon' => urlencode($b['nama_pohon']), 'nomor_juri' => $b['nomor_juri']]) }}"
                                            class="btn btn-sm btn-info">Detail</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="alert alert-warning mb-0">Belum ada data 10 besar yang tersedia.</div>
                @endif
            </div>
        </div>

        {{-- Tabel Rekap Semua Peserta --}}
        <div class="card shadow-sm rounded-4">
            <div class="card-header align-items-center d-flex">
                <h4 class="mb-0">📊 Rekap Nilai Semua Peserta</h4>
            </div>
            <div class="card-body table-responsive">
                @if (!empty($rekapSorted) && count($rekapSorted))
                    <table class="table table-hover table-bordered text-nowrap" id="rekap-table">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>No Juri</th>
                                <th>No Pendaftaran</th>
                                <th>Nama Pohon</th>
                                <th>Pemilik</th>
                                <th>Skor Akhir</th>
                                <th>Himpunan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rekapSorted as $i => $b)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $b['nomor_juri'] }}</td>
                                    <td>{{ $b['nomor_pendaftaran'] }}</td>
                                    <td>{{ $b['nama_pohon'] }}</td>
                                    <td>{{ $b['pemilik'] }}</td>
                                    <td>{{ number_format($b['skor_akhir'], 2) }}</td>
                                    <td>{{ $b['himpunan_akhir'] }}</td>
                                    <td>
                                        <a href="{{ route('rekap.show', ['nama_pohon' => urlencode($b['nama_pohon']), 'nomor_juri' => $b['nomor_juri']]) }}"
                                            class="btn btn-sm btn-info">Detail</a>
                                        <a href="{{ route('rekap.cetak-per-bonsai', $b['id']) }}"
                                            class="btn btn-danger btn-sm" target="_blank">PDF</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="alert alert-warning mb-0">Belum ada data rekap nilai yang tersedia.</div>
                @endif
            </div>
        </div>
    </div>
@endsection
