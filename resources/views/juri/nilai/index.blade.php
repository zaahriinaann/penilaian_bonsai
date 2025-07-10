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
                            <th class="text-nowrap">No Juri</th>
                            <th class="text-nowrap">No Daftar</th>
                            <th class="text-nowrap">Nama Peserta</th>
                            <th class="text-nowrap">Pohon Bonsai</th>
                            <th class="text-nowrap">Status Penilaian</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($dataRender as $item)
                            <tr>
                                <td class="text-center">{{ $item->nomor_juri ?? 'Belum Diisi' }}</td>
                                <td class="text-center">{{ $item->nomor_pendaftaran ?? 'Belum Diisi' }}</td>
                                <td>{{ $item->user->name ?? 'Belum Diisi' }}</td>
                                <td>{{ $item->bonsai->nama_pohon ?? 'Belum Diisi' }}</td>
                                <td>
                                    //buat kondisi untuk status penilaian, kalo sudah dinilai tampilkan "Sudah Dinilai"
                                    dengan melihat kondisi data defuzzifikasi sudah ada data atau belum
                                    @if ($item)
                                        <span class="badge bg-success">Sudah Dinilai</span>
                                    @else
                                        <span class="badge bg-warning">Belum Dinilai</span>
                                    @endif
                                </td>
                                <td class="text-nowrap">
                                    <a href="{{ route('nilai.show', $item->id) }}" class="btn btn-primary btn-sm">Nilai</a>
                                    <a href=" " class="btn btn-danger btn-sm">Hapus</a>
                                </td>

                            </tr>
                        @endforeach
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
