@extends('layouts.app')

@section('title', 'Kelola Peserta Kontes')

@section('button-toolbar')
    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_create_peserta">
        Tambah Peserta
    </button>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5>Data Peserta</h5>
                <div class="d-flex align-items-center gap-2" id="search-wrapper">
                    <label for="search-input" class="w-25">Cari : </label>
                    <input type="text" id="search-input" class="form-control form-control-sm" placeholder="Cari data">
                </div>
            </div>
            <table class="table table-hover table-borderless table-responsive table-data">
                <thead class="align-middle">
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
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="align-middle">
                    @foreach ($dataRender as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <img class="rounded"
                                    src="{{ $item->foto ? asset('assets/images/peserta/' . $item->foto) : asset('assets/media/avatars/blank.png') }}"
                                    alt="Foto Peserta" style="width: 75px; height: 75px; object-fit: cover;">
                            </td>
                            <td>{{ $item->name ?? 'Belum Diisi' }}</td>
                            <td class="list-username">{{ $item->username ?? 'Belum Diisi' }}</td>
                            <td>{{ $item->no_anggota ?? 'Belum Diisi' }}</td>
                            <td>{{ $item->cabang ?? 'Belum Diisi' }}</td>
                            <td>{{ $item->no_hp ?? 'Belum Diisi' }}</td>
                            <td>{{ $item->email ?? 'Belum Diisi' }}</td>
                            <td>{{ $item->alamat ?? 'Belum Diisi' }}</td>
                            <td>
                                <div class="d-flex gap-2 m-0 p-0">
                                    <button type="button" class="btn btn-sm btn-warning btn-edit"
                                        data-id="{{ $item->id }}" data-nama="{{ $item->name }}"
                                        data-email="{{ $item->email }}" data-no_anggota="{{ $item->no_anggota }}"
                                        data-cabang="{{ $item->cabang }}" data-alamat="{{ $item->alamat }}"
                                        data-no_hp="{{ $item->no_hp }}" data-foto="{{ $item->foto }}"
                                        data-username="{{ $item->username }}" data-bs-toggle="modal"
                                        data-bs-target="#kt_modal_edit_peserta" title="Edit data">
                                        <i class="bi bi-pencil-square m-0 p-0"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger btn-delete" title="Hapus data"
                                        data-id="{{ $item->id }}"
                                        data-route="{{ route('master.peserta.destroy', $item->id) }}">
                                        <i class="bi bi-trash-fill m-0 p-0"></i></button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal --}}
    <div class="modal fade" id="kt_modal_create_peserta" tabindex="-1" aria-labelledby="kt_modal_create_peserta"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="kt_modal_create_peserta">Data Peserta Kontes</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('master.peserta.store') }}" enctype="multipart/form-data" method="POST">
                    @csrf
                    @method('POST')
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="nama" class="form-label">Nama</label>
                                <input type="text" class="form-control" name="nama" id="nama"
                                    aria-describedby="nama" title="Nama" placeholder="Masukkan Nama" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" id="username"
                                    aria-describedby="username" title="Username" placeholder="Masukkan Username" required>
                                <span class="msg-slug"></span>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" id="email"
                                    aria-describedby="email" title="Email" placeholder="Masukkan Email" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="no_anggota" class="form-label">No Anggota</label>
                                <input type="text" class="form-control" name="no_anggota" id="no_anggota"
                                    aria-describedby="no_anggota" title="No Anggota" placeholder="Masukkan No Anggota"
                                    required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="cabang" class="form-label">Cabang</label>
                                {{-- <input type="text" class="form-control" name="cabang" id="cabang"
                                    aria-describedby="cabang" title="Cabang" placeholder="Masukkan Cabang" required> --}}
                                <select id="cabang" name="cabang" class="cabang"></select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="no_hp" class="form-label">Nomor Hp</label>
                                <input type="text" class="form-control" name="no_hp" id="no_hp"
                                    aria-describedby="no_hp" title="Nomor Hp" placeholder="Masukkan Nomor Hp" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="alamat" class="form-label">Alamat</label>
                                <input type="text" class="form-control" name="alamat" id="alamat"
                                    aria-describedby="alamat" title="Alamat" placeholder="Masukkan Alamat" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="foto" class="form-label">Foto</label>
                                <input type="file" class="form-control" name="foto" id="foto"
                                    aria-describedby="foto" title="Foto" placeholder="Masukkan Foto">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="reset" id="reset-btn" class="btn btn-sm btn-danger"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <div class="modal fade" id="kt_modal_edit_peserta" tabindex="-1" aria-labelledby="kt_modal_edit_peserta"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Edit Data Peserta Kontes</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <form id="form_edit_peserta" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit_peserta_id">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="edit_nama" class="form-label">Nama</label>
                                <input type="text" class="form-control" name="nama" id="edit_nama"
                                    placeholder="Masukkan Nama Peserta">
                            </div>
                            <div class="d-flex gap-2 col">
                                <div class="w-100 mb-3">
                                    <label for="edit_username" class="form-label">Username</label>
                                    <input type="text" class="form-control" name="username" id="edit_username"
                                        placeholder="Masukkan Username Peserta">
                                    <span class="edit_msg-slug"></span>
                                </div>
                                <div class="w-100 mb-3">
                                    <label for="edit_email" class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" id="edit_email"
                                        placeholder="Masukkan Email Peserta">
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="edit_no_anggota" class="form-label">No Anggota</label>
                                <input type="text" class="form-control" name="no_anggota" id="edit_no_anggota"
                                    placeholder="Masukkan No Anggota Peserta">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="edit_cabang" class="form-label">Cabang</label>
                                {{-- <input type="text" class="form-control" name="cabang" id="edit_cabang"
                                    placeholder="Masukkan Cabang Peserta"> --}}
                                <select id="cabang" name="cabang" class="cabang"></select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="edit_no_hp" class="form-label">Nomor Hp</label>
                                <input type="text" class="form-control" name="no_hp" id="edit_no_hp"
                                    placeholder="Masukkan Nomor Hp Peserta">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="edit_alamat" class="form-label">Alamat</label>
                                <input type="text" class="form-control" name="alamat" id="edit_alamat"
                                    placeholder="Masukkan Alamat Peserta">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="edit_foto" class="form-label">Foto (Opsional)</label>
                                <input type="file" class="form-control" name="foto" id="edit_foto">
                                <input type="hidden" name="foto_lama" id="edit_foto_lama">
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" value="" id="gantiPassword">
                                    <label class="form-check-label" for="gantiPassword">
                                        Ganti Password
                                    </label>
                                </div>
                                <div class="input-group mb-3" id="form-password" style="display: none">
                                    <input type="password" id="input-password" class="form-control"
                                        placeholder="Masukkan Password" name="password" aria-label="Masukkan Password"
                                        aria-describedby="basic-addon1">
                                    <span class="input-group-text cursor-pointer" id="show-password">
                                        <i class="bi bi-eye-slash-fill d-none" id="hide-eye"></i>
                                        <i class="bi bi-eye-fill" id="show-eye"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="reset" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-primary">Perbarui</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        $(document).ready(() => {
            const province = @json($province);

            // Hide password change form on reset button click
            $('button[type="reset"]').on('click', () => {
                $('#form-password').hide();
            });

            // Ensure only numeric characters are inputted for phone number
            $('#no_hp').on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            // Select Modal Add
            $(".cabang").selectize({
                allowEmptyOption: true,
                placeholder: 'Pilih Cabang',
                theme: 'bootstrap-5',
                valueField: 'name',
                labelField: 'name',
                searchField: 'name',
                maxItems: 1,
                create: false,
                options: province,
            });

            // Populate the edit form with data when an edit button is clicked
            $('.btn-edit').on('click', function() {
                const btn = $(this);
                $('#form_edit_peserta').attr('action', '/master/peserta/' + btn.data('id'));
                $('#edit_peserta_id').val(btn.data('id'));
                $('#edit_nama').val(btn.data('nama'));
                $('#edit_username').val(btn.data('username'));
                $('#edit_email').val(btn.data('email'));
                $('#edit_no_anggota').val(btn.data('no_anggota'));
                $('#edit_cabang').val(btn.data('cabang'));
                $('#edit_alamat').val(btn.data('alamat'));
                $('#edit_no_hp').val(btn.data('no_hp'));
                $('#edit_foto_lama').val(btn.data('foto'));
                // Optionally, you can set the image preview if needed
                $(".cabang").selectize({
                    allowEmptyOption: true,
                    placeholder: btn.data('cabang'),
                    theme: 'bootstrap-5',
                    valueField: 'name',
                    labelField: 'name',
                    searchField: 'name',
                    maxItems: 1,
                    create: false,
                    options: province,
                });
            });


            // Handle username field changes (for slug check)
            $('#username, #edit_username').on('input', function() {
                const slug = generateSlug(this.value);
                console.log(slug);
                let slugExists = false;
                $('.list-username').each(function() {
                    if ($(this).text().trim() === slug) {
                        slugExists = true;
                        sameSlug = true;
                        return false;
                    }
                });

                // Show slug availability message
                const msgSlug = $('.msg-slug, .edit_msg-slug');
                if (slugExists) {
                    msgSlug.text('Username ini sudah dipakai. Silakan ubah nama agar unik.')
                        .css({
                            color: 'red',
                            fontSize: '12px'
                        });
                } else {
                    msgSlug.text('Username tersedia dan bisa digunakan.')
                        .css({
                            color: 'green',
                            fontSize: '12px'
                        });
                }
            });

            // Function to generate slug from input text
            function generateSlug(text) {
                return text.toLowerCase()
                    .replace(/ /g, '-')
                    .replace(/[^\w.-]+/g, '')
                    .replace(/--+/g, '-')
                    .replace(/^-+|-+$/g, '');
            }

            $('#gantiPassword').on('change', () => {
                $('#form-password').toggle();
            });

            // Toggle password visibility (show/hide)
            $('#show-password').on('click', () => {
                const passwordField = $('#input-password');
                const hideEye = $('#hide-eye');
                const showEye = $('#show-eye');

                if (hideEye.hasClass('d-none')) {
                    passwordField.prop('type', 'text');
                    hideEye.removeClass('d-none');
                    showEye.addClass('d-none');
                } else {
                    passwordField.prop('type', 'password');
                    hideEye.addClass('d-none');
                    showEye.removeClass('d-none');
                }
            });
        });
    </script>
@endsection
