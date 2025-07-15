@extends('layouts.app')

@section('title', 'Hasil Penilaian Saya')

@section('content')
    <div class="container py-4">
        <h2 class="mb-4">Detail Penilaian untuk Bonsai: {{ $bonsai->nama_bonsai }}</h2>

        <div class="card mb-4">
            <div class="card-body">
                <p><strong>Jenis:</strong> {{ $bonsai->jenis }}</p>
                <p><strong>Pemilik:</strong> {{ $bonsai->user->name }}</p>
                <p><strong>Asal:</strong> {{ $bonsai->asal }}</p>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">Nilai Awal yang Diinput</div>
            <div class="card-body p-0">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Sub Kriteria</th>
                            <th>Nilai Awal</th>
                            <th>Himpunan</th>
                            <th>µ (Derajat Keanggotaan)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $i = 1; @endphp
                        @foreach ($nilaiAwal as $nilai)
                            <tr>
                                <td>{{ $i++ }}</td>
                                <td>{{ $nilai->sub_kriteria }}</td>
                                <td>{{ $nilai->nilai_awal }}</td>
                                <td>{{ $nilai->himpunan }}</td>
                                <td>{{ $nilai->derajat_anggota }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-dark text-white">Hasil Defuzzifikasi (Saya)</div>
            <div class="card-body p-0">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Skor Defuzzifikasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($nilaiPerJuri as $i => $item)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td><strong>{{ $item->hasil_defuzzifikasi }}</strong></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center">Belum ada hasil fuzzy.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <a href="{{ route('nilai.index') }}" class="btn btn-secondary mt-4">Kembali ke Daftar</a>
    </div>
@endsection
