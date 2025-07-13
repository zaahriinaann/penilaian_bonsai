@extends('layouts.app')

@section('title', 'Rekap Nilai Bonsai')

@php
    $rekap = [
        1 => [
            'nama' => 'Bonsai A',
            'jenis' => 'Serut',
            'pemilik' => 'Pak Budi',
            'total' => 255,
            'rata' => 85.0,
            'kategori' => ['Keindahan' => 85, 'Kesehatan' => 90, 'Kerapian' => 80],
        ],
        2 => [
            'nama' => 'Bonsai B',
            'jenis' => 'Beringin',
            'pemilik' => 'Bu Sari',
            'total' => 260,
            'rata' => 86.7,
            'kategori' => ['Keindahan' => 88, 'Kesehatan' => 85, 'Kerapian' => 87],
        ],
        3 => [
            'nama' => 'Bonsai C',
            'jenis' => 'Sancang',
            'pemilik' => 'Pak Joko',
            'total' => 271,
            'rata' => 90.3,
            'kategori' => ['Keindahan' => 90, 'Kesehatan' => 92, 'Kerapian' => 89],
        ],
        4 => [
            'nama' => 'Bonsai D',
            'jenis' => 'Anting Putri',
            'pemilik' => 'Bu Dina',
            'total' => 250,
            'rata' => 83.3,
            'kategori' => ['Keindahan' => 82, 'Kesehatan' => 85, 'Kerapian' => 83],
        ],
        5 => [
            'nama' => 'Bonsai E',
            'jenis' => 'Asam Jawa',
            'pemilik' => 'Pak Andi',
            'total' => 245,
            'rata' => 81.7,
            'kategori' => ['Keindahan' => 80, 'Kesehatan' => 83, 'Kerapian' => 82],
        ],
        6 => [
            'nama' => 'Bonsai F',
            'jenis' => 'Sancang',
            'pemilik' => 'Bu Wati',
            'total' => 275,
            'rata' => 91.7,
            'kategori' => ['Keindahan' => 92, 'Kesehatan' => 91, 'Kerapian' => 92],
        ],
        7 => [
            'nama' => 'Bonsai G',
            'jenis' => 'Beringin',
            'pemilik' => 'Pak Rudi',
            'total' => 262,
            'rata' => 87.3,
            'kategori' => ['Keindahan' => 87, 'Kesehatan' => 88, 'Kerapian' => 87],
        ],
        8 => [
            'nama' => 'Bonsai H',
            'jenis' => 'Serut',
            'pemilik' => 'Bu Nana',
            'total' => 240,
            'rata' => 80.0,
            'kategori' => ['Keindahan' => 79, 'Kesehatan' => 81, 'Kerapian' => 80],
        ],
        9 => [
            'nama' => 'Bonsai I',
            'jenis' => 'Sancang',
            'pemilik' => 'Pak Eko',
            'total' => 268,
            'rata' => 89.3,
            'kategori' => ['Keindahan' => 90, 'Kesehatan' => 90, 'Kerapian' => 88],
        ],
        10 => [
            'nama' => 'Bonsai J',
            'jenis' => 'Asam',
            'pemilik' => 'Bu Rina',
            'total' => 258,
            'rata' => 86.0,
            'kategori' => ['Keindahan' => 86, 'Kesehatan' => 85, 'Kerapian' => 87],
        ],
        11 => [
            'nama' => 'Bonsai K',
            'jenis' => 'Beringin',
            'pemilik' => 'Pak Ujang',
            'total' => 230,
            'rata' => 76.7,
            'kategori' => ['Keindahan' => 76, 'Kesehatan' => 77, 'Kerapian' => 77],
        ],
        12 => [
            'nama' => 'Bonsai L',
            'jenis' => 'Serut',
            'pemilik' => 'Bu Mega',
            'total' => 225,
            'rata' => 75.0,
            'kategori' => ['Keindahan' => 74, 'Kesehatan' => 75, 'Kerapian' => 76],
        ],
    ];

    // Ambil 10 besar
    $rekapSorted = collect($rekap)->sortByDesc('total')->values();
    $bestTen = $rekapSorted->slice(0, 10);
