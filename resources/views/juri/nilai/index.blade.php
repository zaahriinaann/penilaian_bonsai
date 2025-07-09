@extends('layouts.app')

@section('title', 'Penilaian Bonsai')

{{-- penilaian ini langsung pada kontes yang aktif --}}
@section('content')
    <div class="container">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title">Daftar Peserta Kontes</h3>
                <p class="card-text">Silakan pilih peserta untuk melakukan penilaian.</p>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No Juri</th>
                            <th>No Daftar</th>
                            <th>Nama Peserta</th>
                            <th>Pohon Bonsai</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- @foreach ($pesertas as $index => $peserta)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $peserta->nama }}</td>
                                <td>{{ $peserta->kontes->nama }}</td>
                                <td>
                                    <a href="{{ route('nilai.show', $peserta->id) }}"
                                        class="btn btn-primary btn-sm">Penilaian</a>
                                </td>
                            </tr>
                        @endforeach --}}
                        <tr>
                            <td>#1</td>
                            <td>111</td>
                            <td>Nama Peserta</td>
                            <td>Nama Pohon/ukuran/kelas</td>
                            <td>
                                <a href=" " class="btn btn-success btn-sm">Sudah</a>
                                <a href=" " class="btn btn-danger btn-sm">Belum</a>
                            </td>
                            <td>
                                <a href=" " class="btn btn-primary btn-sm">Lihat</a>
                                <a href=" " class="btn btn-warning btn-sm">Edit</a>
                                <a href=" " class="btn btn-danger btn-sm">Hapus</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Tambahkan script JavaScript jika diperlukan
        document.addEventListener('DOMContentLoaded', function() {
            // Contoh: Inisialisasi DataTables atau lainnya
            // $('#example').DataTable();
        });
    </script>
@endsection
