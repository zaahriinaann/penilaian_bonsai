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
                        <th>Tingkatan</th>
                        <th>Ukuran</th>
                        <th class="text-nowrap">Masa Pemeliharaan</th>
                        <th>Pemilik</th>
                        <th>No.Anggota</th>
                        <th>Cabang</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="align-middle">
                    @forelse ($dataRender as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <img class="rounded"
                                    src="{{ asset('images/bonsai/' . $item->foto) ?? asset('assets/media/avatars/blank.png') }}"
                                    alt="Foto Juri" style="width: 75px; height: 75px; object-fit: cover;">
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
                            <td class="text-capitalize">{{ $item->tingkatan }}</td>
                            <td class="text-nowrap">{{ $item->ukuran }}</td>
                            <td>{{ $item->masa_pemeliharaan }} {{ $item->format_masa }}</td>
                            <td>{{ $item->user->name }}</td>
                            <td>{{ $item->user->no_anggota }}</td>
                            <td class="text-nowrap">{{ $item->user->cabang }}</td>
                            <td>
                                <div class="d-flex gap-2 m-0 p-0">
                                    <button type="button" class="btn btn-sm btn-warning btn-edit" data-bs-toggle="modal"
                                        data-bs-target="#kt_modal_edit_bonsai_{{ $item->id }}" title="Edit data">
                                        <i class="bi bi-pencil-square m-0 p-0"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger btn-delete" title="Hapus data"
                                        data-id="{{ $item->id }}"
                                        data-route="{{ route('bonsai.destroy', $item->slug) }}">
                                        <i class="bi bi-trash-fill m-0 p-0"></i></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center no-data">Data tidak tersedia</td>
                        </tr>
                    @endforelse
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
                                <select id="peserta" name="peserta" class="text-capitalize peserta">
                                    <option value="">Pilih Peserta</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-3 pt-2" style="border-top: 1px dashed #ABABAB;">
                                <span class="fw-bold fs-5">Data Pohon</span>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="nama_pohon" class="form-label">Nama Pohon</label>
                                <input type="text" class="form-control" name="nama_pohon" id="nama_pohon"
                                    aria-describedby="nama_pohon" title="Nama Pohon" placeholder="Masukkan Nama Pohon">
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
                            {{-- <div class="col-md-12 mb-3">
                                    <label for="tingkatan" class="form-label">Tingkatan</label>
                                    <select name="tingkatan" id="tingkatan" class="form-select form-control">
                                        <option selected disabled>Pilih Tingkatan</option>
                                        <option value="pratama">Pratama</option>
                                        <option value="madya">Madya</option>
                                        <option value="utama">Utama</option>
                                    </select>
                                </div> --}}
                            <div class="col-md-12 mb-3">
                                <label for="ukuran" class="form-label">Ukuran Pohon</label>
                                <div class="input-group">
                                    <select name="ukuran_1" id="ukuran_1" class="form-select form-control">
                                        <option selected disabled>Pilih Ukuran</option>
                                        <option value="1">Small</option>
                                        <option value="2">Medium</option>
                                        <option value="3">Large</option>
                                    </select>
                                    <input type="number" class="form-control" name="ukuran_2" id="ukuran_2"
                                        aria-describedby="ukuran_2" title="Ukuran Pohon" placeholder="Ukuran Pohon"
                                        min="0">
                                    <select name="format_ukuran" id="format_ukuran"
                                        class="form-select form-control text-capitalize">
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
        <div class="modal fade" id="kt_modal_edit_bonsai_{{ $item->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form id="form-edit-bonsai" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" id="edit_id">
                        <input type="hidden" name="slug" id="edit_slug">

                        <div class="modal-header">
                            <h5 class="modal-title">Edit Data Bonsai {{ $item->user->name }} ({{ $item->nama_pohon }})
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Tutup"></button>
                        </div>

                        <div class="modal-body">
                            <div class="row">
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

                                {{-- <div class="col-md-12 mb-3">
                                <label for="tingkatan" class="form-label">Tingkatan</label>
                                <select name="tingkatan" id="edit_tingkatan" class="form-select form-control">
                                    <option value="pratama">Pratama</option>
                                    <option value="madya">Madya</option>
                                    <option value="utama">Utama</option>
                                </select>
                            </div> --}}

                                <!-- Ukuran -->
                                <div class="col-md-12 mb-3">
                                    <label>Ukuran</label>
                                    <div class="input-group">
                                        <select class="form-select" name="ukuran_1" id="edit_ukuran_1">
                                            <option value="1">Small</option>
                                            <option value="2">Medium</option>
                                            <option value="3">Large</option>
                                        </select>
                                        <input type="number" class="form-control" name="ukuran_2" id="edit_ukuran_2"
                                            placeholder="0">
                                        <select class="form-select" name="format_ukuran" id="edit_format_ukuran">
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
            });
        });
    </script>
@endsection
