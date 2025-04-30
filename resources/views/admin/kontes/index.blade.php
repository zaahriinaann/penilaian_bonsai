@extends('layouts.app')

@section('title', 'Kelola Kontes Bonsai')

@section('button-toolbar')
    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_create_kontes">
        Tambah Kontes Bonsai
    </button>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5>Data Kontes</h5>
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
                        <th>Poster</th>
                        <th>Nama Kontes</th>
                        <th>Tingkat</th>
                        <th>Tempat/Lokasi Kontes</th>
                        <th>Tanggal</th>
                        <th>Jumlah Peserta/Bonsai</th>
                        <th>Harga</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="align-middle">
                    @forelse ($dataRender as $item)
                        <tr>
                            <td class="list-slug" hidden>{{ $item->slug }}</td>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <img src="https://st2.depositphotos.com/1561359/12101/v/950/depositphotos_121012076-stock-illustration-blank-photo-icon.jpg"
                                    alt="Poster Kontes" class="rounded"
                                    style="width: 75px; height: 75px; object-fit: cover;">
                            </td>
                            <td>
                                <a class="text-dark fw-bold"
                                    href="{{ route('kontes.show', $item->slug) }}">{{ $item->nama_kontes }}</a>
                            </td>
                            <td>{{ $item->tingkat_kontes }}</td>
                            <td>{{ $item->tempat_kontes }}
                                @if ($item->link_gmaps)
                                    <a href="{{ $item->link_gmaps }}" target="_blank" title="Lihat di google maps">
                                        <i class="bi bi-geo-alt-fill"></i>
                                    </a>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_mulai_kontes)->locale('id')->timezone('Asia/Jakarta')->translatedFormat('d F Y') . ' - ' . \Carbon\Carbon::parse($item->tanggal_selesai_kontes)->locale('id')->timezone('Asia/Jakarta')->translatedFormat('d F Y') }}
                            </td>
                            <td>{{ $item->jumlah_peserta }} Peserta/Bonsai</td>
                            <td>Rp{{ number_format($item->harga_tiket_kontes, 0, ',', '.') }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button type="button" class="btn btn-sm btn-warning btn-edit"
                                        data-id="{{ $item->id }}" data-nama="{{ $item->nama_kontes }}"
                                        data-tempat="{{ $item->tempat_kontes }}" data-link="{{ $item->link_gmaps }}"
                                        data-tanggal-mulai="{{ $item->tanggal_mulai_kontes }}"
                                        data-tanggal-selesai="{{ $item->tanggal_selesai_kontes }}"
                                        data-tingkat="{{ $item->tingkat_kontes }}"
                                        data-peserta="{{ $item->jumlah_peserta }}"
                                        data-harga="{{ $item->harga_tiket_kontes }}" data-slug="{{ $item->slug }}"
                                        data-bs-toggle="modal" data-bs-target="#kt_modal_edit_kontes" title="Edit data">
                                        <i class="bi bi-pencil-square m-0 p-0"></i>
                                    </button>

                                    {{-- <form action="{{ route('kontes'. $item->id) }}" method="D"></form> --}}
                                    <button class="btn btn-sm btn-danger btn-delete" title="Hapus data"
                                        data-id="{{ $item->id }}"
                                        data-route="{{ route('kontes.destroy', $item->slug) }}">
                                        <i class="bi bi-trash-fill m-0 p-0"></i></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center no-data">Data tidak tersedia</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- modal --}}
    <div class="modal fade" id="kt_modal_create_kontes" tabindex="-1" aria-labelledby="kt_modal_create_kontes"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="kt_modal_create_kontes">Data Kontes Bonsai</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('kontes.store') }}" enctype="multipart/form-data" method="POST">
                    @csrf
                    @method('POST')
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="nama_kontes" class="form-label">Nama Kontes</label>
                                <input type="text" class="form-control nama_kontes" name="nama_kontes" id="nama_kontes"
                                    aria-describedby="nama_kontes" title="Nama Kontes" placeholder="Nama Kontes">
                                <span class="msg-slug"></span>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="d-flex gap-2 justify-content-between">
                                    <label for="tempat_kontes" class="form-label">Tempat Kontes</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value=""
                                            id="link_gmaps_checkbox">
                                        <label class="form-check-label" for="link_gmaps_checkbox">
                                            Google Maps
                                        </label>
                                    </div>
                                </div>
                                <textarea class="form-control" name="tempat_kontes" id="tempat_kontes" aria-describedby="tempat_kontes"
                                    title="Alamat Lengkap Tempat Kontes" placeholder="Alamat Lengkap Tempat Kontes" cols="3" rows="3"></textarea>
                            </div>
                            <div class="col-md-12 mb-3 d-none" id="form_gmaps">
                                <label for="link_gmaps" class="form-label">Link Gmaps</label>
                                <input type="text" class="form-control" name="link_gmaps" id="link_gmaps"
                                    aria-describedby="link_gmaps" title="Link Google Maps Tempat Kontes"
                                    placeholder="Link Google Maps Tempat Kontes">
                            </div>
                            <div class="col-12 mb-3">
                                <label for="tanggal_kontes" class="form-label">Tanggal Kontes</label>
                                <div class="d-flex gap-2 align-items-center">
                                    <input type="datetime-local" class="form-control" name="tanggal_mulai_kontes"
                                        id="tanggal_mulai_kontes" aria-describedby="tanggal_mulai_kontes"
                                        title="Tanggal Kontes">
                                    <span>s/d</span>
                                    <input type="datetime-local" class="form-control" name="tanggal_selesai_kontes"
                                        id="tanggal_selesai_kontes" aria-describedby="tanggal_selesai_kontes"
                                        title="Tanggal Kontes">
                                </div>
                            </div>
                            <div class="col-md-12 mb-3" id="form_tingkat_kontes">
                                <label for="tingkat_kontes" class="form-label d-flex gap-2 align-items-center">
                                    Tingkat Kontes
                                    <i class="bi bi-question-circle-fill cursor-pointer" title="Lihat Panduan"
                                        data-bs-toggle="modal" data-bs-target="#kt_modal_panduan_kontes"></i>
                                </label>
                                <select name="tingkat_kontes" id="tingkat_kontes" class="form-select form-control">
                                    <option selected disabled>Pilih Tingkat Kontes</option>
                                    <option value="madya">Madya</option>
                                    <option value="utama">Utama</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="jumlah_peserta"
                                    class="form-label d-flex justify-content-between align-items-center">
                                    <span>Jumlah Peserta/Bonsai</span>
                                    <small id="jumlah_peserta_text" class="text-danger"></small></label>
                                <input type="number" class="form-control" name="jumlah_peserta" id="jumlah_peserta"
                                    aria-describedby="jumlah_peserta" title="Jumlah Peserta/Bonsai" placeholder="100">
                            </div>
                            <div class="col-md-12 mb-3" id="form_tiket_kontes">
                                <label for="harga_tiket_kontes" class="form-label">Harga Tiket Kontes</label>
                                <input type="text" class="form-control" name="harga_tiket_kontes"
                                    id="harga_tiket_kontes" aria-describedby="harga_tiket_kontes"
                                    title="Harga Tiket Kontes" placeholder="Rp0">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="poster_kontes" class="form-label">Poster Kontes</label>
                                <input type="file" class="form-control" name="poster_kontes" id="poster_kontes">
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

    <div class="modal fade" id="kt_modal_edit_kontes" tabindex="-1" aria-labelledby="kt_modal_edit_kontes"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Edit Kontes Bonsai</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form_edit_kontes" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" name="slug" id="edit_kontes_slug">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="edit_nama_kontes" class="form-label">Nama Kontes</label>
                                <input type="text" class="form-control edit_nama_kontes" name="nama_kontes"
                                    id="edit_nama_kontes">
                                <span class="edit_msg-slug"></span>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="d-flex gap-2 justify-content-between">
                                    <label for="edit_tempat_kontes" class="form-label">Tempat Kontes</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="edit_link_gmaps_checkbox">
                                        <label class="form-check-label" for="edit_link_gmaps_checkbox">Google
                                            Maps</label>
                                    </div>
                                </div>
                                {{-- <input type="text" class="form-control" name="tempat_kontes"
                                        id="edit_tempat_kontes"> --}}
                                <textarea class="form-control" name="tempat_kontes" id="edit_tempat_kontes" aria-describedby="tempat_kontes"
                                    title="Alamat Lengkap Tempat Kontes" placeholder="Alamat Lengkap Tempat Kontes" cols="3" rows="3"></textarea>
                            </div>
                            <div class="col-md-12 mb-3 d-none" id="edit_form_gmaps">
                                <label for="edit_link_gmaps" class="form-label">Link Gmaps</label>
                                <input type="text" class="form-control" name="link_gmaps" id="edit_link_gmaps">
                            </div>
                            <div class="col-12 mb-3">
                                <label for="edit_tanggal_mulai_kontes" class="form-label">Tanggal Kontes</label>
                                <div class="d-flex gap-2 align-items-center">
                                    <input type="datetime-local" class="form-control" name="tanggal_mulai_kontes"
                                        id="edit_tanggal_mulai_kontes">
                                    <span>s/d</span>
                                    <input type="datetime-local" class="form-control" name="tanggal_selesai_kontes"
                                        id="edit_tanggal_selesai_kontes">
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="edit_tingkat_kontes" class="form-label d-flex gap-2 align-items-center">
                                    Tingkat Kontes
                                    <i class="bi bi-question-circle-fill cursor-pointer" title="Lihat Panduan"
                                        data-bs-toggle="modal" data-bs-target="#kt_modal_edit_panduan_kontes"></i>
                                </label>
                                <select name="tingkat_kontes" id="edit_tingkat_kontes" class="form-select">
                                    <option selected>Pilih Tingkat Kontes</option>
                                    <option value="madya">Madya</option>
                                    <option value="utama">Utama</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="edit_jumlah_peserta"
                                    class="form-label d-flex justify-content-between align-items-center">
                                    <span>Jumlah Peserta/Bonsai</span>
                                    <small id="edit_jumlah_peserta_text" class="text-danger"></small></label>
                                <input type="number" class="form-control" name="jumlah_peserta"
                                    id="edit_jumlah_peserta">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="edit_harga_tiket_kontes" class="form-label">Harga Tiket Kontes</label>
                                <input type="text" class="form-control" name="harga_tiket_kontes"
                                    id="edit_harga_tiket_kontes">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="edit_poster_kontes" class="form-label">Poster Kontes (Opsional)</label>
                                <input type="file" class="form-control" name="poster_kontes" id="edit_poster_kontes">
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

    <div class="modal fade" id="kt_modal_panduan_kontes" tabindex="-1" aria-labelledby="kt_modal_panduan_kontes"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="kt_modal_create_kontes">Panduan Untuk Penyelenggara Kontes Bonsai
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th>Tingkat Kontes</th>
                                    <th>Persyaratan Bonsai</th>
                                    <th>Jumlah Peserta/Bonsai</th>
                                    <th>Hasil Kontes</th>
                                    <th>Juri</th>
                                    <th>Anggota Dewan Juri</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Madya</td>
                                    <td>Semua Bonsai</td>
                                    <td>Minimal 30 (tiga puluh) Bonsai</td>
                                    <td>
                                        <ol>
                                            <li>Buku Penilaian Bonsain (BPB)</li>
                                            <li>Sertifikat Tingkat Madya untuk predikat Baik dan Baik Sekali</li>
                                        </ol>
                                    </td>
                                    <td>
                                        Jumlah juri 5 (lima) orang terdiri dari beberapa Juri Madya dan minimal 1 (satu)
                                        orang
                                        Juri Utama
                                    </td>
                                    <td>
                                        Minimal 1 (satu) orang Anggota Dewan Juri
                                    </td>
                                </tr>
                                <tr>
                                    <td>Utama</td>
                                    <td>
                                        Bonsai yang telah mempunyai sertifikat Utama atau Madya dengan predikat Baik Sekali
                                    </td>
                                    <td>Minimal 20 (dua puluh) Bonsai</td>
                                    <td>
                                        Sertifikat Tingkat Utama untuk predikat Baik dan Baik Sekali
                                    </td>
                                    <td>
                                        Jumlah juri 5 (lima) orang Juri Utama
                                    </td>
                                    <td>
                                        Minimal 1 (satu) orang Anggota Dewan Juri
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button data-bs-toggle="modal" data-bs-target="#kt_modal_create_kontes"
                        class="btn btn-sm btn-secondary">Kembali</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="kt_modal_edit_panduan_kontes" tabindex="-1" aria-labelledby="kt_modal_panduan_kontes"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="kt_modal_create_kontes">Panduan Untuk Penyelenggara Kontes Bonsai
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th>Tingkat Kontes</th>
                                    <th>Persyaratan Bonsai</th>
                                    <th>Jumlah Peserta/Bonsai</th>
                                    <th>Hasil Kontes</th>
                                    <th>Juri</th>
                                    <th>Anggota Dewan Juri</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Madya</td>
                                    <td>Semua Bonsai</td>
                                    <td>Minimal 30 (tiga puluh) Bonsai</td>
                                    <td>
                                        <ol>
                                            <li>Buku Penilaian Bonsain (BPB)</li>
                                            <li>Sertifikat Tingkat Madya untuk predikat Baik dan Baik Sekali</li>
                                        </ol>
                                    </td>
                                    <td>
                                        Jumlah juri 5 (lima) orang terdiri dari beberapa Juri Madya dan minimal 1 (satu)
                                        orang
                                        Juri Utama
                                    </td>
                                    <td>
                                        Minimal 1 (satu) orang Anggota Dewan Juri
                                    </td>
                                </tr>
                                <tr>
                                    <td>Utama</td>
                                    <td>
                                        Bonsai yang telah mempunyai sertifikat Utama atau Madya dengan predikat Baik Sekali
                                    </td>
                                    <td>Minimal 20 (dua puluh) Bonsai</td>
                                    <td>
                                        Sertifikat Tingkat Utama untuk predikat Baik dan Baik Sekali
                                    </td>
                                    <td>
                                        Jumlah juri 5 (lima) orang Juri Utama
                                    </td>
                                    <td>
                                        Minimal 1 (satu) orang Anggota Dewan Juri
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button data-bs-toggle="modal" data-bs-target="#kt_modal_edit_kontes"
                        class="btn btn-sm btn-secondary">Kembali</button>
                </div>
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
                if (tingkat === 'madya') {
                    $(inputSelector).prop({
                        min: 30,
                        placeholder: 30
                    });
                    $(textSelector).text('Minimal 30 (tiga puluh) Bonsai/Peserta');
                } else if (tingkat === 'utama') {
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

                $('.list-slug').each(function(e) {
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
                const tingkat = btn.data('tingkat')?.toString().trim().toLowerCase();
                const link = btn.data('link') || '';
                const harga = btn.data('harga') || 0;

                $('#form_edit_kontes').attr('action', '/master/kontes/' + slug);
                $('#edit_kontes_slug').val(slug);
                $('#edit_nama_kontes').val(nama);
                $('#edit_tempat_kontes').val(tempat);
                $('#edit_link_gmaps').val(link);
                $('#edit_tanggal_mulai_kontes').val(tanggalMulai);
                $('#edit_tanggal_selesai_kontes').val(tanggalSelesai);
                $('#edit_jumlah_peserta').val(peserta);
                $('#edit_tingkat_kontes').val(tingkat).trigger('change');

                $('#edit_tingkat_kontes').on('change', function() {
                    const tingkat = this.value;
                    setJumlahPeserta(tingkat, '#edit_jumlah_peserta', '#edit_jumlah_peserta_text');
                });

                setJumlahPeserta(tingkat, '#edit_jumlah_peserta', '#edit_jumlah_peserta_text');


                $('#edit_harga_tiket_kontes').val(formatHarga(harga));

                const hasLink = !!link;
                $('#edit_link_gmaps_checkbox').prop('checked', hasLink);
                $('#edit_form_gmaps').toggleClass('d-none', !hasLink);
            });

            $('#edit_link_gmaps_checkbox').on('change', function() {
                $('#edit_form_gmaps').toggleClass('d-none', !this.checked);
            });

        });
    </script>
@endsection
