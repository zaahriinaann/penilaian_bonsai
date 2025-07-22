@extends('layouts.app')

@section('title', 'Kelola Juri Kontes')

@section('button-toolbar')
    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_create_juri">
        Tambah Juri Kontes
    </button>
@endsection

@section('content')
    <div class="card">
        <div class="card-header align-items-center">
            <h5>Data Juri</h5>
        </div>
        <div class="card-body">
            {{-- Form pencarian --}}
            <form method="GET" action="{{ route('master.juri.index') }}" class="mb-3">
                <div class="row g-2 align-items-center">
                    {{-- Input search --}}
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" name="search" class="form-control border-start-0"
                                placeholder="Cari nama/email/username/telepon..." value="{{ request('search') }}">
                        </div>
                    </div>

                    {{-- Filter status --}}
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">-- Semua Status --</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Non Aktif</option>
                        </select>
                    </div>

                    {{-- Tombol --}}
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-filter-circle"></i> Filter
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('master.juri.index') }}" class="btn btn-secondary w-100">
                            <i class="bi bi-x-circle"></i> Reset
                        </a>
                    </div>
                </div>
            </form>


            <div class="table-responsive mt-3">
                <table class="table table-hover align-middle text-nowrap">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th>#</th>
                            <th>Foto</th>
                            <th>No.Induk & Status</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Username</th>
                            <th>Telepon</th>
                            <th>Sertifikat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($dataRender as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <img src="{{ $item->foto ? asset('assets/images/juri/' . $item->foto) : asset('assets/media/avatars/blank.png') }}"
                                        alt="Foto Juri" class="rounded"
                                        style="width: 60px; height: 60px; object-fit: cover;">
                                </td>
                                <td>
                                    <div>{{ $item->no_induk_juri }}</div>
                                    <span class="badge {{ $item->status == 1 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $item->status == 1 ? 'Aktif' : 'Non Aktif' }}
                                    </span>
                                </td>
                                <td>{{ $item->nama_juri }}</td>
                                <td>{{ $item->email }}</td>
                                <td class="list-username">{{ $item->username }}</td>
                                <td>{{ $item->no_telepon }}</td>
                                <td>
                                    <a class="btn btn-sm btn-outline-primary"
                                        href="{{ asset('sertifikat/' . $item->sertifikat) }}" target="_blank">
                                        <i class="bi bi-eye-fill"></i> Lihat
                                    </a>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        <button class="btn btn-sm btn-warning btn-edit" data-bs-toggle="modal"
                                            data-bs-target="#kt_modal_edit_juri" data-id="{{ $item->id }}"
                                            data-slug="{{ $item->slug }}" data-nama="{{ $item->nama_juri }}"
                                            data-email="{{ $item->email }}" data-no_telepon="{{ $item->no_telepon }}"
                                            data-status="{{ $item->status }}" data-foto="{{ $item->foto }}"
                                            data-no_induk="{{ $item->no_induk_juri }}"
                                            data-username="{{ $item->username }}"
                                            data-sertifikat="{{ $item->sertifikat }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger btn-delete" data-id="{{ $item->id }}"
                                            data-route="{{ route('master.juri.destroy', $item->slug) }}">
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
                {{ $dataRender->withQueryString()->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>

    @include('admin.juri.partials.modal-create')
    @include('admin.juri.partials.modal-edit')
@endsection

@section('script')
    <script>
        $(document).ready(() => {
            $('button[type="reset"]').on('click', () => {
                $('#form-password').hide();
            });

            $('#no_telepon, #edit_no_telepon').on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            $('.btn-edit').on('click', function() {
                const btn = $(this);
                $('#form_edit_juri').attr('action', '/master/juri/' + btn.data('slug'));
                $('#edit_juri_slug').val(btn.data('slug'));
                $('#edit_nama_juri').val(btn.data('nama'));
                $('#edit_email').val(btn.data('email'));
                $('#edit_no_telepon').val(btn.data('no_telepon'));
                $('#edit_status_juri').val(btn.data('status'));
                $('#edit_username').val(btn.data('username'));
                $('#edit_foto_lama').val(btn.data('foto'));
                $('#edit_sertifikat_lama').val(btn.data('sertifikat'));
            });

            $('#gantiPassword').on('change', () => {
                $('#form-password').toggle();
            });

            $('#show-password').on('click', () => {
                const input = $('#input-password');
                const hideEye = $('#hide-eye');
                const showEye = $('#show-eye');

                if (hideEye.hasClass('d-none')) {
                    input.attr('type', 'text');
                    hideEye.removeClass('d-none');
                    showEye.addClass('d-none');
                } else {
                    input.attr('type', 'password');
                    hideEye.addClass('d-none');
                    showEye.removeClass('d-none');
                }
            });

            $('#username, #edit_username').on('input', function() {
                const slug = generateSlug(this.value);
                let slugExists = false;
                $('.list-username').each(function() {
                    if ($(this).text().trim() === slug) {
                        slugExists = true;
                        return false;
                    }
                });

                const msg = $(this).attr('id') === 'username' ? $('.msg-slug') : $('.edit_msg-slug');
                if (slugExists) {
                    msg.text('Username ini sudah dipakai.').css({
                        color: 'red',
                        fontSize: '12px'
                    });
                } else {
                    msg.text('Username tersedia.').css({
                        color: 'green',
                        fontSize: '12px'
                    });
                }
            });

            function generateSlug(text) {
                return text.toLowerCase()
                    .replace(/ /g, '-')
                    .replace(/[^\w.-]+/g, '')
                    .replace(/--+/g, '-')
                    .replace(/^-+|-+$/g, '');
            }
        });
    </script>

@endsection
