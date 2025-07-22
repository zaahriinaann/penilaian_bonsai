@extends('layouts.app')

@section('title', 'Hasil Penilaian Saya')

@section('content')
    <div class="container py-4">
        {{-- Informasi Bonsai --}}
        <div class="card shadow-sm rounded-4 mb-4">
            <div class="card-header bg-secondary rounded-top-4 align-items-center">
                <strong>Hasil Penilaian oleh: {{ Auth::user()->name }}</strong>
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
                    <div class="col-md-6">
                        <strong>Kelas:</strong> {{ $bonsai->kelas }}
                    </div>
                    <div class="col-md-6">
                        <strong>No HP Pemilik:</strong> {{ $bonsai->user->no_hp }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Nilai Awal yang Diinput --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 align-items-center">
                <strong>Nilai Awal yang Diinput</strong>
            </div>
            <div class="card-body table-responsive">
                @php
                    $grouped = $nilaiAwal->groupBy('kriteria');
                    $num = 1;
                @endphp
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
                        @foreach ($grouped as $kriteria => $list)
                            @php
                                $subGroups = $list->groupBy('sub_kriteria');
                                $rowspan = $subGroups->sum->count();
                                $firstK = true;
                            @endphp
                            @foreach ($subGroups as $sub => $items)
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

        {{-- Hasil Defuzzifikasi --}}
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
                            <th>Himpunan Akhir</th>
                            <th>Skor Defuzzifikasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($defuzzMap as $idK => $row)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $row->helperDomain->kriteria ?? 'Kriteria ' . $idK }}</td>
                                <td>{{ $row->himpunan_akhir }}</td>
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
        <div class="card shadow-sm rounded-4 mb-4">
            <div class="card-header rounded-top-4 align-items-center">
                <strong>🔥 Rule Inferensi Aktif</strong>
            </div>
            <div class="card-body">
                @php use Illuminate\Support\Str; @endphp

                @forelse($ruleAktif as $idKriteria => $rules)
                    <div class="mb-4 p-3 border rounded bg-light">
                        {{-- Nama Kriteria --}}
                        <strong>
                            {{ $defuzzMap[$idKriteria]->helperDomain->kriteria ?? 'Kriteria ' . $idKriteria }}
                        </strong>

                        <ol class="mt-2">
                            @foreach ($rules as $index => $hasil)
                                @php
                                    $details = $hasil->rule->details;
                                    // Susun antecedents (If … and …)
                                    $antecedents = $details
                                        ->map(fn($d) => "{$d->input_variable} {$d->himpunan}")
                                        ->implode(' and ');
                                    // Susun simbol keanggotaan (μ…)
                                    $symbols = $details
                                        ->map(
                                            fn($d) => 'μ' .
                                                strtoupper(Str::substr($d->input_variable, 0, 1)) .
                                                $d->himpunan,
                                        )
                                        ->implode('; ');
                                    // Ambil derajat keanggotaan penuh tanpa pembulatan
                                    $values = $details
                                        ->map(
                                            fn($d) => optional(
                                                $nilaiAwal->firstWhere('sub_kriteria', $d->input_variable),
                                            )->derajat_anggota ?? 0,
                                        )
                                        ->implode('; ');
                                    // alpha dan z dibulatkan
                                    $alpha = round($hasil->alpha, 3);
                                    $z = round($hasil->z_value, 2);
                                @endphp

                                <li class="mb-2">
                                    <strong>Rule {{ $index + 1 }}:</strong><br>
                                    If {{ $antecedents }} then
                                    <strong>{{ $hasil->rule->output_himpunan }}</strong><br>

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


        {{-- Proses Agregasi --}}
        <div class="card shadow-sm rounded-4 mb-4">
            <div class="card-header rounded-top-4 align-items-center">
                <strong>🧠 Proses Agregasi & Defuzzifikasi</strong>
            </div>
            <div class="card-body">
                @forelse($hasilAgregasi as $idK => $items)
                    @php
                        $sumAZ = 0;
                        $sumA = 0;
                    @endphp
                    <div class="mb-4 p-3 border rounded bg-light">
                        <strong>
                            {{ $defuzzMap[$idK]->helperDomain->kriteria ?? 'Kriteria ' . $idK }}
                        </strong>
                        <ol class="mt-2">
                            @foreach ($items as $i => $it)
                                @php
                                    $a = round($it->alpha, 3);
                                    $z = round($it->z_value, 2);
                                    $p = round($a * $z, 2);
                                    $sumAZ += $p;
                                    $sumA += $a;
                                @endphp
                                <li>α{{ $i + 1 }}×z{{ $i + 1 }} = {{ $a }}×{{ $z }}
                                    = <strong>{{ $p }}</strong></li>
                            @endforeach
                        </ol>
                        <div class="mt-2">
                            ∑(α×z)=<strong>{{ round($sumAZ, 2) }}</strong>,
                            ∑α=<strong>{{ round($sumA, 3) }}</strong><br>
                            z_final=<strong>{{ number_format($defuzzMap[$idK]->hasil_defuzzifikasi, 2) }}</strong>
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