@endphp

@section('content')
    <div class="container-fluid">
        <div class="card mb-4 shadow-sm rounded-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">🏆 10 Besar Nilai Bonsai</h4>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-hover table-data text-nowrap" id="top-ten-table">
                    <thead class="table-light">
                        <tr>
                            <th>Peringkat</th>
                            <th>Nama Bonsai</th>
                            <th>Pemilik</th>
                            <th>Total Nilai</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bestTen as $i => $b)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $b['nama'] }}</td>
                                <td>{{ $b['pemilik'] }}</td>
                                <td>{{ $b['total'] }}</td>
                                <td>
                                    <button class="btn btn-sm btn-info btn-detail" data-nama="{{ $b['nama'] }}"
                                        data-jenis="{{ $b['jenis'] }}" data-pemilik="{{ $b['pemilik'] }}"
                                        data-total="{{ $b['total'] }}" data-rata="{{ $b['rata'] }}"
                                        data-kategori='@json($b['kategori'])'>
                                        Detail
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card shadow-sm rounded-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">📊 Rekap Nilai Seluruh Peserta</h4>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-hover table-data text-nowrap" id="rekap-table">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Bonsai</th>
                            <th>Jenis</th>
                            <th>Pemilik</th>
                            <th>Total Nilai</th>
                            <th>Rata-rata</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rekapSorted as $b)
                            <tr>
                                <td>{{ $b['nama'] }}</td>
                                <td>{{ $b['jenis'] }}</td>
                                <td>{{ $b['pemilik'] }}</td>
                                <td>{{ $b['total'] }}</td>
                                <td>{{ number_format($b['rata'], 1) }}</td>
                                <td>
                                    <button class="btn btn-sm btn-info btn-detail" data-nama="{{ $b['nama'] }}"
                                        data-jenis="{{ $b['jenis'] }}" data-pemilik="{{ $b['pemilik'] }}"
                                        data-total="{{ $b['total'] }}" data-rata="{{ $b['rata'] }}"
                                        data-kategori='@json($b['kategori'])'>
                                        Detail
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" />
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        // // Aktifkan DataTables
        // $(document).ready(function() {
        //     if ($.fn.DataTable.isDataTable('#rekap-table')) {
        //         $('#rekap-table').DataTable().destroy();
        //     }

        //     $('#rekap-table').DataTable({
        //         responsive: true
        //     });
        // });



        // Modal Detail
        document.querySelectorAll('.btn-detail').forEach(button => {
            button.addEventListener('click', function() {
                const nama = this.dataset.nama;
                const jenis = this.dataset.jenis;
                const pemilik = this.dataset.pemilik;
                const total = this.dataset.total;
                const rata = parseFloat(this.dataset.rata).toFixed(1);
                const kategori = JSON.parse(this.dataset.kategori);

                let html = `
                <table class="table table-bordered text-start">
                    <tr><th width="140">Nama Bonsai</th><td>${nama}</td></tr>
                    <tr><th>Jenis</th><td>${jenis}</td></tr>
                    <tr><th>Pemilik</th><td>${pemilik}</td></tr>
                    <tr><th>Total</th><td>${total}</td></tr>
                    <tr><th>Rata-rata</th><td>${rata}</td></tr>
                </table>
                <h6 class="mt-3">Nilai per Kategori</h6>
            `;

                for (const [key, val] of Object.entries(kategori)) {
                    html += `<p>${key}: <strong>${val}</strong></p>`;
                }

                Swal.fire({
                    title: 'Detail Rekap Nilai',
                    html: html,
                    width: 600,
                    confirmButtonText: 'Tutup'
                });
            });
        });
    </script>
@endsection
