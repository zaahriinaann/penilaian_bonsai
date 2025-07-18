@extends('layouts.app')

@section('title', 'Detail Nilai Bonsai')

@section('content')
    <div class="container py-4">
        <div class="card shadow-sm rounded-4">
            <div class="card-header">
                <h4 class="mb-0">Detail Nilai Bonsai</h4>
            </div>
            <div class="card-body">
                <table class="table table-bordered mb-4">
                    <tr>
                        <th width="200">Nama Bonsai</th>
                        <td>{{ $detail['nama_pohon'] }}</td>
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

                <h5>Detail Per Kategori</h5>
                @forelse ($detail['kategori'] as $kriteria => $list)
                    @php
                        $rata2 = collect($list)->avg('hasil');
                        $himpunanCount = collect($list)->countBy('himpunan');
                        $mayoritas = $himpunanCount->sortDesc()->keys()->first();
                    @endphp
                    <div class="border rounded p-3 mb-3">
                        <strong>{{ $kriteria }}</strong><br>
                        Rata-rata Defuzzifikasi: <strong>{{ number_format($rata2, 2) }}</strong><br>
                        Mayoritas Himpunan: <em>{{ $mayoritas }}</em>
                    </div>
                @empty
                    <div class="alert alert-warning">Data kategori tidak ditemukan.</div>
                @endforelse

                {{-- <a href="{{ route('rekap.index') }}" class="btn btn-secondary mt-3">← Kembali</a> --}}
            </div>
        </div>
    </div>
@endsection
