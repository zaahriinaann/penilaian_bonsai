@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <h4 class="mb-1 text-dark">Detail Penilaian Bonsai</h4>
        <p class="text-muted">
            <strong>Kontes:</strong> {{ $kontes->nama_kontes }}<br>
            <strong>Bonsai:</strong> {{ $bonsai->nama_pohon }} oleh <strong>{{ $bonsai->user->name }}</strong>
        </p>

        @role('admin')
            {{-- Informasi Pendaftaran --}}
            <div class="border rounded mb-4 p-3 bg-white shadow-sm">
                <h6 class="mb-3 border-bottom pb-2">Informasi Pendaftaran</h6>
                <div class="row mb-2">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Kelas:</strong> {{ $bonsai->kelas ?? '-' }}</p>
                        <p class="mb-1"><strong>Ukuran:</strong> {{ $bonsai->ukuran_1 }} × {{ $bonsai->ukuran_2 }}
                            ({{ $bonsai->format_ukuran }})</p>
                        <p class="mb-1"><strong>Nomor Induk:</strong> {{ $bonsai->no_induk_pohon ?? '-' }}</p>
                        <p class="mb-1"><strong>Masa Pemeliharaan:</strong> {{ $bonsai->masa_pemeliharaan }}
                            {{ $bonsai->format_masa }}</p>
                        <p class="mb-1"><strong>Nomor Peserta:</strong> {{ $pendaftaran->nomor_pendaftaran ?? '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Daftar Juri Aktif:</strong></p>
                        <ul class="mb-0 ps-3">
                            @forelse ($juriList as $juri)
                                <li>{{ $juri->nama_juri }}</li>
                            @empty
                                <li class="text-muted">Belum ada juri terdaftar.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Penilaian per Juri --}}
            @forelse ($penilaian as $juriNama => $nilaiPerKriteria)
                <div class="border rounded mb-4 p-3 bg-white shadow-sm">
                    <h6 class="mb-3 border-bottom pb-2">Penilaian oleh: {{ $juriNama }}</h6>
                    @foreach ($nilaiPerKriteria as $kriteria => $nilaiList)
                        <p class="fw-semibold mt-3 mb-2">{{ $kriteria }}</p>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Subkriteria</th>
                                        <th>Nilai</th>
                                        <th>Himpunan</th>
                                        <th>Derajat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($nilaiList as $nilai)
                                        <tr>
                                            <td>{{ $nilai->subKriteria->sub_kriteria ?? '-' }}</td>
                                            <td>{{ $nilai->nilai }}</td>
                                            <td>{{ $nilai->subKriteria->himpunan->nama ?? '-' }}</td>
                                            <td>{{ number_format($nilai->derajat, 3) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach

                    {{-- Defuzzifikasi --}}
                    @if (!empty($defuzz[$juriNama]))
                        <p class="fw-semibold mt-4 mb-2">Hasil Defuzzifikasi</p>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Kriteria</th>
                                        <th>Skor Akhir</th>
                                        <th>Himpunan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($defuzz[$juriNama] as $d)
                                        <tr>
                                            <td>{{ $d->kriteria->kriteria }}</td>
                                            <td>{{ number_format($d->skor_akhir, 2) }}</td>
                                            <td>{{ $d->himpunan->nama ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @empty
                <div class="alert alert-light border text-muted">Belum ada penilaian dari juri.</div>
            @endforelse

            {{-- Hasil Gabungan --}}
            <div class="border rounded p-3 bg-white shadow-sm">
                <h6 class="mb-3 border-bottom pb-2">Hasil Gabungan Semua Juri</h6>

                <p class="fw-semibold">Rata-rata per Kriteria</p>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Kriteria</th>
                                <th>Skor Rata-rata</th>
                                <th>Himpunan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($hasilRata as $hasil)
                                <tr>
                                    <td>{{ $hasil->kriteria->kriteria }}</td>
                                    <td>{{ number_format($hasil->skor_rata, 2) }}</td>
                                    <td>{{ $hasil->himpunan->nama ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Belum ada hasil rata-rata.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <p class="fw-semibold mt-4">Rekap Nilai Akhir</p>
                @if ($rekap)
                    <table class="table table-sm table-bordered w-auto">
                        <tr>
                            <th>Skor Akhir</th>
                            <td>{{ number_format($rekap->skor_akhir, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Himpunan Akhir</th>
                            <td>{{ $rekap->himpunan->nama ?? '-' }}</td>
                        </tr>
                    </table>
                @else
                    <p class="text-muted">Belum ada hasil akhir.</p>
                @endif
            </div>
        @endrole
    </div>
@endsection
