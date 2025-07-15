@extends('layouts.app')

@section('title', 'Hasil Penilaian Saya')

@section('content')
    <div class="container py-4">

        {{-- Informasi Bonsai --}}
        <div class="card shadow-sm rounded-4 mb-4">
            <div class="card-header bg-secondary  rounded-top-4 align-items-center">
                <strong>Hasil Penilaian Bonsai - {{ $bonsai->nama_pohon }}</strong>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-md-6">
                        <strong>Nomor Juri:</strong> {{ $bonsai->pendaftaranKontes->nomor_juri ?? '-' }}
                    </div>
                    <div class="col-md-6">
                        <strong>No Daftar:</strong> {{ $bonsai->pendaftaranKontes->nomor_pendaftaran ?? '-' }}
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

        {{-- Nilai Awal Dikelompokkan --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 align-items-center">
                <strong>Nilai Awal yang Diinput</strong>
            </div>
            <div class="card-body p-0">
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
                        @php
                            $grouped = $nilaiAwal->groupBy('kriteria');
                            $num = 1;
                        @endphp

                        @foreach ($grouped as $kriteria => $subList)
                            @php
                                $subGrouped = $subList->groupBy('sub_kriteria');
                                $rowspanKriteria = $subGrouped->reduce(fn($carry, $g) => $carry + $g->count(), 0);
                                $printedKriteria = false;
                            @endphp

                            @foreach ($subGrouped as $sub => $items)
                                @php $printedSub = false; @endphp
                                @foreach ($items as $index => $item)
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

        {{-- Hasil Defuzzifikasi --}}
        <div class="card shadow-sm rounded-4 mb-4">
            <div class="card-header rounded-top-4 align-items-center">
                <strong>Hasil Defuzzifikasi</strong>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered align-middle mb-0 text-center">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Kriteria</th>
                            <th>Skor Defuzzifikasi</th>
                            <th>Himpunan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($nilaiPerJuri as $i => $item)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $kriteria }}</td>
                                <td><strong>{{ $item->hasil_defuzzifikasi }}</strong></td>
                                <td>himpunan</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted">Belum ada hasil fuzzy.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tombol Kembali --}}
        <div class="text-end mt-4">
            <a href="{{ route('nilai.index') }}" class="btn btn-secondary rounded-pill">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
            </a>
        </div>

    </div>
@endsection
