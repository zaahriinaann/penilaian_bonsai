@extends('layouts.app')

@section('title', 'Dashboard Peserta')

@section('content')
    <div class="container py-4">

        {{-- Ringkasan Cepat --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="fw-semibold mb-3 text-center text-md-start">Ringkasan Anda</h5>
                <div class="row g-3 text-center">
                    <div class="col-12 col-md-6">
                        <div class="p-3 border rounded-3 bg-light">
                            <div class="fs-4 fw-bold">{{ $totalBonsai }}</div>
                            <div class="text-muted">Bonsai Terdaftar</div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="p-3 border rounded-3 bg-light">
                            <div class="fs-4 fw-bold">{{ $totalKontes }}</div>
                            <div class="text-muted">Kontes Diikuti</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Informasi Kontes Aktif --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white align-content-center">
                <strong>Kontes Aktif Saat Ini</strong>
            </div>
            <div class="card-body">
                @if ($kontesAktif)
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Nama Kontes: <strong>{{ $kontesAktif->nama_kontes }}</strong></li>
                        <li class="list-group-item">Tanggal:
                            <strong>{{ \Carbon\Carbon::parse($kontesAktif->tanggal_mulai_kontes)->format('d M Y') }} -
                                {{ \Carbon\Carbon::parse($kontesAktif->tanggal_selesai_kontes)->format('d M Y') }}</strong>
                        </li>
                        <li class="list-group-item">Lokasi: <strong>{{ $kontesAktif->tempat_kontes }}</strong></li>
                        <li class="list-group-item">Status: <span class="badge bg-success">Sedang Berlangsung</span></li>
                    </ul>
                @else
                    <p class="text-muted mb-0">Belum ada kontes aktif saat ini.</p>
                @endif
            </div>
        </div>

        {{-- Statistik Pendaftaran & Slot --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-secondary align-content-center">
                        <strong>Statistik Pendaftaran Anda</strong>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">Total Bonsai Terdaftar: <strong>{{ $totalBonsai }}</strong></li>
                            <li class="list-group-item">Kontes Diikuti: <strong>{{ $totalKontes }}</strong></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-secondary align-content-center">
                        <strong>Slot Kontes Aktif</strong>
                    </div>
                    <div class="card-body">
                        @if ($kontesAktif)
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">Total Slot: <strong>{{ $kontesAktif->slot_total }}</strong></li>
                                <li class="list-group-item">Slot Terisi: <strong>{{ $kontesAktif->slot_terisi }}</strong>
                                </li>
                                <li class="list-group-item">Sisa Slot:
                                    <strong>{{ $kontesAktif->slot_total - $kontesAktif->slot_terisi }}</strong>
                                </li>
                            </ul>
                        @else
                            <p class="text-muted mb-0">Tidak ada kontes aktif.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Grafik Bonsai Dinilai per Tahun --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white align-content-center">
                Grafik Bonsai Dinilai per Tahun
            </div>
            <div class="card-body">
                <canvas id="chartPenilaian" height="80"></canvas>
            </div>
        </div>

        {{-- Tren Skor Rata-rata per Kriteria --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white align-content-center">
                Tren Skor Rata-rata per Kriteria (5 Tahun Terakhir)
            </div>
            <div class="card-body">
                <canvas id="chartKategoriAnggota" height="80"></canvas>
            </div>
        </div>

        {{-- Daftar Bonsai --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white align-content-center">
                <strong>Daftar Bonsai Anda</strong>
            </div>
            <div class="card-body">
                @if ($bonsaiAnggota->isEmpty())
                    <p class="text-muted">Anda belum mendaftarkan bonsai untuk kontes mana pun.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Nama Pohon</th>
                                    <th>Kontes</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bonsaiAnggota as $i => $bonsai)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $bonsai->nama_pohon }}</td>
                                        <td>{{ $bonsai->pendaftaranKontes->kontes->nama_kontes }}</td>
                                        <td>{{ \Carbon\Carbon::parse($bonsai->pendaftaranKontes->kontes->tanggal_mulai_kontes)->format('d M Y') }}
                                        </td>
                                        <td>
                                            @if ($bonsai->rekapNilai)
                                                <span class="badge bg-success">Sudah Dinilai</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Belum Dinilai</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- Top 10 Bonsai Terbaik --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white align-content-center">
                <strong>Top 10 Bonsai Terbaik</strong>
            </div>
            <div class="card-body">
                @if ($bestTen->isEmpty())
                    <p class="text-muted">Belum ada peringkat untuk kontes aktif.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Peringkat</th>
                                    <th>Nama Bonsai</th>
                                    <th>Pemilik</th>
                                    <th>Skor Akhir</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bestTen as $item)
                                    <tr>
                                        <td>{{ $item->peringkat }}</td>
                                        <td>{{ $item->bonsai->nama_pohon }}</td>
                                        <td>{{ optional($item->bonsai->pendaftaranKontes->user)->name ?? '-' }}</td>
                                        <td>{{ number_format($item->skor_akhir, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection

@section('script')
    <script>
        const tahun = @json($tahun);
        const dataPenilaian = @json($dataPenilaian);
        const kriteriaTren = @json($kriteriaTren);
        const colors = [{
                bg: 'rgba(40,167,69,0.6)',
                b: 'rgba(40,167,69,1)'
            },
            {
                bg: 'rgba(54,162,235,0.6)',
                b: 'rgba(54,162,235,1)'
            },
            {
                bg: 'rgba(255,193,7,0.6)',
                b: 'rgba(255,193,7,1)'
            },
            {
                bg: 'rgba(220,53,69,0.6)',
                b: 'rgba(220,53,69,1)'
            },
            {
                bg: 'rgba(153,102,255,0.6)',
                b: 'rgba(153,102,255,1)'
            },
            {
                bg: 'rgba(255,159,64,0.6)',
                b: 'rgba(255,159,64,1)'
            }
        ];

        // Chart Bonsai Dinilai (Bar)
        new Chart(document.getElementById('chartPenilaian'), {
            type: 'bar',
            data: {
                labels: tahun,
                datasets: [{
                    label: 'Bonsai Dinilai',
                    data: dataPenilaian,
                    backgroundColor: colors[0].bg,
                    borderColor: colors[0].b,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });

        // Chart Tren Skor Kriteria (Line)
        new Chart(document.getElementById('chartKategoriAnggota'), {
            type: 'line',
            data: {
                labels: tahun,
                datasets: Object.entries(kriteriaTren).map(([label, data], idx) => ({
                    label,
                    data,
                    fill: false,
                    tension: 0.3,
                    backgroundColor: colors[idx % colors.length].bg,
                    borderColor: colors[idx % colors.length].b,
                    borderWidth: 2
                }))
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@endsection
