@extends('layouts.app')
@section('title', 'Detail Nilai Saya')

@section('button-toolbar')
    {{-- Tombol Rekap Nilai di Atas --}}
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('rekap.show', $bonsai->id, Auth::id()) }}" class="btn btn-sm btn-info">Detail</a>
        {{-- <a href="{{ route('riwayat.rekap', $bonsai->id) }}" class="btn btn-primary btn-sm">
            <i class="fas fa-eye"></i> Lihat Rekap Nilai
        </a> --}}
    </div>
@endsection

@section('content')
    <div class="container py-4">
        <div class="card shadow-sm rounded-4 mb-4">
            <div class="card-header bg-secondary  rounded-top-4 align-items-center">
                <strong>Hasil Penilaian Bonsai - {{ $bonsai->nama_pohon }}</strong>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-md-6">
                        <strong>Nomor Juri:</strong> {{ $pendaftaran->nomor_juri ?? '-' }}
                    </div>
                    <div class="col-md-6">
                        <strong>No Daftar:</strong> {{ $pendaftaran->nomor_pendaftaran ?? '-' }}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-6">
                        <strong>Ukuran Bonsai:</strong> {{ $bonsai->ukuran_2 }} ({{ $bonsai->ukuran }})
                    </div>
                    <div class="col-md-6">
                        <strong>Pemilik:</strong> {{ $bonsai->user->name }}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-6"><strong>Kelas:</strong> {{ $bonsai->kelas }}</div>
                    <div class="col-md-6"><strong>No Hp:</strong> {{ $bonsai->user->no_hp }}</div>
                </div>
            </div>
        </div>

        {{-- Nilai Awal --}}
        @php
            $grouped = $nilaiAwal->groupBy('kriteria');
            $num = 1;
        @endphp
        <div class="card mb-4">
            <div class="card-header rounded-top-4 align-items-center">
                <strong>Nilai Awal yang Diinput</strong>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Kriteria</th>
                            <th>Sub Kriteria</th>
                            <th>Nilai Awal</th>
                            <th>Himpunan</th>
                            <th>µ (Derajat Keanggotaan)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($grouped as $kriteria => $subList)
                            @php
                                $subGrouped = $subList->groupBy('sub_kriteria');
                                $rowspanKriteria = $subGrouped->reduce(fn($carry, $g) => $carry + $g->count(), 0);
                                $printedKriteria = false;
                            @endphp
                            @foreach ($subGrouped as $sub => $items)
                                @php $printedSub = false; @endphp
                                @foreach ($items as $item)
                                    <tr>
                                        @if (!$printedKriteria)
                                            <td rowspan="{{ $rowspanKriteria }}">{{ $num++ }}</td>
                                            <td rowspan="{{ $rowspanKriteria }}">{{ $kriteria }}</td>
                                            @php $printedKriteria = true; @endphp
                                        @endif
                                        @if (!$printedSub)
                                            <td rowspan="{{ $items->count() }}">{{ $sub }}</td>
                                            <td rowspan="{{ $items->count() }}">{{ $item->nilai_awal }}</td>
                                            @php $printedSub = true; @endphp
                                        @endif
                                        <td>{{ $item->himpunan }}</td>
                                        <td>{{ $item->derajat_anggota }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Defuzzifikasi --}}
        <div class="card shadow-sm rounded-4 mb-4">
            <div class="card-header rounded-top-4 align-items-center">
                <strong>Hasil Defuzzifikasi</strong>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Kriteria</th>
                            <th>Himpunan</th>
                            <th>Skor Defuzzifikasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($defuzzifikasiPerKriteria as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->nama_kriteria }}</td>
                                <td>{{ $item->hasil_himpunan }}</td>
                                <td><strong>{{ $item->hasil_defuzzifikasi }}</strong></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Belum ada hasil defuzzifikasi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
