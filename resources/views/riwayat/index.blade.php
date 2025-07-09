@extends('layouts.app')

@section('title', 'Riwayat Penilaian')
@php
    // Data manual
    $kontes = [
        ['id' => 1, 'nama' => 'Kontes Nasional 2024', 'tanggal' => '2024-05-01'],
        ['id' => 2, 'nama' => 'Kontes Regional 2024', 'tanggal' => '2024-06-10'],
    ];

    $bonsai = [
        1 => [
            ['id' => 1, 'nama' => 'Bonsai A', 'jenis' => 'Serut'],
            ['id' => 2, 'nama' => 'Bonsai B', 'jenis' => 'Beringin'],
        ],
        2 => [['id' => 3, 'nama' => 'Bonsai C', 'jenis' => 'Sancang']],
    ];

    $detail = [
        1 => [
            'nama' => 'Bonsai A',
            'jenis' => 'Serut',
            'pemilik' => 'Pak Budi',
            'penilaian' => [
                'Keindahan' => 85,
                'Kesehatan' => 90,
                'Kerapian' => 80,
            ],
        ],
        2 => [
            'nama' => 'Bonsai B',
            'jenis' => 'Beringin',
            'pemilik' => 'Bu Sari',
            'penilaian' => [
                'Keindahan' => 88,
                'Kesehatan' => 85,
                'Kerapian' => 87,
            ],
        ],
        3 => [
            'nama' => 'Bonsai C',
            'jenis' => 'Sancang',
            'pemilik' => 'Pak Joko',
            'penilaian' => [
                'Keindahan' => 90,
                'Kesehatan' => 92,
                'Kerapian' => 89,
            ],
        ],
    ];
@endphp

@section('content')
    <section id="kontes-section">
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="mb-0">Daftar Kontes</h4>
            </div>
            <div class="card-body">
                <table class="table table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Kontes</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($kontes as $k)
                            <tr>
                                <td>{{ $k['nama'] }}</td>
                                <td>{{ $k['tanggal'] }}</td>
                                <td>
                                    <button class="btn btn-primary btn-lihat-bonsai" data-kontes="{{ $k['id'] }}">
                                        Lihat Daftar Bonsai
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section id="bonsai-section" style="display:none;">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Daftar Bonsai</h4>
                <button class="btn btn-secondary" id="btn-back-kontes">Kembali ke Daftar Kontes</button>
            </div>
            <div class="card-body">
                <table class="table table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Bonsai</th>
                            <th>Jenis</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="bonsai-table-body">
                        <!-- Diisi via JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section id="detail-section" style="display:none;">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Detail Bonsai & Penilaian</h4>
                <button class="btn btn-secondary" id="btn-back-bonsai">Kembali ke Daftar Bonsai</button>
            </div>
            <div class="card-body" id="detail-content">
                <!-- Diisi via JS -->
            </div>
        </div>
    </section>
@endsection

@section('script')
    <script>
        const bonsaiData = @json($bonsai);
        const detailData = @json($detail);

        let currentKontes = null;

        document.querySelectorAll('.btn-lihat-bonsai').forEach(btn => {
            btn.addEventListener('click', function() {
                currentKontes = this.dataset.kontes;
                document.getElementById('kontes-section').style.display = 'none';
                document.getElementById('bonsai-section').style.display = '';
                loadBonsaiTable(currentKontes);
            });
        });

        document.getElementById('btn-back-kontes').addEventListener('click', function() {
            document.getElementById('bonsai-section').style.display = 'none';
            document.getElementById('kontes-section').style.display = '';
        });

        document.getElementById('btn-back-bonsai').addEventListener('click', function() {
            document.getElementById('detail-section').style.display = 'none';
            document.getElementById('bonsai-section').style.display = '';
        });

        function loadBonsaiTable(kontesId) {
            const tbody = document.getElementById('bonsai-table-body');
            tbody.innerHTML = '';
            (bonsaiData[kontesId] || []).forEach(b => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                        <td>${b.nama}</td>
                        <td>${b.jenis}</td>
                        <td>
                            <button class="btn btn-info btn-lihat-detail" data-bonsai="${b.id}">Lihat Detail</button>
                        </td>
                    `;
                tbody.appendChild(tr);
            });
            // Add event listener for detail buttons
            tbody.querySelectorAll('.btn-lihat-detail').forEach(btn => {
                btn.addEventListener('click', function() {
                    const bonsaiId = this.dataset.bonsai;
                    showDetail(bonsaiId);
                });
            });
        }

        function showDetail(bonsaiId) {
            document.getElementById('bonsai-section').style.display = 'none';
            document.getElementById('detail-section').style.display = '';
            const d = detailData[bonsaiId];
            let html = `
                    <div class="mb-3">
                        <table class="table table-bordered">
                            <tr><th width="150">Nama</th><td>${d.nama}</td></tr>
                            <tr><th>Jenis</th><td>${d.jenis}</td></tr>
                            <tr><th>Pemilik</th><td>${d.pemilik}</td></tr>
                        </table>
                    </div>
                    <h5>Penilaian</h5>
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Kriteria</th>
                                <th>Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                `;
            for (const [kriteria, nilai] of Object.entries(d.penilaian)) {
                html += `<tr><td>${kriteria}</td><td>${nilai}</td></tr>`;
            }
            html += `
                        </tbody>
                    </table>
                `;
            document.getElementById('detail-content').innerHTML = html;
        }
    </script>
@endsection
