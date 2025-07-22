@extends('layouts.app')

@section('title', 'Detail Penilaian Juri')

@section('content')
    <div class="container py-4">
        {{-- Informasi Bonsai --}}
        <div class="card shadow-sm rounded-4 mb-4">
            <div class="card-header bg-secondary rounded-top-4 align-items-center">
                <strong>Penilaian oleh: {{ $juri->user->name }}</strong><br>
                <small>Nomor Juri: {{ $pendaftaran->nomor_juri ?? '-' }}</small>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-md-6">
                        <strong>No Daftar:</strong> {{ $pendaftaran->nomor_pendaftaran ?? '-' }}
                    </div>
                    <div class="col-md-6">
                        <strong>Pemilik:</strong> {{ $bonsai->user->name }}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-6">
                        <strong>Ukuran Bonsai:</strong> {{ $bonsai->ukuran_2 }} ({{ $bonsai->ukuran }})
                    </div>
                    <div class="col-md-6">
                        <strong>Kelas:</strong> {{ $bonsai->kelas }}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-6">
                        <strong>No HP:</strong> {{ $bonsai->user->no_hp }}
                    </div>
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
                                $rowspan = $subGrouped->sum(fn($g) => $g->count());
                                $firstK = true;
                            @endphp
                            @foreach ($subGrouped as $sub => $items)
                                @php $firstS = true; @endphp
                                @foreach ($items as $item)
                                    <tr>
                                        @if ($firstK)
                                            <td rowspan="{{ $rowspan }}">{{ $num++ }}</td>
                                            <td rowspan="{{ $rowspan }}">{{ $kriteria }}</td>
                                            @php $firstK = false; @endphp
                                        @endif
                                        @if ($firstS)
                                            <td rowspan="{{ $items->count() }}">{{ $sub }}</td>
                                            <td rowspan="{{ $items->count() }}">{{ $item->nilai_awal }}</td>
                                            @php $firstS = false; @endphp
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
                <strong>Hasil Defuzzifikasi Per Juri</strong>
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
                        @foreach ($defuzzMap as $kId => $def)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $def->helperDomain->kriteria ?? 'Kriteria ' . $kId }}</td>
                                <td>{{ $def->hasil_himpunan }}</td>
                                <td><strong>{{ number_format($def->hasil_defuzzifikasi, 2) }}</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Rule Aktif --}}
        <div class="card shadow-sm rounded-4 mb-4">
            <div class="card-header rounded-top-4 align-items-center">
                <strong>🔥 Rule Inferensi Aktif Per Juri</strong>
            </div>
            <div class="card-body">
                @foreach ($ruleAktif as $kId => $rules)
                    <div class="mb-4 p-3 border rounded bg-light">
                        <strong>{{ $defuzzMap[$kId]->helperDomain->kriteria ?? 'Kriteria ' . $kId }}</strong>
                        <ol class="mt-2">
                            @foreach ($rules as $idx => $r)
                                @php
                                    $ant = $r->rule->details
                                        ->map(fn($d) => "{$d->input_variable} {$d->himpunan}")
                                        ->implode(' and ');
                                    $alpha = round($r->alpha, 3);
                                    $z = round($r->z_value, 2);
                                @endphp
                                <li><strong>Rule {{ $idx + 1 }}:</strong> If {{ $ant }} then
                                    <em>{{ $r->rule->output_himpunan }}</em><br>a-predikat = {{ $alpha }}; z =
                                    {{ $z }}</li>
                            @endforeach
                        </ol>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Agregasi --}}
        <div class="card shadow-sm rounded-4 mb-4">
            <div class="card-header rounded-top-4 align-items-center">
                <strong>🧠 Proses Agregasi & Defuzzifikasi Per Juri</strong>
            </div>
            <div class="card-body">
                @foreach ($hasilAgregasi as $kId => $items)
                    @php
                        $sumAZ = 0;
                        $sumA = 0;
                    @endphp
                    <div class="mb-4 p-3 border rounded bg-light">
                        <strong>{{ $defuzzMap[$kId]->helperDomain->kriteria ?? 'Kriteria ' . $kId }}</strong>
                        <ol class="mt-2">
                            @foreach ($items as $i => $it)
                                @php
                                    $a = round($it->alpha, 3);
                                    $z = round($it->z_value, 2);
                                    $p = $a * $z;
                                    $sumAZ += $p;
                                    $sumA += $a;
                                @endphp
                                <li>α{{ $i + 1 }}×z{{ $i + 1 }} = {{ $a }}×{{ $z }}
                                    = <strong>{{ round($p, 2) }}</strong></li>
                            @endforeach
                        </ol>
                        <div class="mt-2">
                            ∑(α×z)=<strong>{{ round($sumAZ, 2) }}</strong>, ∑α=<strong>{{ round($sumA, 3) }}</strong><br>
                            z_final=<strong>{{ $sumA > 0 ? round($sumAZ / $sumA, 2) : '0.00' }}</strong>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Tombol Kembali --}}
        <div class="text-end mt-4">
            <a href="{{ route('admin.riwayat.peserta', [$kontes->id, $juri->id]) }}"
                class="btn btn-secondary rounded-pill">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Peserta
            </a>
        </div>
    </div>
@endsection
