@extends('layouts.app')

@section('title', 'Rekap Nilai Bonsai')

@section('button-toolbar')
    @if (isset($kontes))
        {{-- Tombol Cetak Laporan Rekap --}}
        <a href="{{ route('rekap-nilai.cetak-laporan', $kontes->id) }}" target="_blank" class="btn btn-danger btn-sm me-2">
            <i class="fas fa-print me-1"></i>
            Cetak Laporan Rekap
        </a>

        {{-- Tombol Generate Peringkat (Admin Only) --}}
        @if (auth()->user()->role === 'admin')
            <form action="{{ route('rekap-nilai.generateRanking', $kontes) }}" method="POST" class="d-inline">
                @csrf
                @php
                    $pending = $kontes->rekapNilai()->whereNull('peringkat')->count();
                @endphp
                <button type="button" class="btn btn-primary btn-sm btn-generate"
                    data-kontes-name="{{ $kontes->nama_kontes }}" @if ($pending === 0) disabled @endif>
                    <i class="bi bi-list-ol me-1"></i>
                    Generate Peringkat
                </button>
            </form>
        @endif
    @endif
@endsection

@section('content')
    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success mb-3">{{ session('success') }}</div>
        @endif

        {{-- Tabel 10 Besar --}}
        <div class="card mb-4 shadow-sm rounded-4">
            <div class="card-header align-items-center d-flex">
                <h4 class="mb-0"> <i class="bi bi-trophy-fill me-2"></i> 10 Besar Nilai Bonsai</h4>
            </div>
            <div class="card-body table-responsive">
                @if ($bestTen->count())
                    <table class="table table-hover table-bordered text-nowrap" id="top-ten-table">
                        <thead class="table-light">
                            <tr>
                                <th>Peringkat</th>
                                <th>No Juri</th>
                                <th>No Pendaftaran</th>
                                <th>Nama Pohon</th>
                                <th>Pemilik</th>
                                <th>Skor Akhir</th>
                                <th>Himpunan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bestTen as $i => $b)
                                <tr>
                                    <td>{{ $b->peringkat ?? $i + 1 }}</td>
                                    <td>{{ $b->nomor_juri }}</td>
                                    <td>{{ $b->nomor_pendaftaran }}</td>
                                    <td>{{ $b->nama_pohon }}</td>
                                    <td>{{ $b->pemilik }}</td>
                                    <td>{{ number_format($b->skor_akhir, 2) }}</td>
                                    <td>{{ $b->himpunan_akhir }}</td>
                                    <td>
                                        <a href="{{ route('rekap-nilai.show', $b->id) }}"
                                            class="btn btn-sm btn-info">Detail Rekap</a>
                                        <a href="{{ route('rekap-nilai.cetak-per-bonsai', $b->id) }}"
                                            class="btn btn-sm btn-danger" target="_blank">Cetak Rekap</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="alert alert-warning mb-0">Belum ada data 10 besar yang tersedia.</div>
                @endif
            </div>
        </div>

        {{-- Tabel Rekap Semua Peserta --}}
        <div class="card shadow-sm rounded-4">
            <div class="card-header align-items-center d-flex">
                <h4 class="mb-0">
                    <i class="bi bi-bar-chart-fill me-2"></i>
                    Rekap Nilai Semua Peserta
                </h4>
            </div>
            <div class="card-body">
                {{-- Form Pencarian --}}
                <form method="GET" action="{{ route('rekap-nilai.index') }}" class="mb-3">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" name="search" class="form-control border-start-0"
                                    placeholder="Cari nomor/pohon/pemilik..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-filter-circle"></i>
                                Filter</button>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('rekap-nilai.index') }}" class="btn btn-secondary w-100"><i
                                    class="bi bi-x-circle"></i> Reset</a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    @if ($rekap->count())
                        <table class="table table-hover table-bordered text-nowrap" id="rekap-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Peringkat</th>
                                    <th>No Juri</th>
                                    <th>No Pendaftaran</th>
                                    <th>Nama Pohon</th>
                                    <th>Pemilik</th>
                                    <th>Skor Akhir</th>
                                    <th>Himpunan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rekap as $i => $b)
                                    <tr>
                                        <td>{{ $b->peringkat ?? $rekap->firstItem() + $i }}</td>
                                        <td>{{ $b->nomor_juri }}</td>
                                        <td>{{ $b->nomor_pendaftaran }}</td>
                                        <td>{{ $b->nama_pohon }}</td>
                                        <td>{{ $b->pemilik }}</td>
                                        <td>{{ number_format($b->skor_akhir, 2) }}</td>
                                        <td>{{ $b->himpunan_akhir }}</td>
                                        <td>
                                            <a href="{{ route('rekap-nilai.show', $b->id) }}"
                                                class="btn btn-sm btn-info">Detail Rekap</a>
                                            <a href="{{ route('rekap-nilai.cetak-per-bonsai', $b->id) }}"
                                                class="btn btn-sm btn-danger" target="_blank">Cetak Rekap</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="mt-3">
                            {{ $rekap->appends(request()->query())->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    @else
                        <div class="alert alert-warning mb-0">Belum ada data rekap nilai yang tersedia.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    {{-- SweetAlert2 CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.btn-generate').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const nama = btn.getAttribute('data-kontes-name');
                    Swal.fire({
                        title: 'Generate Peringkat?',
                        text: `Generate peringkat untuk kontes "${nama}"?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Generate!',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            btn.closest('form').submit();
                        }
                    });
                });
            });
        });
    </script>
@endsection
