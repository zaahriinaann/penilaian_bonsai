@extends('layouts.app')

@section('title', 'Detail Penilaian Juri')


@section('content')
    <div class="container py-4">
        {{-- Informasi Bonsai --}}
        <div class="card shadow-sm rounded-4 mb-4">
            <div class="card-header bg-secondary rounded-top-4 align-items-center">
                <strong>Penilaian oleh: {{ $juri->user->name }}</strong><br>
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

        {{-- Rule Aktif --}}
        <div class="card shadow-sm rounded-4 mb-4">
            <div class="card-header rounded-top-4 align-items-center">
                <strong>🔥 Rule Inferensi Aktif</strong>
            </div>
            <div class="card-body">
                @forelse($ruleAktif as $idKriteria => $rules)
                    <div class="mb-4 p-3 border rounded bg-light">
                        <strong>Kriteria ID: {{ $idKriteria }}</strong>
                        <ol class="mt-2">
                            @foreach ($rules as $index => $hasil)
                                @php
                                    $details = $hasil->rule->details;
                                    $antecedents = $details
                                        ->map(fn($d) => $d->input_variable . ' ' . $d->himpunan)
                                        ->implode(' and ');
                                    $symbols = $details
                                        ->map(function ($d) {
                                            $abbr = implode(
                                                '',
                                                array_map(fn($w) => substr($w, 0, 1), explode(' ', $d->input_variable)),
                                            );
                                            return 'μ' . strtoupper($abbr) . $d->himpunan;
                                        })
                                        ->implode('; ');
                                    $values = $details
                                        ->map(function ($d) use ($nilaiAwal) {
                                            $match = $nilaiAwal->firstWhere('sub_kriteria', $d->input_variable);
                                            return $match ? round($match->derajat_anggota, 2) : '0';
                                        })
                                        ->implode('; ');
                                    $alpha = round($hasil->alpha, 3);
                                    $z = round($hasil->z_value, 2);
                                @endphp
                                <li class="mb-2">
                                    <strong>Rule {{ $index + 1 }}:</strong>
                                    If {{ $antecedents }} then Penampilan
                                    <strong>{{ $hasil->rule->output_himpunan }}</strong><br>
                                    a-predikat{{ $index + 1 }} = {{ $symbols }} = Min ({{ $values }}) =
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

        {{-- Agregasi --}}
        <div class="card shadow-sm rounded-4 mb-4">
            <div class="card-header rounded-top-4 align-items-center">
                <strong>🧠 Proses Agregasi & Defuzzifikasi</strong>
            </div>
            <div class="card-body">
                @forelse($hasilAgregasi as $idKriteria => $items)
                    @php
                        $sumAlphaZ = 0;
                        $sumAlpha = 0;
                    @endphp
                    <div class="mb-4 p-3 border rounded bg-light">
                        <strong>Kriteria ID: {{ $idKriteria }}</strong>
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
                                    Rule z{{ $i + 1 }} = {{ $z }}, α{{ $i + 1 }} =
                                    {{ $alpha }} → α × z = <strong>{{ $product }}</strong>
                                </li>
                            @endforeach
                        </ol>
                        <div class="mt-2">
                            ∑(α × z) = <strong>{{ round($sumAlphaZ, 2) }}</strong>,
                            ∑α = <strong>{{ round($sumAlpha, 3) }}</strong><br>
                            z_final = <strong>{{ $sumAlpha > 0 ? round($sumAlphaZ / $sumAlpha, 2) : '0.00' }}</strong>
                        </div>
                    </div>
                @empty
                    <p class="text-muted">Tidak ada proses agregasi ditemukan.</p>
                @endforelse
            </div>
        </div>

        {{-- Tombol Kembali --}}
        <div class="text-end mt-4">
            <a href="{{ route('juri.riwayat.peserta', [$kontes->id, $juri->id]) }}" class="btn btn-secondary rounded-pill">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Peserta
            </a>
        </div>
    </div>
@endsection
