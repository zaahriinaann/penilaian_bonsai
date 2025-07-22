@extends('layouts.app')

@section('title', 'Hasil Penilaian Saya')

@section('content')
    <div class="container py-4">

        {{-- Informasi Bonsai --}}
        <div class="card shadow-sm rounded-4 mb-4">
            <div class="card-header rounded-top-4 align-items-center">
                <strong>Hasil Penilaian Bonsai - {{ $bonsai->nama_pohon }}</strong><br>
                <small>Nomor Juri: {{ $pendaftaran->nomor_juri ?? '-' }}</small>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-md-6">
                        <strong>No Daftar:</strong> {{ $pendaftaran->nomor_pendaftaran ?? '-' }}
                    </div>
                    <div class="col-md-6">
                        <strong>Nama Pemilik:</strong> {{ $bonsai->user->name }}
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
                        <strong>No HP Pemilik:</strong> {{ $bonsai->user->no_hp }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Nilai Awal Dikelompokkan --}}
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

        {{-- Hasil Defuzzifikasi --}}
        <div class="card shadow-sm rounded-4 mt-4">
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
                        @foreach ($defuzzMap as $idKriteria => $row)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                {{-- Nama kriteria via helperDomain --}}
                                <td>{{ $row->helperDomain->kriteria ?? 'Kriteria ' . $idKriteria }}</td>
                                <td>{{ $row->hasil_himpunan }}</td>
                                <td><strong>{{ number_format($row->hasil_defuzzifikasi, 2) }}</strong></td>
                            </tr>
                        @endforeach

                        @if ($defuzzMap->isEmpty())
                            <tr>
                                <td colspan="4" class="text-center">Belum ada hasil defuzzifikasi.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Rule Inferensi Aktif --}}
        <div class="card shadow-sm rounded-4 mt-4">
            <div class="card-header rounded-top-4 align-items-center">
                <strong>🔥 Rule Inferensi Aktif</strong>
            </div>
            <div class="card-body">
                @php use Illuminate\Support\Str; @endphp

                @forelse($ruleAktif as $idKriteria => $rules)
                    <div class="mb-4 p-3 border rounded shadow-sm bg-light">
                        {{-- Ambil nama kriteria dari defuzzMap --}}
                        <strong>
                            {{ $defuzzMap[$idKriteria]->helperDomain->kriteria ?? 'Kriteria ' . $idKriteria }}
                        </strong>
                        <ol class="mt-2">
                            @foreach ($rules as $index => $hasil)
                                @php
                                    $details = $hasil->rule->details;
                                    $antecedents = $details
                                        ->map(fn($d) => "{$d->input_variable} {$d->himpunan}")
                                        ->implode(' and ');
                                    $symbols = $details
                                        ->map(
                                            fn($d) => 'μ' .
                                                strtoupper(Str::substr($d->input_variable, 0, 1)) .
                                                $d->himpunan,
                                        )
                                        ->implode('; ');
                                    $values = $details
                                        ->map(
                                            fn($d) => ($m = $nilaiAwal->firstWhere('sub_kriteria', $d->input_variable))
                                                ? round($m->derajat_anggota, 2)
                                                : '0',
                                        )
                                        ->implode('; ');
                                    $alpha = round($hasil->alpha, 3);
                                    $z = round($hasil->z_value, 2);
                                @endphp

                                <li class="mb-2">
                                    <strong>Rule {{ $index + 1 }}:</strong>
                                    If {{ $antecedents }} then {{ $hasil->rule->output_himpunan }}<br>
                                    a-predikat = {{ $symbols }} = Min({{ $values }}) =
                                    <strong>{{ $alpha }}</strong><br>
                                    z = <strong>{{ $z }}</strong>
                                </li>
                            @endforeach
                        </ol>
                    </div>
                @empty
                    <p class="text-muted">Tidak ada rule aktif untuk bonsai ini.</p>
                @endforelse
            </div>
        </div>


        {{-- Proses Agregasi & Defuzzifikasi --}}
        <div class="card shadow-sm rounded-4 mt-4">
            <div class="card-header rounded-top-4 align-items-center">
                <strong>🧠 Proses Agregasi & Defuzzifikasi</strong>
            </div>
            <div class="card-body">
                @forelse($hasilAgregasi as $idKriteria => $items)
                    @php
                        $sumAlphaZ = 0;
                        $sumAlpha = 0;
                    @endphp
                    <div class="mb-4 p-3 border rounded shadow-sm bg-light">
                        {{-- Nama Kriteria --}}
                        <strong>
                            {{ $defuzzMap[$idKriteria]->helperDomain->kriteria ?? 'Kriteria ' . $idKriteria }}
                        </strong>

                        {{-- Rincian α×z --}}
                        <ol class="mt-2">
                            @foreach ($items as $i => $item)
                                @php
                                    $alpha = round($item->alpha, 3);
                                    $z = round($item->z_value, 2);
                                    $product = round($alpha * $z, 2);
                                    $sumAlphaZ += $product;
                                    $sumAlpha += $alpha;
                                @endphp
                                <li>
                                    α{{ $i + 1 }} × z{{ $i + 1 }} = {{ $alpha }} ×
                                    {{ $z }} =
                                    <strong>{{ $product }}</strong>
                                </li>
                            @endforeach
                        </ol>

                        {{-- Hasil Akhir z_final --}}
                        <div class="mt-2">
                            ∑(α × z) = <strong>{{ round($sumAlphaZ, 2) }}</strong>,
                            ∑α = <strong>{{ round($sumAlpha, 3) }}</strong><br>
                            z_final =
                            <strong>
                                {{ number_format($defuzzMap[$idKriteria]->hasil_defuzzifikasi, 2) }}
                            </strong>
                        </div>
                    </div>
                @empty
                    <p class="text-muted">Tidak ada proses agregasi ditemukan.</p>
                @endforelse
            </div>
        </div>


        {{-- Tombol Kembali --}}
        <div class="text-end mt-4">
            <a href="{{ route('juri.nilai.index') }}" class="btn btn-secondary rounded-pill">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
            </a>
        </div>

    </div>
@endsection
