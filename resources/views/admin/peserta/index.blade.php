@extends('layouts.app')

@section('title', 'Kelola Juri Kontes')

@section('button-toolbar')
    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_create_peserta">
        Tambah Peserta
    </button>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5>Data Juri</h5>
                <div class="d-flex align-items-center gap-2" id="search-wrapper">
                    <label for="search-input" class="w-25">Cari : </label>
                    <input type="text" id="search-input" class="form-control form-control-sm" placeholder="Cari data">
                </div>
            </div>
            <table class="table table-hover table-borderless table-responsive table-data">
                <thead class="align-middle">
                    <tr>
                        <th hidden></th>
                        <th>#</th>
                        <th>Foto</th>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>No Anggota</th>
                        <th>Cabang</th>
                        <th>Alamat</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="align-middle">
                    @forelse ($dataRender as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <img class="rounded-circle"
                                    src="{{ asset('images/peserta/' . $item->foto) ?? asset('assets/media/avatars/blank.png') }}"
                                    alt="Foto Peserta" style="width: 75px; height: 75px; object-fit: cover;">
                            </td>
                            <td>{{ $item->name ?? 'Belum Diisi' }}</td>
                            <td>{{ $item->username ?? 'Belum Diisi' }}</td>
                            <td>{{ $item->no_anggota ?? 'Belum Diisi' }}</td>
                            <td>{{ $item->cabang ?? 'Belum Diisi' }}</td>
                            <td>{{ $item->alamat ?? 'Belum Diisi' }}</td>
                            <td>{{ $item->email ?? 'Belum Diisi' }}</td>
                            <td>
                                <div class="d-flex gap-2 m-0 p-0">
                                    <button type="button" class="btn btn-sm btn-warning btn-edit"
                                        data-id="{{ $item->id }}" data-slug="{{ $item->slug }}"
                                        data-nama="{{ $item->nama_juri }}" data-email="{{ $item->email }}"
                                        data-no_telepon="{{ $item->no_telepon }}" data-status="{{ $item->status }}"
                                        data-foto="{{ $item->foto }}" data-no_induk="{{ $item->no_induk_juri }}"
                                        data-username="{{ $item->username }}" data-foto="{{ $item->foto }}"
                                        data-bs-toggle="modal" data-bs-target="#kt_modal_edit_juri" title="Edit data">
                                        <i class="bi bi-pencil-square m-0 p-0"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger btn-delete" title="Hapus data"
                                        data-id="{{ $item->id }}" data-route="">
                                        <i class="bi bi-trash-fill m-0 p-0"></i></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center no-data">Data tidak tersedia</td>
                        </tr>
                    @endforelse
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
                    <h1 class="modal-title fs-5" id="kt_modal_create_peserta">Data Juri Kontes</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('peserta.store') }}" enctype="multipart/form-data" method="POST">
                    @csrf
                    @method('POST')
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="nama" class="form-label">Nama</label>
                                <input type="text" class="form-control" name="nama" id="nama"
                                    aria-describedby="nama" title="Nama" placeholder="Masukkan Nama">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" id="username"
                                    aria-describedby="username" title="Usernam" placeholder="Masukkan Usernam">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" class="form-control" name="email" id="email"
                                    aria-describedby="email" title="email" placeholder="Masukkan Cabang">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="no_anggota" class="form-label">No Anggota</label>
                                <input type="text" class="form-control" name="no_anggota" id="no_anggota"
                                    aria-describedby="no_anggota" title="No Anggota" placeholder="Masukkan No Anggota">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="cabang" class="form-label">Cabang</label>
                                <input type="text" class="form-control" name="cabang" id="cabang"
                                    aria-describedby="cabang" title="Cabang" placeholder="Masukkan Cabang">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="no_hp" class="form-label">Nomor Hp</label>
                                <input type="text" class="form-control" name="no_hp" id="no_hp"
                                    aria-describedby="no_hp" title="Nomor Hp" placeholder="Masukkan Nomor Hp">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="alamat" class="form-label">Alamat</label>
                                <input type="text" class="form-control" name="alamat" id="alamat"
                                    aria-describedby="alamat" title="alamat" placeholder="Masukkan Cabang">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="foto" class="form-label">Foto</label>
                                <input type="file" class="form-control" name="foto" id="foto"
                                    aria-describedby="foto" title="alamat" placeholder="Masukkan Cabang">
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

    <div class="modal fade" id="kt_modal_edit_juri" tabindex="-1" aria-labelledby="kt_modal_edit_juri"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Edit Data Juri Kontes</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <form id="form_edit_juri" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" name="slug" id="edit_juri_slug">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="edit_nama_juri" class="form-label">Nama Juri</label>
                                <input type="text" class="form-control" name="nama_juri" id="edit_nama_juri"
                                    placeholder="Masukkan Nama Juri">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="edit_email" class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" id="edit_email"
                                    placeholder="Masukkan Email Juri">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="edit_username" class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" id="edit_username"
                                    title="Username Juri" placeholder="Masukkan Username Juri">
                                <span class="edit_msg-slug"></span>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="edit_no_telepon" class="form-label">No Telepon</label>
                                <input type="text" class="form-control" name="no_telepon" id="edit_no_telepon"
                                    placeholder="Masukkan No Telepon Juri">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="edit_status_juri" class="form-label">Status Juri</label>
                                <select name="status" id="edit_status_juri" class="form-select">
                                    <option disabled>Pilih Status Juri</option>
                                    <option value="1">Aktif</option>
                                    <option value="2">Non Aktif</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="edit_foto" class="form-label">Foto Juri (Opsional)</label>
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
            // Hide password change form on reset button click
            $('button[type="reset"]').on('click', () => {
                $('#form-password').hide();
            });

            // Ensure only numeric characters are inputted for phone number
            $('#no_telepon_juri').on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            // Populate the edit form with data when an edit button is clicked
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
            });

            // Handle username field changes (for slug check)
            $('#username, #edit_username').on('input', function() {
                const slug = generateSlug(this.value);
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
        });
    </script>
@endsection
