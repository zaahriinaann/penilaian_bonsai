@extends('layouts.app')

@section('title', 'Kelola Bonsai Saya')

@section('button-toolbar')
    {{-- Tombol Tambah Bonsai --}}
    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_create_bonsai">
        Tambah Bonsai Saya
    </button>
@endsection

@section('content')
    <div class="card">
        <div class="card-header align-items-center">
            <h5 class="mb-0">Data Bonsai Saya</h5>
        </div>
        <div class="card-body">
            {{-- Form Pencarian --}}
            <form method="GET" action="{{ route('peserta.bonsaiSaya.index') }}" class="mb-4">
                <div class="row g-2 align-items-center">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" name="search" class="form-control border-start-0"
                                placeholder="Cari nama pohon atau no induk..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-filter-circle"></i> Filter
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('peserta.bonsaiSaya.index') }}" class="btn btn-secondary w-100">
                            <i class="bi bi-x-circle"></i> Reset
                        </a>
                    </div>
                </div>
            </form>

            {{-- Tabel Bonsai --}}
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Foto</th>
                            <th>No Induk</th>
                            <th>Nama Pohon (Lokal / Latin)</th>
                            <th>Kelas</th>
                            <th>Ukuran</th>
                            <th>Masa Pemeliharaan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bonsai as $item)
                            <tr>
                                <td>{{ $loop->iteration + ($bonsai->currentPage() - 1) * $bonsai->perPage() }}</td>
                                <td>
                                    <img src="{{ $item->foto ? asset('assets/images/bonsai/' . $item->foto) : asset('assets/media/avatars/blank.png') }}"
                                        alt="Foto Bonsai" class="rounded" style="width:75px;height:75px;object-fit:cover;">
                                </td>
                                <td>{{ $item->no_induk_pohon }}</td>
                                <td style="min-width:200px;">
                                    <div>{{ $item->nama_pohon }}</div>
                                    <small class="text-muted">({{ $item->nama_lokal }}/{{ $item->nama_latin }})</small>
                                </td>
                                <td class="text-capitalize">{{ $item->kelas }}</td>
                                <td>{{ $item->ukuran }}</td>
                                <td>{{ $item->masa_pemeliharaan }} {{ $item->format_masa }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        {{-- Edit via modal --}}
                                        <button type="button" class="btn btn-sm btn-warning btn-edit"
                                            data-slug="{{ $item->slug }}" data-nama_pohon="{{ $item->nama_pohon }}"
                                            data-nama_lokal="{{ $item->nama_lokal }}"
                                            data-nama_latin="{{ $item->nama_latin }}"
                                            data-ukuran_1="{{ $item->ukuran_1 }}" data-ukuran_2="{{ $item->ukuran_2 }}"
                                            data-format_ukuran="{{ $item->format_ukuran }}"
                                            data-masa_pemeliharaan="{{ $item->masa_pemeliharaan }}"
                                            data-format_masa="{{ $item->format_masa }}" data-kelas="{{ $item->kelas }}"
                                            data-foto="{{ $item->foto }}" data-bs-toggle="modal"
                                            data-bs-target="#kt_modal_edit_bonsai" title="Edit Bonsai">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>

                                        {{-- Hapus via form --}}
                                        <form action="{{ route('peserta.bonsaiSaya.destroy', $item->slug) }}"
                                            method="POST" onsubmit="return confirm('Yakin ingin menghapus bonsai ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus Bonsai">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada data bonsai ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-3">
                {{ $bonsai->links('vendor.pagination.bootstrap-5') }}
            </div>
        </div>
    </div>

    {{-- Partial Modals --}}
    @include('peserta.bonsai-saya.partials.modal-create')
    @include('peserta.bonsai-saya.partials.modal-edit')
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.btn-edit').forEach(btn => {
                btn.addEventListener('click', () => {
                    const modal = document.getElementById('kt_modal_edit_bonsai');
                    const form = modal.querySelector('#form_edit_bonsai');

                    form.action = `/peserta/bonsai-saya/${btn.dataset.slug}`;
                    modal.querySelector('#edit_bonsai_slug').value = btn.dataset.slug;
                    modal.querySelector('#edit_nama_pohon').value = btn.dataset.nama_pohon;
                    modal.querySelector('#edit_nama_lokal').value = btn.dataset.nama_lokal;
                    modal.querySelector('#edit_nama_latin').value = btn.dataset.nama_latin;
                    modal.querySelector('#edit_ukuran_1').value = btn.dataset.ukuran_1;
                    modal.querySelector('#edit_ukuran_2').value = btn.dataset.ukuran_2;
                    modal.querySelector('#edit_format_ukuran').value = btn.dataset.format_ukuran;
                    modal.querySelector('#edit_masa_pemeliharaan').value = btn.dataset
                        .masa_pemeliharaan;
                    modal.querySelector('#edit_format_masa').value = btn.dataset.format_masa;
                    modal.querySelector('#edit_kelas').value = btn.dataset.kelas;

                    const preview = modal.querySelector('#edit_foto_preview');
                    const container = modal.querySelector('#edit_foto_container');
                    if (btn.dataset.foto) {
                        preview.src = `/assets/images/bonsai/${btn.dataset.foto}`;
                        container.style.display = 'block';
                    } else {
                        container.style.display = 'none';
                    }
                });
            });
        });
    </script>
@endsection
