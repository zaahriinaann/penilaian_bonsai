@extends('layouts.app')

@section('title', 'Detail Nilai Akhir Bonsai')

@section('button-toolbar')
    <a href="{{ route('rekap.cetak-per-bonsai', $detail['id']) }}" class="btn btn-danger btn-sm" target="_blank">
        <i class="fas fa-print"></i> Cetak
    </a>
@endsection

@section('content')
    <div class="container py-4">
        <div class="card shadow-sm rounded-4">
            <div class="card-header align-items-center">
                <h3 class="mb-0">Detail Nilai Akhir Bonsai</h3>
            </div>
            <div class="card-body">
                {{-- Informasi utama bonsai --}}
                <table class="table table-bordered mb-4">
                    <tr>
                        <th width="200">Nama Bonsai</th>
                        <td>{{ $detail['nama_pohon'] }}</td>
                    </tr>
                    <tr>
                        <th>Kelas / Ukuran</th>
                        <td>{{ $detail['kelas'] }} / {{ $detail['ukuran_2'] }}</td>
                    </tr>
                    <tr>
                        <th>No Juri</th>
                        <td>{{ $detail['nomor_juri'] }}</td>
                    </tr>
                    <tr>
                        <th>No Pendaftaran</th>
                        <td>{{ $detail['nomor_pendaftaran'] }}</td>
                    </tr>
                    <tr>
                        <th>Pemilik</th>
                        <td>{{ $detail['pemilik'] }}</td>
                    </tr>
                    <tr>
                        <th>Skor Akhir</th>
                        <td><strong>{{ number_format($detail['skor_akhir'], 2) }}</strong></td>
                    </tr>
                    <tr>
                        <th>Himpunan Akhir</th>
                        <td><strong>{{ $detail['himpunan_akhir'] }}</strong></td>
                    </tr>
                </table>

                {{-- Rincian nilai per kategori --}}
                <h5>Detail Per Kategori</h5>
                <hr>

                @forelse ($detail['kategori'] as $kriteria => $data)
                    <div class="border rounded p-3 mb-3">
                        <strong>{{ $kriteria }}</strong><br>
                        Rata-rata Defuzzifikasi: <strong>{{ number_format($data['rata_defuzzifikasi'], 2) }}</strong><br>
                        Himpunan: <em>{{ $data['rata_himpunan'] }}</em>
                    </div>
                @empty
                    <div class="alert alert-warning">Data kategori tidak ditemukan.</div>
                @endforelse

                {{-- Tombol kembali opsional --}}
                {{-- <a href="{{ route('rekap.index') }}" class="btn btn-secondary mt-3">← Kembali</a> --}}
            </div>
        </div>
    </div>
@endsection
