@extends('layouts.app')

@section('title', 'Kelola Peserta Kontes')

@section('button-toolbar')
    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_create_peserta">
        Tambah Peserta
    </button>
@endsection

@section('content')
    <div class="card">
        <div class="card-header align-items-center">
            <h5>Data Peserta</h5>
        </div>
        <div class="card-body">
            {{-- Form Pencarian --}}
            <form method="GET" action="{{ route('master.peserta.index') }}" class="mb-3">
                <div class="row g-2 align-items-center">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" name="search" class="form-control border-start-0"
                                placeholder="Cari nama/email/username/anggota..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-filter-circle"></i> Filter
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('master.peserta.index') }}" class="btn btn-secondary w-100">
                            <i class="bi bi-x-circle"></i> Reset
                        </a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover table-borderless align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Foto</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>No Anggota</th>
                            <th>Cabang</th>
                            <th>No Hp</th>
                            <th>Email</th>
                            <th>Alamat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($dataRender as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <img class="rounded img-thumbnail"
                                        src="{{ $item->foto ? asset('assets/images/peserta/' . $item->foto) : asset('assets/media/avatars/blank.png') }}"
                                        alt="Foto Peserta" style="width: 60px; height: 60px; object-fit: cover;">
                                </td>
                                <td>{{ $item->name ?? 'Belum Diisi' }}</td>
                                <td class="list-username">{{ $item->username ?? 'Belum Diisi' }}</td>
                                <td>{{ $item->no_anggota ?? 'Belum Diisi' }}</td>
                                <td>{{ $item->cabang ?? 'Belum Diisi' }}</td>
                                <td>{{ $item->no_hp ?? 'Belum Diisi' }}</td>
                                <td>{{ $item->email ?? 'Belum Diisi' }}</td>
                                <td>{{ $item->alamat ?? 'Belum Diisi' }}</td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        <button type="button" class="btn btn-sm btn-warning btn-edit"
                                            data-id="{{ $item->id }}" data-nama="{{ $item->name }}"
                                            data-email="{{ $item->email }}" data-no_anggota="{{ $item->no_anggota }}"
                                            data-cabang="{{ $item->cabang }}" data-alamat="{{ $item->alamat }}"
                                            data-no_hp="{{ $item->no_hp }}" data-foto="{{ $item->foto }}"
                                            data-username="{{ $item->username }}" data-bs-toggle="modal"
                                            data-bs-target="#kt_modal_edit_peserta" title="Edit data">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger btn-delete" title="Hapus data"
                                            data-id="{{ $item->id }}"
                                            data-route="{{ route('master.peserta.destroy', $item->id) }}">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $dataRender->links('vendor.pagination.bootstrap-5') }}
            </div>

        </div>
    </div>

    {{-- Include Modals --}}
    @include('admin.peserta.partials.modal-create')
    @include('admin.peserta.partials.modal-edit')
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            // 1. Reset form Create tiap modal tampil
            $('#kt_modal_create_peserta').on('show.bs.modal', function() {
                $(this).find('form')[0].reset();
                // kosongkan datalist-input Cabang
                $('#cabang_input').val('');
            });

            // 2. Isi data di modal Edit saat tombol diklik
            $('.btn-edit').on('click', function() {
                const btn = $(this);
                const modal = $('#kt_modal_edit_peserta');

                // set action URL
                modal.find('#form_edit_peserta')
                    .attr('action', `/master/peserta/${btn.data('id')}`);

                // isi field hidden
                modal.find('#edit_peserta_id').val(btn.data('id'));

                // isi field teks
                modal.find('#edit_nama').val(btn.data('nama'));
                modal.find('#edit_username').val(btn.data('username'));
                modal.find('#edit_email').val(btn.data('email'));
                modal.find('#edit_no_anggota').val(btn.data('no_anggota'));
                modal.find('#edit_alamat').val(btn.data('alamat'));
                modal.find('#edit_no_hp').val(btn.data('no_hp'));
                modal.find('#edit_foto_lama').val(btn.data('foto'));

                // isi Cabang (datalist)
                modal.find('#edit_cabang_input').val(btn.data('cabang'));

                // reset toggle password
                modal.find('#form-password').hide();
                modal.find('#gantiPassword').prop('checked', false);
            });

            // 3. Username slug‐check realtime
            $('#username, #edit_username').on('input', function() {
                const slug = generateSlug($(this).val());
                let exists = false;
                $('.list-username').each(function() {
                    if ($(this).text().trim() === slug) {
                        exists = true;
                        return false;
                    }
                });
                const msg = this.id === 'username' ?
                    $('.msg-slug') :
                    $('.edit_msg-slug');
                msg.text(exists ? 'Username ini sudah dipakai.' : 'Username tersedia.')
                    .css({
                        color: exists ? 'red' : 'green',
                        fontSize: '12px'
                    });
            });

            function generateSlug(text) {
                return text.toLowerCase()
                    .replace(/ /g, '-')
                    .replace(/[^\w.-]+/g, '')
                    .replace(/--+/g, '-')
                    .replace(/^-+|-+$/g, '');
            }

            // 4. Toggle form ganti password
            $('#gantiPassword').on('change', function() {
                $('#form-password').toggle();
            });

            // 5. Show/hide password field
            $('#show-password').on('click', function() {
                const field = $('#input-password');
                const hideEye = $('#hide-eye');
                const showEye = $('#show-eye');
                if (field.attr('type') === 'password') {
                    field.attr('type', 'text');
                    hideEye.removeClass('d-none');
                    showEye.addClass('d-none');
                } else {
                    field.attr('type', 'password');
                    hideEye.addClass('d-none');
                    showEye.removeClass('d-none');
                }
            });
        });
    </script>
@endsection
