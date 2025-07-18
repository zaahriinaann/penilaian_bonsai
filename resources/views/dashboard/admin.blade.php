<div class="card">
    {{-- Total Card Section (TIDAK DIUBAH) --}}
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

    {{-- Kartu 3: Kontes Aktif Saat Ini --}}
    <div class="row px-4 pb-4 d-none">
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
                        <li class="list-group-item">Lokasi: <strong>{{ $kontesAktif->tempat_kontes ?? '-' }}</strong>
                        </li>
                        <li class="list-group-item">Status:
                            <strong class="{{ $kontesAktif ? 'text-success' : 'text-muted' }}">
                                {{ $kontesAktif ? 'Sedang Berlangsung' : 'Tidak Aktif' }}
                            </strong>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>


    {{-- Statistik dan Aktivitas --}}
    <div class="row px-4 pb-4">
        {{-- Kartu 1: Statistik Penilaian --}}
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-dark text-white align-items-center">
                    Statistik Penilaian
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Jumlah Bonsai Dinilai: <strong>{{ $bonsaiDinilai }}</strong></li>
                        <li class="list-group-item">Jumlah Bonsai Belum Dinilai: <strong>{{ $bonsaiBelum }}</strong>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Kartu 2: Statistik Slot Kontes --}}
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-dark text-white align-items-center">
                    Statistik Slot Kontes
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Total Slot Tersedia: <strong>{{ $slotTotal }}</strong></li>
                        <li class="list-group-item">Slot Terpakai: <strong>{{ $slotTotal - $slotSisa }}</strong></li>
                        <li class="list-group-item">Sisa Slot Kosong: <strong>{{ $slotSisa }}</strong></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>



    {{-- Top 3 Bonsai --}}
    <div class="row px-4 pb-5">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white align-items-center">
                    Top 3 Bonsai Terbaik
                </div>
                <div class="card-body">
                    <table class="table table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Nama Bonsai</th>
                                <th>Jenis</th>
                                <th>Pemilik</th>
                                <th>Nilai Akhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Bonsai A</td>
                                <td>Serut</td>
                                <td>Pak Budi</td>
                                <td><span class="badge bg-success fs-6">90.2</span></td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Bonsai B</td>
                                <td>Anting Putri</td>
                                <td>Ibu Sari</td>
                                <td><span class="badge bg-primary fs-6">88.5</span></td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Bonsai C</td>
                                <td>Sancang</td>
                                <td>Pak Didi</td>
                                <td><span class="badge bg-info text-dark fs-6">87.3</span></td>
                            </tr>
                        </tbody>
                    </table>
                    <small class="text-muted">* Berdasarkan akumulasi nilai semua juri</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Prediksi Slot Kontes Tahun Depan
        @if ($prediksiSlot > 0)
            <div class="row px-4 pb-4">
                <div class="col-12">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-dark text-white align-items-center">
                            Prediksi Slot Kontes Tahun Depan
                        </div>
                        <div class="card-body text-center">
                            <p class="mb-2">
                                Perkiraan berdasarkan tren 25% kenaikan dari tahun ini
                            </p>
                            <h3 class="text-primary mb-0">
                                {{ $prediksiSlot }} Slot
                            </h3>
                            <small class="text-muted">*Data bersifat estimasi</small>
                        </div>
                    </div>
                </div>
            </div>
        @endif --}}

    {{-- Prediksi Kebutuhan Meja Tahun Depan
        @if ($prediksiJumlahMeja > 0)
            <div class="row px-4 pb-4">
                <div class="col-12">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-dark text-white align-items-center">
                            Prediksi Kebutuhan Meja Tahun Depan
                        </div>
                        <div class="card-body text-center">
                            <p class="mb-2">
                                Estimasi rata‑rata bonsai per kontes (5 th) + tren naik 25 %
                            </p>
                            <h5 class="text-muted mb-1">
                                Perkiraan Bonsai: <strong>{{ $prediksiBonsai }}</strong> pohon
                            </h5>
                            <h3 class="text-primary mb-1">
                                {{ $prediksiJumlahMeja }} Meja
                            </h3>
                            <small class="text-muted">
                                *1 meja menampung maksimal {{ $kapasitasMeja }} bonsai
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        @endif --}}

    {{-- Prediksi Slot Kontes Tahun Depan --}}
    <div class="row px-4 pb-4">
        <div class="col-12">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-dark text-white align-items-center">
                    Prediksi Slot & Meja Tahun Depan
                </div>
                <div class="card-body text-center">
                    <p class="mb-2">
                        Berdasarkan tren kenaikan rata-rata <strong>{{ $rataKenaikan }}%</strong> dari 5 tahun terakhir
                    </p>
                    <h4 class="text-primary mb-2">Prediksi Bonsai: {{ $prediksiBonsai }} pohon</h4>
                    <h4 class="text-success mb-0">Kebutuhan Meja: {{ $prediksiMeja }} meja</h4>
                    <small class="text-muted d-block mt-2">*1 meja dapat menampung 5 pohon bonsai</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Grafik 5 Tahun Terakhir - 2 Kolom --}}
    <div class="row px-4 pb-4">
        {{-- Grafik Kontes --}}
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

        {{-- Grafik Peserta --}}
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-secondary text-white align-items-center">
                    <h5 class="mb-0">Jumlah Peserta 5 Tahun Terakhir</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartPeserta"></canvas>
                </div>
            </div>
        </div>

        {{-- Grafik Bonsai --}}
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-secondary align-items-center">
                    <h5 class="mb-0">Jumlah Bonsai 5 Tahun Terakhir</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartBonsai"></canvas>
                </div>
            </div>
        </div>

        {{-- Grafik Bonsai vs Juri --}}
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-secondary align-items-center">
                    <h5 class="mb-0">Perbandingan Jumlah Bonsai vs Juri per Tahun</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartBonsaiJuri"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>

@section('script')
<script>
    const tahun = {!! json_encode($tahun) !!};
    const dataKontes = {!! json_encode($data_kontes) !!};
    const dataPeserta = {!! json_encode($data_peserta) !!};
    const dataBonsai = {!! json_encode($data_bonsai) !!};
    const dataJuri = {!! json_encode($data_juri) !!};


    // Chart Kontes
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
                    beginAtZero: true,
                    stepSize: 1
                }
            }
        }
    });

    // Chart Peserta
    new Chart(document.getElementById('chartPeserta'), {
        type: 'bar',
        data: {
            labels: tahun,
            datasets: [{
                label: 'Jumlah Peserta',
                data: dataPeserta,
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
                    beginAtZero: true
                }
            }
        }
    });

    // Chart Bonsai
    new Chart(document.getElementById('chartBonsai'), {
        type: 'bar',
        data: {
            labels: tahun,
            datasets: [{
                label: 'Jumlah Bonsai',
                data: dataBonsai,
                backgroundColor: 'rgba(255, 193, 7, 0.6)',
                borderColor: 'rgba(255, 193, 7, 1)',
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

    // Chart Bonsai vs Juri
    new Chart(document.getElementById('chartBonsaiJuri'), {
        type: 'bar',
        data: {
            labels: tahun,
            datasets: [{
                    label: 'Jumlah Bonsai',
                    data: dataBonsai,
                    backgroundColor: 'rgba(255, 193, 7, 0.6)',
                    borderColor: 'rgba(255, 193, 7, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Jumlah Juri',
                    data: dataJuri,
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
</script>
@endsection