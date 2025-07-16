@extends('layouts.app')

@section('title', 'Rekap Nilai Bonsai')

@section('content')
    <div class="container-fluid">
        {{-- TOP 10 --}}
        <div class="card mb-4 shadow-sm rounded-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">🏆 10 Besar Nilai Bonsai</h4>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-hover table-bordered text-nowrap" id="top-ten-table">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>No Juri</th>
                            <th>No Pendaftaran</th>
                            <th>Nama Pohon</th>
                            <th>Pemilik</th>
                            <th>Skor Akhir</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bestTen as $i => $b)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $b['nomor_juri'] }}</td>
                                <td>{{ $b['nomor_pendaftaran'] }}</td>
                                <td>{{ $b['nama_pohon'] }}</td>
                                <td>{{ $b['pemilik'] }}</td>
                                <td>{{ number_format($b['skor_akhir'], 2) }}</td>
                                <td>
                                    <button class="btn btn-sm btn-info btn-detail" data-nama="{{ $b['nama_pohon'] }}"
                                        data-juri="{{ $b['nomor_juri'] }}" data-total="{{ $b['skor_akhir'] }}"
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

        {{-- Semua Rekap --}}
        <div class="card shadow-sm rounded-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">📊 Rekap Nilai Semua Peserta</h4>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-hover table-bordered text-nowrap" id="rekap-table">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>No Juri</th>
                            <th>No Pendaftaran</th>
                            <th>Nama Pohon</th>
                            <th>Pemilik</th>
                            <th>Skor Akhir</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rekapSorted as $i => $b)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $b['nomor_juri'] }}</td>
                                <td>{{ $b['nomor_pendaftaran'] }}</td>
                                <td>{{ $b['nama_pohon'] }}</td>
                                <td>{{ $b['pemilik'] }}</td>
                                <td>{{ number_format($b['skor_akhir'], 2) }}</td>
                                <td>
                                    <button class="btn btn-sm btn-info btn-detail" data-nama="{{ $b['nama_pohon'] }}"
                                        data-juri="{{ $b['nomor_juri'] }}" data-total="{{ $b['skor_akhir'] }}"
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
        $(document).ready(function() {
            $('#top-ten-table').DataTable({
                paging: false,
                searching: false,
                info: false,
                responsive: true
            });

            $('#rekap-table').DataTable({
                responsive: true
            });
        });

        // SweetAlert modal detail
        document.querySelectorAll('.btn-detail').forEach(button => {
            button.addEventListener('click', function() {
                const nama = this.dataset.nama;
                const juri = this.dataset.juri;
                const total = parseFloat(this.dataset.total).toFixed(2);
                const kategori = JSON.parse(this.dataset.kategori);

                let html = `
                <table class="table table-bordered text-start">
                    <tr><th width="140">Nama Bonsai</th><td>${nama}</td></tr>
                    <tr><th>No Juri</th><td>${juri}</td></tr>
                    <tr><th>Skor Akhir</th><td><strong>${total}</strong></td></tr>
                </table>
                <h6 class="mt-3">Detail Kategori</h6>
            `;

                for (const [kriteria, data] of Object.entries(kategori)) {
                    html += `
                    <div class="border rounded p-2 mb-2">
                        <strong>${kriteria}</strong><br/>
                        Hasil Defuzzifikasi: <strong>${parseFloat(data.hasil).toFixed(2)}</strong><br/>
                        Himpunan: <em>${data.himpunan}</em>
                    </div>
                `;
                }

                Swal.fire({
                    title: 'Detail Nilai Bonsai',
                    html: html,
                    width: 600,
                    confirmButtonText: 'Tutup'
                });
            });
        });
    </script>
@endsection
