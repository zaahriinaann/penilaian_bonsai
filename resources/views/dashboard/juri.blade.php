@extends('layouts.app')

@section('title', 'Dashboard Juri')

@section('content')
    <div class="card">
        <div class="container py-4">
            {{-- Statistik Umum --}}
            <div class="row mb-4">
                @php
                    $stats = [
                        ['label' => 'Total Kontes Diikuti', 'value' => $totalKontes],
                        ['label' => 'Bonsai Dinilai', 'value' => $bonsaiDinilai],
                        ['label' => 'Bonsai Belum Dinilai', 'value' => $bonsaiBelumDinilai],
                    ];
                @endphp

                @foreach ($stats as $stat)
                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm h-100">
                            <div class="card-body text-center">
                                <h6 class="text-muted">{{ $stat['label'] }}</h6>
                                <h3 class="fw-bold">{{ $stat['value'] }}</h3>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Kontes Aktif --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-success text-white align-items-center">
                    Kontes Aktif Saat Ini
                </div>
                <div class="card-body">
                    @if ($kontesAktif)
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                Nama Kontes: <strong>{{ $kontesAktif->nama_kontes }}</strong>
                            </li>
                            <li class="list-group-item">
                                Tanggal:
                                <strong>{{ \Carbon\Carbon::parse($kontesAktif->tanggal_mulai_kontes)->format('d M Y') }} -
                                    {{ \Carbon\Carbon::parse($kontesAktif->tanggal_selesai_kontes)->format('d M Y') }}</strong>
                            </li>
                            <li class="list-group-item">
                                Lokasi: <strong>{{ $kontesAktif->tempat_kontes }}</strong>
                            </li>
                            <li class="list-group-item text-center mt-3">
                                <a href="{{ route('juri.nilai.index') }}" class="btn btn-primary">
                                    Masuk Penilaian
                                </a>
                            </li>
                        </ul>
                    @else
                        <p class="text-muted mb-0">Tidak ada kontes aktif saat ini.</p>
                    @endif
                </div>
            </div>

            {{-- Grafik Bonsai Dinilai --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-dark text-white align-items-center">
                    Grafik Bonsai Dinilai per Tahun
                </div>
                <div class="card-body">
                    <canvas id="chartPenilaian" height="100"></canvas>
                </div>
            </div>

            {{-- Grafik Tren Skor Kriteria --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-dark text-white align-items-center">
                    Tren Skor Rata-rata per Kriteria (5 Tahun Terakhir)
                </div>
                <div class="card-body">
                    <canvas id="chartKategoriJuri" height="100"></canvas>
                </div>
            </div>

            {{-- Daftar Kontes yang Pernah Dinilai --}}
            @if ($kontesDiikuti->count())
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white align-items-center">
                        Daftar Kontes yang Pernah Dinilai
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-responsive table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Nama Kontes</th>
                                    <th>Tanggal</th>
                                    <th>Lokasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($kontesDiikuti as $index => $kontes)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $kontes->nama_kontes }}</td>
                                        <td>{{ \Carbon\Carbon::parse($kontes->tanggal_mulai_kontes)->format('d M Y') }} -
                                            {{ \Carbon\Carbon::parse($kontes->tanggal_selesai_kontes)->format('d M Y') }}
                                        </td>
                                        <td>{{ $kontes->tempat_kontes }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection

@section('script')
    <script>
        const tahun = @json($tahun);
        const dataPenilaian = @json($dataPenilaian);
        const kriteriaTrenJuri = @json($kriteriaTren);
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

        // Chart Bonsai Dinilai per Tahun (Bar)
        new Chart(document.getElementById('chartPenilaian'), {
            type: 'bar',
            data: {
                labels: tahun,
                datasets: [{
                    label: 'Bonsai Dinilai',
                    data: dataPenilaian,
                    backgroundColor: 'rgba(40, 167, 69, 0.6)',
                    borderColor: 'rgba(40, 167, 69, 1)',
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

        // Chart Tren Skor Kriteria
        new Chart(document.getElementById('chartKategoriJuri'), {
            type: 'line',
            data: {
                labels: tahun,
                datasets: Object.entries(kriteriaTrenJuri).map(([label, data], idx) => ({
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
