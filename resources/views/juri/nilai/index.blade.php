@extends('layouts.app')

@section('title', 'Penilaian Bonsai')

@section('content')
    <div class="container">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">

                {{-- Kontes Aktif --}}
                @if ($kontes)
                    <div class="alert alert-success shadow-sm rounded-3">
                        <h4 class="mb-1">
                            <i class="bi bi-award-fill me-2"></i>Kontes Aktif: {{ $kontes->nama_kontes }}
                        </h4>
                        <p class="mb-0">
                            <strong>Tanggal:</strong>
                            {{ \Carbon\Carbon::parse($kontes->tanggal_mulai_kontes)->format('d M Y') ?? '-' }}
                            s/d
                            {{ \Carbon\Carbon::parse($kontes->tanggal_selesai_kontes)->format('d M Y') ?? '-' }}
                        </p>
                        <p class="card-text mt-2">Silakan pilih peserta untuk melakukan penilaian.</p>
                    </div>
                @else
                    <div class="alert alert-warning shadow-sm rounded-3">
                        <h5><i class="bi bi-exclamation-triangle me-2"></i>Tidak ada kontes aktif saat ini.</h5>
                        <p>Silakan hubungi admin jika ini adalah kesalahan sistem.</p>
                    </div>
                @endif

                {{-- Table Responsive --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle text-nowrap table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>No Juri</th>
                                <th>No Daftar</th>
                                <th>Nama Peserta</th>
                                <th>Pohon Bonsai</th>
                                <th>Status Penilaian</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pendaftarans as $index => $item)
                                @php
                                    $juri = \App\Models\Juri::where('user_id', Auth::id())->first();
                                    $sudahDinilai = $juri
                                        ? \App\Models\Nilai::sudahDinilai($item->bonsai_id, $juri->id)
                                        : false;
                                @endphp

                                <tr>
                                    {{-- Nomor urut yang mengikuti pagination --}}
                                    <td class="text-center">
                                        {{ ($pendaftarans->currentPage() - 1) * $pendaftarans->perPage() + $index + 1 }}
                                    </td>
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
                                        <div class="d-flex flex-wrap gap-1">
                                            <a href="{{ $sudahDinilai ? route('juri.nilai.edit', $item->bonsai_id) : route('juri.nilai.form', $item->bonsai_id) }}"
                                                class="btn btn-sm {{ $sudahDinilai ? 'btn-warning' : 'btn-primary' }}">
                                                {{ $sudahDinilai ? 'Edit Nilai' : 'Nilai' }}
                                            </a>

                                            @if ($sudahDinilai)
                                                <a href="{{ route('juri.nilai.show', $item->bonsai_id) }}"
                                                    class="btn btn-sm btn-info">
                                                    <i class="bi bi-eye"></i> Lihat
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $pendaftarans->links() }}
                </div>


            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Tambahkan JS jika perlu (misal DataTables)
    </script>
@endsection
