@extends('layouts.app')

@section('title', 'Kelola Bonsai Peserta')

@section('button-toolbar')
    {{-- Tombol Tambah Bonsai --}}
    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_create_bonsai">
        Tambah Bonsai Peserta
    </button>
@endsection

@section('content')
    <div class="card">
        <div class="card-header align-items-center">
            <h5>Data Bonsai Peserta</h5>
        </div>
        <div class="card-body">
            {{-- Form Pencarian --}}
            <form method="GET" action="{{ route('master.bonsai.index') }}" class="mb-3">
                <div class="row g-2 align-items-center">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" name="search" class="form-control border-start-0"
                                placeholder="Cari nama pohon/no induk..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-filter-circle"></i> Filter
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('master.bonsai.index') }}" class="btn btn-secondary w-100">
                            <i class="bi bi-x-circle"></i> Reset
                        </a>
                    </div>
                </div>
            </form>


            {{-- Tabel Responsif --}}
            <div class="table-responsive">
                <table class="table table-hover table-sm align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Foto</th>
                            <th>No.Induk Pohon</th>
                            <th>Nama Pohon (Lokal / Latin)</th>
                            <th>Kelas</th>
                            <th>Ukuran</th>
                            <th>Masa Pemeliharaan</th>
                            <th>Pemilik</th>
                            <th>No HP</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dataRender as $item)
                            <tr>
                                <td>{{ $loop->iteration + ($dataRender->currentPage() - 1) * $dataRender->perPage() }}</td>
                                <td>
                                    <img src="{{ $item->foto ? asset('assets/images/bonsai/' . $item->foto) : asset('assets/media/avatars/blank.png') }}"
                                        alt="Foto Bonsai" class="rounded"
                                        style="width: 75px; height: 75px; object-fit: cover;">
                                </td>
                                <td>{{ $item->no_induk_pohon }}</td>
                                <td style="min-width: 200px;">
                                    <div>
                                        <div>{{ $item->nama_pohon }}</div>
                                        <small class="text-muted">
                                            ({{ $item->nama_lokal }}/{{ $item->nama_latin }})
                                        </small>
                                    </div>
                                </td>
                                <td class="text-capitalize">{{ $item->kelas }}</td>
                                <td>{{ $item->ukuran }}</td>
                                <td>{{ $item->masa_pemeliharaan }} {{ $item->format_masa }}</td>
                                <td>{{ $item->user?->name }}</td>
                                <td>{{ $item->user?->no_hp }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button type="button" class="btn btn-sm btn-warning btn-edit"
                                            data-id="{{ $item->id }}" data-slug="{{ $item->slug }}"
                                            data-peserta="{{ $item->user_id }}" data-nama_pohon="{{ $item->nama_pohon }}"
                                            data-nama_lokal="{{ $item->nama_lokal }}"
                                            data-nama_latin="{{ $item->nama_latin }}"
                                            data-ukuran_1="{{ $item->ukuran_1 }}" data-ukuran_2="{{ $item->ukuran_2 }}"
                                            data-format_ukuran="{{ $item->format_ukuran }}"
                                            data-masa_pemeliharaan="{{ $item->masa_pemeliharaan }}"
                                            data-format_masa="{{ $item->format_masa }}" data-kelas="{{ $item->kelas }}"
                                            data-foto="{{ $item->foto }}" data-bs-toggle="modal"
                                            data-bs-target="#kt_modal_edit_bonsai" title="Edit data">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger btn-delete" title="Hapus data"
                                            data-id="{{ $item->id }}"
                                            data-route="{{ route('master.bonsai.destroy', $item->slug) }}">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">Tidak ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $dataRender->links('vendor.pagination.bootstrap-5') }}
            </div>

        </div>
    </div>

    {{-- Modal Create Bonsai --}}
    @include('admin.bonsai.partials.modal-create')

    {{-- Modal Edit Bonsai --}}
    @include('admin.bonsai.partials.modal-edit')
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            const pesertaSelect = $('.peserta').selectize({
                allowEmptyOption: true,
                placeholder: 'Pilih Peserta',
                theme: 'bootstrap-5',
                valueField: 'id',
                labelField: 'name',
                searchField: 'name',
                maxItems: 1,
                options: @json($user),
                onInitialize: function() {
                    const selectize = this;
                    selectize.clearOptions();
                    @foreach ($user as $u)
                        selectize.addOption({
                            id: '{{ $u->id }}',
                            name: '{{ $u->name }} / {{ $u->no_hp }} / {{ $u->no_anggota }}'
                        });
                    @endforeach
                    selectize.refreshOptions(false);
                }
            });

            // Edit handler untuk single modal-edit
            $('.btn-edit').on('click', function() {
                const btn = $(this);
                const modal = $('#kt_modal_edit_bonsai');

                modal.find('#form_edit_bonsai')
                    .attr('action', `/master/bonsai/${btn.data('slug')}`);
                modal.find('#edit_bonsai_slug').val(btn.data('slug'));
                modal.find('#peserta')[0].selectize.setValue(btn.data('peserta'));
                modal.find('#edit_nama_pohon').val(btn.data('nama_pohon'));
                modal.find('#edit_nama_lokal').val(btn.data('nama_lokal'));
                modal.find('#edit_nama_latin').val(btn.data('nama_latin'));
                modal.find('#edit_ukuran_1').val(btn.data('ukuran_1'));
                modal.find('#edit_ukuran_2').val(btn.data('ukuran_2'));
                modal.find('#edit_format_ukuran').val(btn.data('format_ukuran'));
                modal.find('#edit_masa_pemeliharaan').val(btn.data('masa_pemeliharaan'));
                modal.find('#edit_format_masa').val(btn.data('format_masa'));
                modal.find('#edit_kelas').val(btn.data('kelas'));

                // Foto preview logic
                const foto = btn.data('foto');
                const $container = modal.find('#edit_foto_container');
                const $preview = modal.find('#edit_foto_preview');
                if (foto) {
                    $preview
                        .attr('src', `/assets/images/bonsai/${foto}`)
                        .show();
                    $container.show();
                } else {
                    $preview.hide();
                    $container.hide();
                }
            });

            // Detail script slug-check & toggle password (jika ada)
        });
    </script>
@endsection
