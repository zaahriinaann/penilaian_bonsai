@extends('layouts.app')

@section('title', 'Penilaian Bonsai')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title">Daftar Peserta Kontes: {{ $kontes->nama_kontes }}</h3>
                <p class="card-text">Silakan pilih peserta untuk melakukan penilaian.</p>

                <table class="table table-bordered table-striped text-nowrap table-data">
                    <thead>
                        <tr>
                            <th>No Juri</th>
                            <th>No Daftar</th>
                            <th>Nama Peserta</th>
                            <th>Pohon Bonsai</th>
                            <th>Status Penilaian</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pendaftarans as $item)
                            @php
                                $sudahDinilai = \App\Models\Nilai::sudahDinilai($item->bonsai_id, Auth::id());
                            @endphp
                            <tr>
                                <td class="text-center">{{ $item->nomor_juri ?? '-' }}</td>
                                <td class="text-center">{{ $item->nomor_pendaftaran ?? '-' }}</td>
                                <td>{{ $item->user->name ?? '-' }}</td>
                                <td>{{ $item->bonsai->nama_pohon ?? '-' }}</td>
                                <td>
                                    @if ($sudahDinilai)
                                        <span class="badge bg-success">Sudah Dinilai</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Belum Dinilai</span>
                                    @endif
                                </td>
                                <td class="text-nowrap">
                                    <a href="{{ route('nilai.show', $item->bonsai_id) }}"
                                        class="btn btn-sm {{ $sudahDinilai ? 'btn-warning' : 'btn-primary' }}">
                                        {{ $sudahDinilai ? 'Edit Nilai' : 'Nilai' }}
                                    </a>
                                    {{-- Tombol hapus bisa diatur jika dibutuhkan --}}
                                    {{-- <form action="{{ route('nilai.destroy', $item->bonsai_id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus nilai?')">Hapus</button>
                                    </form> --}}
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
        // Tambahkan JS jika perlu (misal DataTables)
    </script>
@endsection
