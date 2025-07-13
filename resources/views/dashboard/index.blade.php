@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid py-4">
        {{-- Kartu Total --}}
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-3">
            @php
                $dataRender = [
                    'Kontes' => [13, '00b894'],
                    'Juri' => [7, '0984e3'],
                    'Peserta' => [85, 'fdcb6e'],
                    'Bonsai' => [130, 'd63031'],
                ];
            @endphp
            @foreach ($dataRender as $key => $item)
                <div class="col">
                    <div class="card text-white border-0 shadow-sm" style="background-color: #{{ $item[1] }}">
                        <div class="card-body text-center">
                            <small>Total {{ $key }}</small>
                            <h4>{{ number_format($item[0], 0) }}</h4>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Kontes Aktif --}}
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center text-white bg-info">Kontes Aktif:
                Kontes Nasional 2025</div>
            <div class="card-body">
                <p>Tanggal: 10 Juli 2025 s/d 15 Juli 2025</p>
                <p>Jumlah peserta: 45</p>
                <p>Jumlah bonsai: 70</p>
            </div>
        </div>

        {{-- Statistik Penilaian, Top 3, Aktivitas Juri --}}
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card border-start border-success border-4">
                    <div class="card-body">
                        <strong>Total Bonsai Dinilai:</strong>
                        <h5>55 / 70 (78.6%)</h5>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-start border-warning border-4">
                    <div class="card-body">
                        <strong>Top 3 Nilai Tertinggi</strong>
                        <ul class="mb-0">
                            <li>Bonsai Serut - 278</li>
                            <li>Bonsai Anting Putri - 274</li>
                            <li>Bonsai Santigi - 269</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-start border-primary border-4">
                    <div class="card-body">
                        <strong>Aktivitas Juri Terakhir</strong>
                        <ul class="mb-0">
                            <li>Pak Budi menilai Bonsai Cemara</li>
                            <li>Ibu Sari menilai Bonsai Waru</li>
                            <li>Pak Didi menilai Bonsai Santigi</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Grafik --}}
        <div class="row mt-4">
            @php
                $tahun = [2021, 2022, 2023, 2024, 2025];
                $data_kontes = [2, 4, 5, 3, 10];
                $data_peserta = [20, 35, 40, 60, 85];
                $data_bonsai = [30, 55, 65, 95, 130];
            @endphp
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-success text-white text-center">Grafik Kontes</div>
                    <div class="card-body"><canvas id="chartKontes" height="200"></canvas></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white text-center">Grafik Peserta</div>
                    <div class="card-body"><canvas id="chartPeserta" height="200"></canvas></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-danger text-white text-center">Grafik Bonsai</div>
                    <div class="card-body"><canvas id="chartBonsai" height="200"></canvas></div>
                </div>
            </div>
        </div>

        {{-- Ekspor dan Notifikasi --}}
        <div class="mt-4 d-flex justify-content-between align-items-center">
            <div>
                <a href="#" onclick="alert('Simulasi export PDF');" class="btn btn-outline-danger">Export PDF</a>
                <a href="#" onclick="alert('Simulasi export Excel');" class="btn btn-outline-success">Export Excel</a>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const tahun = @json($tahun);
        const dataKontes = @json($data_kontes);
        const dataPeserta = @json($data_peserta);
        const dataBonsai = @json($data_bonsai);

        function buatChart(id, label, data, warna) {
            const ctx = document.getElementById(id).getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: tahun,
                    datasets: [{
                        label: label,
                        data: data,
                        backgroundColor: warna,
                        borderColor: warna,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }

        buatChart('chartKontes', 'Jumlah Kontes', dataKontes, 'rgba(40, 167, 69, 0.7)');
        buatChart('chartPeserta', 'Jumlah Peserta', dataPeserta, 'rgba(0, 123, 255, 0.7)');
        buatChart('chartBonsai', 'Jumlah Bonsai', dataBonsai, 'rgba(220, 53, 69, 0.7)');
    </script>
@endsection
