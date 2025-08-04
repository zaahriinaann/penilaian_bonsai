@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            {{-- Total Card Section --}}
            <div class="d-flex flex-wrap justify-content-center gap-3 px-3 px-md-5 my-4">
                @foreach ($dataRender as $key => $item)
                    <div class="container-card noselect mb-3" style="flex: 1 1 220px; max-width: 260px;">
                        <div class="canvas">
                            @for ($i = 1; $i <= 25; $i++)
                                <div class="tracker tr-{{ $i }}"></div>
                            @endfor
                            <div id="card" style="background: #{{ $item[1] }}">
                                <p id="prompt">Total {{ $key }}</p>
                                <div class="title">{{ number_format($item[0], 0) }} {{ $key }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Kontes Aktif --}}
            <div class="row px-4 pb-4">
                <div class="col-12">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-success text-white align-items-center">
                            Kontes Aktif Saat Ini
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">Nama Kontes:
                                    <strong>{{ $kontesAktif->nama_kontes ?? 'Tidak Ada Kontes Aktif' }}</strong>
                                </li>
                                <li class="list-group-item">Tanggal:
                                    <strong>{{ $kontesAktif ? \Carbon\Carbon::parse($kontesAktif->tanggal_mulai_kontes)->format('d M Y') : '-' }}
                                        -
                                        {{ $kontesAktif ? \Carbon\Carbon::parse($kontesAktif->tanggal_selesai_kontes)->format('d M Y') : '-' }}</strong>
                                </li>
                                <li class="list-group-item">Lokasi:
                                    <strong>{{ $kontesAktif->tempat_kontes ?? '-' }}</strong>
                                </li>
                                <li class="list-group-item">Status: <strong
                                        class="{{ $kontesAktif ? 'text-success' : 'text-muted' }}">{{ $kontesAktif ? 'Sedang Berlangsung' : 'Tidak Aktif' }}</strong>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Statistik Penilaian & Slot --}}
            <div class="row px-4 pb-4">
                <div class="col-md-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-dark text-white align-items-center">Statistik Penilaian</div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">Jumlah Bonsai Dinilai: <strong>{{ $bonsaiDinilai }}</strong>
                                </li>
                                <li class="list-group-item">Jumlah Bonsai Belum Dinilai:
                                    <strong>{{ $bonsaiBelum }}</strong>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-dark text-white align-items-center">Statistik Slot Kontes</div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">Total Slot Tersedia: <strong>{{ $slotTotal }}</strong></li>
                                <li class="list-group-item">Slot Terpakai: <strong>{{ $slotTotal - $slotSisa }}</strong>
                                </li>
                                <li class="list-group-item">Sisa Slot: <strong>{{ $slotSisa }}</strong></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Top 3 Bonsai --}}
            <div class="row px-4 pb-5">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-dark text-white align-items-center">Top 3 Bonsai Terbaik</div>
                        <div class="card-body">
                            @if ($topBonsai->isEmpty())
                                <p class="text-muted">Belum ada data rekap nilai.</p>
                            @else
                                <table class="table table-striped align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Nomor Juri</th>
                                            <th>Nomor Pendaftaran</th>
                                            <th>Nama Pohon</th>
                                            <th>Pemilik</th>
                                            <th>Skor Akhir</th>
                                            <th>Himpunan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($topBonsai as $i => $rekap)
                                            @php $pendaftaran = $rekap->bonsai->pendaftaranKontes; @endphp
                                            <tr>
                                                <td>{{ $i + 1 }}</td>
                                                <td>{{ $pendaftaran->nomor_juri }}</td>
                                                <td>{{ $pendaftaran->nomor_pendaftaran }}</td>
                                                <td>{{ $rekap->bonsai->nama_pohon }}</td>
                                                <td>{{ $pendaftaran->user->name }}</td>
                                                <td><span
                                                        class="badge {{ $i === 0 ? 'bg-success' : ($i === 1 ? 'bg-primary' : 'bg-info text-dark') }} fs-6">{{ number_format($rekap->skor_akhir, 2) }}</span>
                                                </td>
                                                <td>{{ $rekap->himpunan_akhir }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Prediksi --}}
            <div class="row px-4 pb-4">
                <div class="col-12">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-dark text-white align-items-center">Prediksi Slot & Meja Kontes
                            Selanjutnya</div>
                        <div class="card-body text-center">
                            <h4 class="text-primary mb-2">Prediksi Bonsai: {{ $prediksiBonsai }} pohon</h4>
                            <h4 class="text-success mb-0">Kebutuhan Meja: {{ $prediksiMeja }} meja</h4>
                            <small class="text-muted d-block mt-2">
                                * 1 meja dapat menampung 5 pohon
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Grafik --}}
            <div class="row px-4 pb-4">
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-secondary text-white align-items-center">
                            <h5 class="mb-0">Jumlah Bonsai per Kontes ({{ $tahunSekarang }})</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="chartBonsai"></canvas>
                        </div>
                    </div>
                </div>
                {{-- <div class="col-md-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-secondary text-white align-items-center">
                            <h5 class="mb-0">Perbandingan Jumlah Bonsai vs Juri per Kontes ({{ $tahunSekarang }})</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="chartBonsaiJuri"></canvas>
                        </div>
                    </div>
                </div> --}}
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-secondary text-white align-items-center">
                            <h5 class="mb-0">Jumlah Kontes 5 Tahun Terakhir</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="chartKontes"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-secondary text-white align-items-center">
                            <h5 class="mb-0">Tren Skor Rata-rata per Kriteria (5 Tahun Terakhir)</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="chartKategori"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        const namaKontes = @json($namaKontes);
        const bonsaiPerKontes = @json($bonsaiPerKontes);
        const juriPerKontes = @json($juriPerKontes);
        const tahun = @json($tahun);
        const dataKontes = @json($data_kontes);
        const dataBonsai = @json($data_bonsai);
        const dataJuri = @json($data_juri);
        const kriteriaTren = @json($kriteriaTren);

        // Chart Bonsai per Kontes (tahun berjalan)
        new Chart(document.getElementById('chartBonsai'), {
            type: 'bar',
            data: {
                labels: namaKontes.map(label => label.length > 20 ? label.substring(0, 20) + '...' :
                label), // singkat di axis
                datasets: [{
                    label: 'Jumlah Bonsai',
                    data: bonsaiPerKontes,
                    backgroundColor: 'rgba(255, 193, 7, 0.6)',
                    borderColor: 'rgba(255, 193, 7, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        callbacks: {
                            title: function(context) {
                                return namaKontes[context[0].dataIndex]; // full name di tooltip
                            },
                            label: function(context) {
                                return 'Jumlah Bonsai: ' + context.formattedValue;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });


        // Chart Bonsai vs Juri per Kontes
        new Chart(document.getElementById('chartBonsaiJuri'), {
            type: 'bar',
            data: {
                labels: namaKontes,
                datasets: [{
                        label: 'Jumlah Bonsai',
                        data: bonsaiPerKontes,
                        backgroundColor: 'rgba(255, 193, 7, 0.6)',
                        borderColor: 'rgba(255, 193, 7, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Jumlah Juri',
                        data: juriPerKontes,
                        backgroundColor: 'rgba(0, 123, 255, 0.6)',
                        borderColor: 'rgba(0, 123, 255, 1)',
                        borderWidth: 1
                    }
                ]
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

        // Chart Kontes 5 Tahun Terakhir
        new Chart(document.getElementById('chartKontes'), {
            type: 'bar',
            data: {
                labels: tahun,
                datasets: [{
                    label: 'Jumlah Kontes',
                    data: dataKontes,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
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

        // Chart Tren Skor Kriteria
        const categoryColors = [{
                bg: 'rgba(40, 167, 69, 0.6)',
                border: 'rgba(40, 167, 69, 1)'
            },
            {
                bg: 'rgba(54, 162, 235, 0.6)',
                border: 'rgba(54, 162, 235, 1)'
            },
            {
                bg: 'rgba(255, 193, 7, 0.6)',
                border: 'rgba(255, 193, 7, 1)'
            },
            {
                bg: 'rgba(220, 53, 69, 0.6)',
                border: 'rgba(220, 53, 69, 1)'
            },
            {
                bg: 'rgba(153, 102, 255, 0.6)',
                border: 'rgba(153, 102, 255, 1)'
            },
            {
                bg: 'rgba(255, 159, 64, 0.6)',
                border: 'rgba(255, 159, 64, 1)'
            }
        ];
        new Chart(document.getElementById('chartKategori'), {
            type: 'line',
            data: {
                labels: tahun,
                datasets: Object.entries(kriteriaTren).map(([label, data], idx) => ({
                    label,
                    data,
                    fill: false,
                    tension: 0.3,
                    backgroundColor: categoryColors[idx % categoryColors.length].bg,
                    borderColor: categoryColors[idx % categoryColors.length].border,
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
