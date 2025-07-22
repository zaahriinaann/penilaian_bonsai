@extends('layouts.app')

@section('title', 'Kelola Kontes Bonsai')

@section('button-toolbar')
    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_create_kontes">
        Tambah Kontes Bonsai
    </button>
@endsection

@section('content')
    <div class="card">
        <div class="card-header align-items-center">
            <h5>Data Kontes</h5>
        </div>
        <div class="card-body">
            {{-- Form pencarian --}}
            <form method="GET" action="{{ route('master.kontes.index') }}" class="mb-3">
                <div class="row g-2 align-items-center">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" name="search" class="form-control border-start-0"
                                placeholder="Cari nama/tempat/tingkat kontes..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-filter-circle"></i> Filter
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('master.kontes.index') }}" class="btn btn-secondary w-100">
                            <i class="bi bi-x-circle"></i> Reset
                        </a>
                    </div>
                </div>
            </form>

            <div class="table-responsive" style="overflow-x: auto;">
                <table class="table table-hover table-bordered align-middle text-nowrap">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Poster</th>
                            <th>Nama Kontes</th>
                            <th>Tingkat/Kelas</th>
                            <th>Tempat</th>
                            <th>Tanggal</th>
                            <th>Peserta</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($dataRender as $item)
                            <tr>
                                <td class="list-slug d-none">{{ $item->slug }}</td>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <img class="rounded img-fluid"
                                        src="{{ $item->poster_kontes ? asset('assets/images/kontes/' . $item->poster_kontes) : asset('assets/media/avatars/blank.png') }}"
                                        alt="Foto Kontes" style="max-width: 75px; height: auto; object-fit: cover;">
                                </td>
                                <td>
                                    <a class="fw-bold text-dark"
                                        href="{{ route('master.kontes.show', $item->slug) }}">{{ $item->nama_kontes }}</a>
                                </td>
                                <td>{{ $item->tingkat_kontes }}</td>
                                <td class="text-capitalize">
                                    {{ $item->tempat_kontes }}
                                    @if ($item->link_gmaps)
                                        <a href="{{ $item->link_gmaps }}" target="_blank" title="Lihat di Google Maps">
                                            <i class="bi bi-geo-alt-fill"></i>
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($item->tanggal_mulai_kontes)->locale('id')->translatedFormat('d F Y') }}
                                    -
                                    {{ \Carbon\Carbon::parse($item->tanggal_selesai_kontes)->locale('id')->translatedFormat('d F Y') }}
                                </td>
                                <td>{{ $item->jumlah_peserta }} Bonsai</td>
                                <td>
                                    <form action="{{ route('master.kontes.update', $item->slug) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <button
                                            class="btn btn-sm btn-{{ $item->status == 1 ? 'success' : 'secondary' }} w-100"
                                            type="submit" name="setActive">
                                            {{ $item->status == 1 ? 'Aktif' : 'Tidak Aktif' }}
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <div class="d-flex flex-column flex-md-row gap-1">
                                        {{-- Tombol Detail --}}
                                        <a href="{{ route('master.kontes.show', $item->slug) }}"
                                            class="btn btn-sm btn-info" title="Detail Kontes">
                                            <i class="fas fa-info-circle"></i>
                                        </a>

                                        {{-- Tombol Edit --}}
                                        <button type="button" class="btn btn-sm btn-warning btn-edit"
                                            data-id="{{ $item->id }}" data-nama="{{ $item->nama_kontes }}"
                                            data-tempat="{{ $item->tempat_kontes }}" data-link="{{ $item->link_gmaps }}"
                                            data-tanggal-mulai="{{ $item->tanggal_mulai_kontes }}"
                                            data-tanggal-selesai="{{ $item->tanggal_selesai_kontes }}"
                                            data-tingkat="{{ $item->tingkat_kontes }}"
                                            data-peserta="{{ $item->jumlah_peserta }}"
                                            data-harga="{{ $item->harga_tiket_kontes }}" data-slug="{{ $item->slug }}"
                                            data-poster="{{ $item->poster_kontes }}" data-bs-toggle="modal"
                                            data-bs-target="#kt_modal_edit_kontes" title="Edit data">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>

                                        {{-- Tombol Hapus --}}
                                        <button class="btn btn-sm btn-danger btn-delete" data-id="{{ $item->id }}"
                                            data-route="{{ route('master.kontes.destroy', $item->slug) }}"
                                            title="Hapus data">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </div>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($dataRender->hasPages())
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4">
                    <div class="text-muted mb-2 mb-md-0 small">
                        Menampilkan {{ $dataRender->firstItem() }} - {{ $dataRender->lastItem() }} dari total
                        {{ $dataRender->total() }} data
                    </div>

                    <div>
                        {{ $dataRender->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif

        </div>
    </div>

    {{-- Modal Tambah --}}
    <div class="modal fade" id="kt_modal_create_kontes" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                @include('admin.kontes.partials.modal-create')
            </div>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div class="modal fade" id="kt_modal_edit_kontes" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                @include('admin.kontes.partials.modal-edit')
            </div>
        </div>
    </div>

    {{-- Modal Panduan --}}
    <div class="modal fade" id="kt_modal_panduan_kontes" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-fullscreen-sm-down">
            <div class="modal-content">
                @include('admin.kontes.partials.modal-panduan')
            </div>
        </div>
    </div>

    {{-- Modal Panduan Edit --}}
    <div class="modal fade" id="kt_modal_edit_panduan_kontes" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-fullscreen-sm-down">
            <div class="modal-content">
                @include('admin.kontes.partials.modal-edit-panduan')
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(() => {

            // ===================== UTILITAS ======================
            function generateSlug(text) {
                return text.toLowerCase()
                    .replace(/ /g, '-')
                    .replace(/[^\w-]+/g, '')
                    .replace(/--+/g, '-')
                    .replace(/^-+|-+$/g, '');
            }

            function formatHarga(value) {
                const angka = value.toString().replace(/[^\d]/g, '');
                return 'Rp' + angka.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            function setJumlahPeserta(tingkat, inputSelector, textSelector) {
                if (tingkat.toLowerCase() === 'madya') {
                    $(inputSelector).prop({
                        min: 30,
                        placeholder: 30
                    });
                    $(textSelector).text('Minimal 30 (tiga puluh) Bonsai/Peserta');
                } else if (tingkat.toLowerCase() === 'utama') {
                    $(inputSelector).prop({
                        min: 20,
                        placeholder: 20
                    });
                    $(textSelector).text('Minimal 20 (dua puluh) Bonsai/Peserta');
                } else {
                    $(inputSelector).prop('min', 0);
                    $(textSelector).text('');
                }
            }

            // ===================== TAMBAH KONTEST ======================
            $('#link_gmaps_checkbox').on('change', function() {
                $('#form_gmaps').toggleClass('d-none', !this.checked);
            });

            $('#reset-btn').on('click', () => {
                $('#form_gmaps').addClass('d-none');
            });

            $('.nama_kontes, .edit_nama_kontes').on('change', function() {
                const slug = generateSlug(this.value);
                let slugExists = false;

                $('.list-slug').each(function() {
                    if ($(this).text().trim() === slug) {
                        slugExists = true;
                        return false;
                    }
                });

                if (slugExists) {
                    $('.msg-slug, .edit_msg-slug').text(
                            'Nama kontes ini sudah dipakai. Silakan ubah nama agar unik.')
                        .css({
                            color: 'red',
                            fontSize: '12px'
                        });
                } else {
                    $('.msg-slug, .edit_msg-slug').text('Nama kontes tersedia dan bisa digunakan.')
                        .css({
                            color: 'green',
                            fontSize: '12px'
                        });
                }
            });

            const today = new Date().toLocaleDateString('fr-ca');
            $('#tanggal_mulai_kontes, #tanggal_selesai_kontes').attr('min', today + 'T00:00');

            $('#fee').on('change', function() {
                const isChecked = this.checked;
                $('#form_tiket_kontes').toggleClass('d-none', !isChecked);
                if (!isChecked) $('#harga_tiket_kontes').val(0);
            });

            $('#harga_tiket_kontes, #edit_harga_tiket_kontes').on('input', function() {
                this.value = formatHarga(this.value);
            });

            $('#tingkat_kontes').on('change', function() {
                const tingkat = this.value;
                setJumlahPeserta(tingkat, '#jumlah_peserta', '#jumlah_peserta_text');
            });

            // ===================== EDIT KONTEST ======================
            $('.btn-edit').on('click', function() {
                const btn = $(this);

                const slug = btn.data('slug');
                const nama = btn.data('nama');
                const tempat = btn.data('tempat');
                const tanggalMulai = btn.data('tanggal-mulai');
                const tanggalSelesai = btn.data('tanggal-selesai');
                const peserta = btn.data('peserta');
                const tingkat = btn.data('tingkat') || '';
                const tingkatCapital = tingkat.charAt(0).toUpperCase() + tingkat.slice(1).toLowerCase();
                const link = btn.data('link') || '';
                const harga = btn.data('harga') || 0;
                const poster = btn.data('poster') || '';

                $('#form_edit_kontes').attr('action', '/master/kontes/' + slug);
                $('#edit_kontes_slug').val(slug);
                $('#edit_nama_kontes').val(nama);
                $('#edit_tempat_kontes').val(tempat);
                $('#edit_link_gmaps').val(link);
                $('#edit_tanggal_mulai_kontes').val(tanggalMulai);
                $('#edit_tanggal_selesai_kontes').val(tanggalSelesai);
                $('#edit_jumlah_peserta').val(peserta);
                $('#edit_tingkat_kontes').val(tingkatCapital).trigger('change');
                $('#edit_poster_kontes_lama').val(poster);
                $('#edit_harga_tiket_kontes').val(formatHarga(harga));

                $('#edit_tingkat_kontes').on('change', function() {
                    const tingkat = this.value;
                    setJumlahPeserta(tingkat, '#edit_jumlah_peserta', '#edit_jumlah_peserta_text');
                });

                setJumlahPeserta(tingkatCapital, '#edit_jumlah_peserta', '#edit_jumlah_peserta_text');

                const hasLink = !!link;
                $('#edit_link_gmaps_checkbox').prop('checked', hasLink);
                $('#edit_form_gmaps').toggleClass('d-none', !hasLink);
            });

            $('#edit_link_gmaps_checkbox').on('change', function() {
                $('#edit_form_gmaps').toggleClass('d-none', !this.checked);
            });

            // Scroll reset saat modal tambah muncul
            $('#kt_modal_create_kontes').on('shown.bs.modal', () => {
                const area = document.querySelector('#kt_modal_create_kontes .modal-body');
                if (area) area.scrollTop = 0;
            });
        });
    </script>
@endsection

@section('style')
    <style>
        /* Modal scroll */
        .modal-dialog-scrollable .modal-body {
            max-height: calc(100vh - 200px);
            overflow-y: auto;
        }

        /* Pagination styling */
        .pagination {
            margin-bottom: 0;
        }

        .pagination .page-item .page-link {
            border-radius: 6px !important;
            margin: 0 2px;
            padding: 6px 12px;
            color: #495057;
            font-weight: 500;
        }

        .pagination .page-item.active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: white;
        }

        .pagination .page-item.disabled .page-link {
            opacity: 0.4;
        }
    </style>
@endsection
