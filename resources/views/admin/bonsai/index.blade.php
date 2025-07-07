@extends('layouts.app')

@section('title', 'Kelola Bonsai Peserta')

@section('button-toolbar')
    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_create_bonsai">
        Tambah Bonsai Peserta
    </button>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5>Data Bonsai Peserta</h5>
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
                        <th class="text-nowrap">No.Induk Pohon</th>
                        <th>
                            Nama Pohon
                            <br>
                            <b>(Nama Lokal/Nama Latin)</b>
                        </th>
                        <th>Kelas</th>
                        <th>Ukuran</th>
                        <th class="text-nowrap">Masa Pemeliharaan</th>
                        <th>Pemilik</th>
                        <th>No Hp</th>
                        <th>Cabang</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="align-middle">
                    @foreach ($dataRender as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <img class="rounded"
                                    src="{{ $item->foto ? asset('images/bonsai/' . $item->foto) : asset('assets/media/avatars/blank.png') }}"
                                    alt="Foto Bonsai" style="width: 75px; height: 75px; object-fit: cover;">
                            </td>
                            <td>{{ $item->no_induk_pohon }}</td>
                            <td>
                                <div class="d-grid" style="width: 250px">
                                    <span>
                                        {{ $item->nama_pohon }}
                                    </span>
                                    <small>
                                        <b>
                                            ({{ $item->nama_lokal . '/' . $item->nama_latin }})
                                        </b>
                                    </small>
                                </div>
                            </td>
                            <td class="text-capitalize">{{ $item->kelas }}</td>
                            <td class="text-nowrap">{{ $item->ukuran }}</td>
                            <td>{{ $item->masa_pemeliharaan }} {{ $item->format_masa }}</td>
                            <td>{{ $item->user?->name }}</td>
                            <td>{{ $item->user?->no_hp }}</td>
                            <td class="text-nowrap">{{ $item->user?->cabang }}</td>
                            <td>
                                <div class="d-flex gap-2 m-0 p-0">

                                    <button type="button" class="btn btn-sm btn-warning btn-edit"
                                        data-id="{{ $item->id }}" data-slug="{{ $item->slug }}"
                                        data-peserta="{{ $item->user_id }}" data-nama_pohon="{{ $item->nama_pohon }}"
                                        data-nama_lokal="{{ $item->nama_lokal }}"
                                        data-nama_latin="{{ $item->nama_latin }}" data-ukuran="{{ $item->ukuran }}"
                                        data-ukuran_1="{{ $item->ukuran_1 }}" data-ukuran_2="{{ $item->ukuran_2 }}"
                                        data-format_ukuran="{{ $item->format_ukuran }}"
                                        data-nomor_induk_pohon="{{ $item->nomor_induk_pohon }}"
                                        data-masa_pemeliharaan="{{ $item->masa_pemeliharaan }}"
                                        data-format_masa="{{ $item->format_masa }}" data-kelas="{{ $item->kelas }}"
                                        data-foto="{{ $item->foto }}" data-bs-toggle="modal"
                                        data-bs-target="#kt_modal_edit_bonsai" title="Edit data">
                                        <i class="bi bi-pencil-square m-0 p-0"></i>
                                    </button>

                                    <button class="btn btn-sm btn-danger btn-delete" title="Hapus data"
                                        data-id="{{ $item->id }}"
                                        data-route="{{ route('bonsai.destroy', $item->slug) }}">
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
    <div class="modal fade" id="kt_modal_create_bonsai" tabindex="-1" aria-labelledby="kt_modal_create_bonsai"
        aria-hidden="true">
        <div class="modal-dialog change-modal modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="kt_modal_create_bonsai">Data Bonsai Peserta</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form-create-bonsai" action="{{ route('bonsai.store') }}" enctype="multipart/form-data"
                    method="POST">
                    @csrf
                    @method('POST')
                    <div class="modal-body">
                        <div id="form-sudah-daftar" class="row">
                            <div class="col-12 mb-3">
                                <label for="peserta" class="form-label">Nama Peserta</label>
                                <select id="peserta" name="peserta" class="text-capitalize peserta" required>
                                    <option value="">Pilih Peserta</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-3 pt-2" style="border-top: 1px dashed #ABABAB;">
                                <span class="fw-bold fs-5">Data Pohon</span>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="nama_pohon" class="form-label">Nama Pohon</label>
                                <input type="text" class="form-control" name="nama_pohon" id="nama_pohon"
                                    aria-describedby="nama_pohon" title="Nama Pohon" placeholder="Masukkan Nama Pohon"
                                    required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="d-flex gap-2">
                                    <div class="w-100">
                                        <label for="nama_lokal" class="form-label">Nama Lokal Pohon</label>
                                        <input type="text" class="form-control" name="nama_lokal" id="nama_lokal"
                                            aria-describedby="nama_lokal" title="Nama Lokal"
                                            placeholder="Masukkan Nama Lokal">
                                    </div>
                                    <div class="w-100">
                                        <label for="nama_latin" class="form-label">Nama Latin Pohon</label>
                                        <input type="text" class="form-control" name="nama_latin" id="nama_latin"
                                            aria-describedby="nama_latin" title="Nama Latin"
                                            placeholder="Masukkan Nama Latin">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="kelas" class="form-label">Kelas</label>
                                <select name="kelas" id="kelas" class="form-select form-control" required>
                                    <option selected disabled>Pilih kelas</option>
                                    <option value="Bahan">Bahan</option>
                                    <option value="Pratama">Pratama</option>
                                    <option value="Madya">Madya</option>
                                    <option value="Utama">Utama</option>
                                    <option value="Bintang">Bintang</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="ukuran" class="form-label">Ukuran Pohon</label>
                                <div class="input-group">
                                    <select name="ukuran_1" id="ukuran_1" class="form-select form-control" required>
                                        <option selected disabled>Pilih Ukuran</option>
                                        <option value="1">Small</option>
                                        <option value="2">Medium</option>
                                        <option value="3">Large</option>
                                    </select>
                                    <input type="number" class="form-control" name="ukuran_2" id="ukuran_2"
                                        aria-describedby="ukuran_2" title="Ukuran Pohon" placeholder="Ukuran Pohon"
                                        min="0" required>
                                    <select name="format_ukuran" id="format_ukuran"
                                        class="form-select form-control text-capitalize" required>
                                        <option selected value="cm">cm</option>
                                        <option value="m">m</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="masa_pemeliharaan" class="form-label">Masa Pemeliharaan</label>
                                <div class="d-flex gap-2">
                                    <input type="number" min="0" class="form-control" name="masa_pemeliharaan"
                                        id="masa_pemeliharaan" aria-describedby="masa_pemeliharaan"
                                        title="Masa Pemeliharaan" placeholder="Masukkan Masa Pemeliharaan">
                                    <select name="format_masa" id="format_masa"
                                        class="form-select form-control text-capitalize">
                                        <option selected value="bulan">bulan</option>
                                        <option value="tahun">tahun</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="foto" class="form-label">Foto Bonsai</label>
                                <input type="file" class="form-control" name="foto" id="foto">
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

    @foreach ($dataRender as $item)
        <div class="modal fade" id="kt_modal_edit_bonsai" tabindex="-1" aria-labelledby="kt_modal_edit_bonsai"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form id="form_edit_bonsai" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <input type="hidden" name="slug" id="edit_bonsai_slug">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label for="peserta" class="form-label">Nama Peserta</label>
                                    <select id="peserta" class="text-capitalize peserta" name="peserta">
                                    </select>
                                </div>

                                <div class="col-md-12 mb-3 pt-2" style="border-top: 1px dashed #ABABAB;">
                                    <span class="fw-bold fs-5">Data Pohon</span>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label>Nama Pohon</label>
                                    <input type="text" class="form-control" name="nama_pohon" id="edit_nama_pohon"
                                        required>
                                </div>
                                <div class="col-12 d-flex gap-2 mb-3">
                                    <div class="w-100">
                                        <label>Nama Lokal</label>
                                        <input type="text" class="form-control" name="nama_lokal"
                                            id="edit_nama_lokal">
                                    </div>
                                    <div class="w-100">
                                        <label>Nama Latin</label>
                                        <input type="text" class="form-control" name="nama_latin"
                                            id="edit_nama_latin">
                                    </div>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="kelas" class="form-label">kelas</label>
                                    <select name="kelas" id="edit_kelas" class="form-select form-control" required>
                                        <option selected disabled>Pilih kelas</option>
                                        <option value="Bahan">Bahan</option>
                                        <option value="Pratama">Pratama</option>
                                        <option value="Madya">Madya</option>
                                        <option value="Utama">Utama</option>
                                        <option value="Bintang">Bintang</option>
                                    </select>
                                </div>

                                <!-- Ukuran -->
                                <div class="col-md-12 mb-3">
                                    <label>Ukuran</label>
                                    <div class="input-group">
                                        <select class="form-select" name="ukuran_1" id="edit_ukuran_1" required>
                                            <option value="1">Small</option>
                                            <option value="2">Medium</option>
                                            <option value="3">Large</option>
                                        </select>
                                        <input type="number" class="form-control" name="ukuran_2" id="edit_ukuran_2"
                                            placeholder="0" min="0" required>
                                        <select class="form-select" name="format_ukuran" id="edit_format_ukuran"
                                            required>
                                            <option value="cm">cm</option>
                                            <option value="m">m</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Masa Pemeliharaan -->
                                <div class="col-md-12 mb-3">
                                    <label>Masa Pemeliharaan</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="masa_pemeliharaan"
                                            id="edit_masa_pemeliharaan">
                                        <select class="form-select" name="format_masa" id="edit_format_masa">
                                            <option value="hari">hari</option>
                                            <option value="bulan">bulan</option>
                                            <option value="tahun">tahun</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="foto" class="form-label">Foto Bonsai</label>
                                    <input type="file" class="form-control" name="foto" id="foto">
                                    <input type="hidden" name="foto_lama" id="foto_lama">
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
    @endforeach

@endsection


@section('script')
    <script>
        $(document).ready(() => {

            const peserta = $('.peserta').selectize({
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
                    @foreach ($user as $userItem)
                        selectize.addOption({
                            id: '{{ $userItem->id }}',
                            name: '{{ $userItem->name }} / {{ $userItem->no_hp }} / {{ $userItem->no_anggota }}'
                        });
                    @endforeach
                    selectize.refreshOptions(false);
                }
            });

            // //buat selectize edit peserta bisa search
            // $('.edit_peserta').selectize({
            //     allowEmptyOption: true,
            //     placeholder: $('.edit_peserta').data('placeholder'), // tampilkan di awal
            //     theme: 'bootstrap-5',
            //     valueField: 'id',
            //     labelField: 'name',
            //     searchField: 'name',
            //     maxItems: 1,
            //     options: @json($user),
            //     onInitialize: function() {
            //         const selectize = this;
            //         selectize.clearOptions();
            //         @foreach ($user as $userItem)
            //             selectize.addOption({
            //                 id: '{{ $userItem->id }}',
            //                 name: '{{ $userItem->name }} / {{ $userItem->no_hp }} / {{ $userItem->no_anggota }}'
            //             });
            //         @endforeach
            //         selectize.refreshOptions(false);

            //         // Set value sebelumnya (jika ada)
            //         if (selectedId) {
            //             selectize.setValue(selectedId);
            //         }
            //     }
            // });



            // ubah edit data dibawah dengan bonsai
            $('.btn-edit').on('click', function() {
                const btn = $(this);

                const slug = btn.data('slug');
                const peserta = btn.data('peserta');
                const namaPohon = btn.data('nama_pohon');
                const namaLokal = btn.data('nama_lokal');
                const namaLatin = btn.data('nama_latin');
                const ukuran = btn.data('ukuran');
                const ukuran1 = btn.data('ukuran_1');
                const ukuran2 = btn.data('ukuran_2');
                const formatUkuran = btn.data('format_ukuran');
                const masaPemeliharaan = btn.data('masa_pemeliharaan');
                const formatMasa = btn.data('format_masa');
                const kelas = btn.data('kelas');
                const foto = btn.data('foto') || '';

                $('#form_edit_bonsai').attr('action', '/master/bonsai/' + slug);
                $('#edit_bonsai_slug').val(slug);
                $('#edit_peserta').val(peserta);
                $('#edit_nama_pohon').val(namaPohon);
                $('#edit_nama_lokal').val(namaLokal);
                $('#edit_nama_latin').val(namaLatin);
                $('#edit_ukuran').val(ukuran);
                $('#edit_ukuran_1').val(ukuran1);
                $('#edit_ukuran_2').val(ukuran2);
                $('#edit_format_ukuran').val(formatUkuran);
                $('#edit_masa_pemeliharaan').val(masaPemeliharaan);
                $('#edit_format_masa').val(formatMasa);
                $('#edit_kelas').val(kelas);
                $('#foto_lama').val(foto);
            });
        });
    </script>
@endsection
