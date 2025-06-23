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
                        <th>No.Induk Pohon</th>
                        <th>
                            Nama Pohon
                            <br>
                            <b>(Nama Lokal/Nama Latin)</b>
                        </th>
                        <th>Tingkatan</th>
                        <th>Ukuran</th>
                        <th>Masa Pemeliharaan</th>
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
                            <td class="text-capitalize">{{ $item->tingkatan }}</td>
                            <td>{{ $item->ukuran }}</td>
                            <td>{{ $item->masa_pemeliharaan }}</td>
                            <td>{{ $item->pemilik }}</td>
                            <td>{{ $item->no_anggota }}</td>
                            <td>{{ $item->cabang }}</td>
                            <td>
                                <div class="d-flex gap-2 m-0 p-0">
                                    <button type="button" class="btn btn-sm btn-warning btn-edit"
                                        data-id="{{ $item->id }}" data-slug="{{ $item->slug }}"
                                        data-nama="{{ $item->nama_pohon }}" data-nama_lokal="{{ $item->nama_lokal }}"
                                        data-nama_latin="{{ $item->nama_latin }}" data-ukuran="{{ $item->ukuran }}"
                                        data-no_induk_pohon="{{ $item->no_induk_pohon }}"
                                        data-masa_pemeliharaan="{{ $item->masa_pemeliharaan }}"
                                        data-pemilik="{{ $item->pemilik }}" data-no_anggota="{{ $item->no_anggota }}"
                                        data-cabang="{{ $item->cabang }}" data-ukuran_1="{{ $item->ukuran_1 }}"
                                        data-ukuran_2="{{ $item->ukuran_2 }}" data-tingkatan="{{ $item->tingkatan }}"
                                        data-format_ukuran="{{ $item->format_ukuran }}" data-foto="{{ $item->foto }}"
                                        data-bs-toggle="modal" data-bs-target="#kt_modal_edit_bonsai" title="Edit data">
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
                        <input type="hidden" name="cabang" id="cabang-input">
                        <div class="row">
                            <div class="col-md-12 mb-3 d-flex justify-content-between align-items-center">
                                <span class="fw-bold fs-5">Data Pemilik</span>
                                <div class="form-check form-check-reverse">
                                    <label class="form-check-label" for="pernahDaftar">
                                        Pernah Daftar
                                    </label>
                                    <input class="form-check-input" type="checkbox" value="" id="pernahDaftar"
                                        name="pernahDaftar">
                                </div>
                            </div>
                            <div id="pemilikBaruDaftar">
                                <div class="col-md-12 mb-3">
                                    <div class="d-flex gap-2">
                                        <div class="w-100">
                                            <label for="pemilik" class="form-label">Pemilik</label>
                                            <input type="hidden" name="pemilik" id="pemilik">
                                            <select id="pemilik" class="form-select form-control">
                                                <option value="">Pilih Peserta Terdaftar</option>
                                                @foreach ($pemilik as $p)
                                                    <option value="{{ $p->pemilik }}"
                                                        data-no_anggota="{{ $p->no_anggota }}"
                                                        data-cabang="{{ $p->cabang }}">
                                                        {{ $p->pemilik . ' - (' . $p->no_anggota . ') - ' . $p->cabang }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        {{--  <div class="w-100">
                                            <label for="no_anggota" class="form-label">Nomor Anggota</label>
                                            <input type="text" class="form-control" name="no_anggota" id="no_anggota"
                                                aria-describedby="no_anggota" title="Nomor Anggota"
                                                placeholder="Masukkan Nomor Anggota">
                                        </div> --}}
                                    </div>
                                </div>
                                {{--  <div class="col-md-12 mb-3">
                                    <label for="cabang" class="form-label">PPBI Cabang</label>
                                    <select type="text" id="cabang"></select>
                                </div> --}}
                                <span class="msg-daftar"></span>
                            </div>
                            <div id="pemilikPernahDaftar" style="display: none;">
                                <div class="col-md-12 mb-3">
                                    <label for="pemilik" class="form-label">Nama Pemilik</label>
                                    <select id="pemilik" class="form-select form-control">
                                        <option value="">Pilih Pemilik Pohon</option>
                                        @foreach ($pemilik as $p)
                                            <option value="{{ $p->pemilik }}" data-no_anggota="{{ $p->no_anggota }}"
                                                data-cabang="{{ $p->cabang }}">
                                                {{ $p->pemilik . ' - ' . $p->no_anggota }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div id="data-list-pohon-pemilik" style="display: none;">
                                <div class="col-md-12 mb-3" style="border-top: 1px dashed #ABABAB; padding-top: 10px;">
                                    <span class="fw-bold fs-5">Data List Pohon Pemilik</span>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover table-borderless table-striped">
                                        <tbody class="align-middle" id="table-data-list-pohon">
                                        </tbody>
                                    </table>
                                </div>
                                <div class="row">
                                    <button type="button" class="btn btn-sm btn-primary" id="tambah-pohon-baru">Tambah
                                        Pohon Baru</button>
                                </div>
                            </div>
                            <div id="data-pohon" style="display: none;">
                                <div class="col-md-12 mb-3" style="border-top: 1px dashed #ABABAB; padding-top: 10px;">
                                    <span class="fw-bold fs-5">Data Pohon</span>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="nama_pohon" class="form-label">Nama Pohon</label>
                                    <input type="text" class="form-control" name="nama_pohon" id="nama_pohon"
                                        aria-describedby="nama_pohon" title="Nama Pohon"
                                        placeholder="Masukkan Nama Pohon">
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
                                        <input type="number" min="0" class="form-control"
                                            name="masa_pemeliharaan" id="masa_pemeliharaan"
                                            aria-describedby="masa_pemeliharaan" title="Masa Pemeliharaan"
                                            placeholder="Masukkan Masa Pemeliharaan">
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
                    </div>
                    <div class="modal-footer">
                        <button type="reset" id="reset-btn" class="btn btn-sm btn-danger"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-sm btn-primary btn-proceed disabled">Lanjut</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="kt_modal_edit_bonsai" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="form-edit-bonsai" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="edit_id">
                    <input type="hidden" name="slug" id="edit_slug">

                    <div class="modal-header">
                        <h5 class="modal-title">Edit Data Bonsai</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
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
                                    <input type="text" class="form-control" name="nama_lokal" id="edit_nama_lokal">
                                </div>
                                <div class="w-100">
                                    <label>Nama Latin</label>
                                    <input type="text" class="form-control" name="nama_latin" id="edit_nama_latin">
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

@endsection

@section('script')
    <script>
        $(document).ready(() => {
            const pemilikSelector = '#pemilik';
            const noAnggotaSelector = '#no_anggota';
            const cabangSelector = '#cabang';
            const cabangInput = '#cabang-input';
            const tableDataListPohon = $('#table-data-list-pohon');
            const msgDaftar = $('.msg-daftar');
            const btnProceed = $('.btn-proceed'); // Define btnProceed properly

            const noAnggotaPemilik = @json($pemilik->pluck('no_anggota')->unique());
            const dataPemilik = Object.values(@json($pemilik));
            const dataPohon = Object.values(@json($dataRender));
            const province = @json($province);

            let tempCabang = '';
            let btnProceedClicked = false;
            msgDaftar.text('');

            function setTempCabang(cabang) {
                tempCabang = cabang;
                console.log('Set cabang:', cabang);
                return cabang;
            }

            function triggerSelect() {
                $(cabangSelector).trigger('change');
            }

            // RESET
            $('button[type="reset"]').on('click', () => {
                $('#data-list-pohon-pemilik').hide();
                $('#pemilikPernahDaftar').hide();
                $('#pemilikBaruDaftar').show();
                if (window.selectizeElement) {
                    window.selectizeElement.clear();
                }
                $('#data-pohon').hide();
                msgDaftar.text('');
                btnProceed.addClass('disabled').prop('type', 'button');
                $('.change-modal').removeClass('modal-lg');
            });

            // TOGGLE Checkbox "Pernah Daftar"
            $('#pernahDaftar').change(function() {
                $('#pemilikBaruDaftar').toggle();
                $('#pemilikPernahDaftar').toggle();

                if (window.selectizeElement) {
                    window.selectizeElement.clear();
                }

                if (!this.checked) {
                    $('.change-modal').removeClass('modal-lg');
                    msgDaftar.text('');
                    $(pemilikSelector).val('');
                    $(noAnggotaSelector).val('');
                    $(cabangSelector).val('');
                    $('#data-list-pohon-pemilik').hide();
                    $('#data-pohon').hide();
                }
            });

            // INISIALISASI SELECTIZE
            $('#pemilik_pernah_daftar').selectize({
                allowEmptyOption: true,
                placeholder: 'Pilih Pemilik Pohon',
                create: false,
                theme: 'bootstrap-5',
                onInitialize: function() {
                    window.selectizeElement = this;

                    this.on('change', function(value) {
                        const selectedOption = this.options[value];
                        $('.change-modal').addClass('modal-lg');
                        if (selectedOption) {
                            const valNoAnggota = selectedOption.no_anggota;
                            const valCabang = selectedOption.cabang;
                            $(cabangSelector).val(valCabang);
                            $(cabangInput).val(valCabang);
                            $(pemilikSelector).val(value);
                            $(noAnggotaSelector).val(valNoAnggota);

                            const pemilikTerdaftar = dataPemilik.find(item => item
                                .no_anggota === valNoAnggota);

                            if (pemilikTerdaftar) {
                                btnProceedClicked = true;
                                msgDaftar.text('Anggota ini sudah terdaftar.').css({
                                    color: 'green',
                                    fontSize: '12px'
                                });

                                // Tampilkan pohon milik anggota
                                const pohonPemilik = dataPohon.filter(item => item
                                    .no_anggota === valNoAnggota);
                                tableDataListPohon.empty();
                                pohonPemilik.forEach((item) => {
                                    const row = `
                                        <tr>
                                            <td>${item.no_induk_pohon}</td>
                                            <td>
                                                <div class="d-grid" style="width: 250px">
                                                    <span>${item.nama_pohon}</span>
                                                    <small><b>(${item.nama_lokal}/${item.nama_latin})</b></small>
                                                </div>
                                            </td>
                                            <td class="text-capitalize">${item.tingkatan}</td>
                                            <td>${item.ukuran}</td>
                                            <td>${item.masa_pemeliharaan}</td>
                                        </tr>
                                        `;
                                    tableDataListPohon.append(row);
                                });

                                $('#data-list-pohon-pemilik').show();
                                $('#data-pohon').hide();
                                btnProceedClicked ? btnProceed.removeClass('disabled').text(
                                    'Simpan').prop('type',
                                    'submit') : btnProceed.prop('type', 'button');
                            } else {
                                msgDaftar.text('Data anggota tidak ditemukan.').css({
                                    color: 'red',
                                    fontSize: '12px'
                                });
                                console.log('Pemilik belum terdaftar');


                                btnProceedClicked = true;
                                btnProceedClicked ? btnProceed.removeClass('disabled').text(
                                    'Simpan').prop('type',
                                    'submit') : btnProceed.prop('type', 'button');
                                tableDataListPohon.empty();
                                $('#data-list-pohon-pemilik').hide();
                                $('#data-pohon').hide();
                            }
                        } else {
                            // Jika tidak ada yang dipilih
                            $(pemilikSelector).val('');
                            $(noAnggotaSelector).val('');
                            $(cabangInput).val('');

                            msgDaftar.text('');
                            tableDataListPohon.empty();
                            $('#data-list-pohon-pemilik').hide();
                            $('#data-pohon').addClass('d-none').hide();
                        }
                    });
                }
            });

            // Initialize Selectize for cabang
            const cabangSelectize = $(cabangSelector).selectize({
                allowEmptyOption: true,
                placeholder: 'Pilih Cabang',
                theme: 'bootstrap-5',
                valueField: 'name',
                labelField: 'name',
                searchField: 'name',
                maxItems: 1,
                options: province,
                onInitialize: function() {
                    this.on('change', function(value) {
                        const provinceName = this.options[this.getValue()]?.name || '';
                        $(this.$control_input).val(provinceName || tempCabang || '');
                        $(cabangInput).val(provinceName || tempCabang || '');
                    });
                }
            })[0].selectize;

            // VALIDASI SAAT INPUT MANUAL
            $(`${pemilikSelector}, ${noAnggotaSelector}, ${cabangSelector}`).on('change', function() {
                const valPemilik = $(pemilikSelector).val();
                const valNoAnggota = $(noAnggotaSelector).val();
                const valCabang = $(cabangSelector).val();
                $(cabangInput).val(valCabang);

                if (valPemilik && valNoAnggota && valCabang) {
                    btnProceed.removeClass('disabled');

                    // Hanya daftarkan sekali
                    if (!btnProceed.data('initialized')) {
                        btnProceed.data('initialized', true);

                        btnProceed.on('click', function(e) {
                            e.preventDefault();
                            $('.change-modal').addClass('modal-lg');

                            if (!btnProceedClicked) {
                                btnProceedClicked = true;
                                btnProceed.text('Simpan');

                                if (noAnggotaPemilik.includes(valNoAnggota)) {
                                    const pemilikTerdaftar = dataPemilik.find(item => item
                                        .no_anggota === valNoAnggota);
                                    if (!pemilikTerdaftar) return;

                                    // Sinkronisasi input
                                    $(pemilikSelector).val(pemilikTerdaftar.pemilik);
                                    $(noAnggotaSelector).val(pemilikTerdaftar.no_anggota);
                                    cabangSelectize.setValue(pemilikTerdaftar.cabang);
                                    setTempCabang(pemilikTerdaftar.cabang);

                                    if (window.selectizeElement) {
                                        window.selectizeElement.clear();
                                        window.selectizeElement.addOption({
                                            value: pemilikTerdaftar.pemilik,
                                            text: `${pemilikTerdaftar.pemilik} - ${pemilikTerdaftar.no_anggota}`,
                                            no_anggota: pemilikTerdaftar.no_anggota,
                                            cabang: pemilikTerdaftar.cabang
                                        });
                                        window.selectizeElement.refreshOptions(false);
                                        window.selectizeElement.setValue(pemilikTerdaftar.pemilik);
                                    }

                                    msgDaftar.text('Anggota ini sudah terdaftar.').css({
                                        color: 'green',
                                        fontSize: '12px'
                                    });

                                    const pohonPemilik = dataPohon.filter(item => item
                                        .no_anggota === valNoAnggota);
                                    tableDataListPohon.empty();
                                    pohonPemilik.forEach((item) => {
                                        const row = `
                                        <tr>
                                            <td>${item.no_induk_pohon}</td>
                                            <td>
                                                <div class="d-grid" style="width: 250px">
                                                    <span>${item.nama_pohon}</span>
                                                    <small><b>(${item.nama_lokal}/${item.nama_latin})</b></small>
                                                </div>
                                            </td>
                                            <td class="text-capitalize">${item.tingkatan}</td>
                                            <td>${item.ukuran}</td>
                                            <td>${item.masa_pemeliharaan}</td>
                                        </tr>`;
                                        tableDataListPohon.append(row);
                                    });

                                    $('#data-list-pohon-pemilik').show();
                                    $('#data-pohon').hide();
                                } else {
                                    msgDaftar.text('');
                                    tableDataListPohon.empty();
                                    $('#data-list-pohon-pemilik').hide();
                                    $('#data-pohon').show();
                                }

                                // Setelah klik pertama, ubah tipe agar bisa submit di klik berikutnya
                                btnProceed.prop('type', 'submit');
                            } else {
                                $('#form-create-bonsai').submit();
                            }
                        });
                    }
                } else {
                    msgDaftar.text('');
                    tableDataListPohon.empty();
                    $('#data-list-pohon-pemilik').hide();
                    $('#data-pohon').hide();
                    btnProceed.addClass('disabled');
                    btnProceed.text('Lanjut');
                    btnProceed.prop('type', 'button');
                    btnProceedClicked = false;
                }
            });

            $('#tambah-pohon-baru').on('click', function() {
                $('#data-pohon').toggle();
            });

            $(document).on('click', '.btn-edit', function() {
                const id = $(this).data('id');
                const slug = $(this).data('slug');
                const nama = $(this).data('nama');
                const nama_lokal = $(this).data('nama_lokal');
                const nama_latin = $(this).data('nama_latin');
                const ukuran = $(this).data('ukuran');
                const no_induk_pohon = $(this).data('no_induk_pohon');
                const masa_pemeliharaan = $(this).data('masa_pemeliharaan');
                const ukuran_1 = $(this).data('ukuran_1');
                const ukuran_2 = $(this).data('ukuran_2');
                const format_ukuran = $(this).data('format_ukuran');
                const tingkatan = $(this).data('tingkatan');
                const foto_lama = $(this).data('foto');

                $('#form-edit-bonsai').attr('action', '/master/bonsai/' + slug);
                $('#edit_id').val(id);
                $('#edit_slug').val(slug);
                $('#edit_nama_pohon').val(nama);
                $('#edit_nama_lokal').val(nama_lokal);
                $('#edit_nama_latin').val(nama_latin);
                $('#edit_no_induk_pohon').val(no_induk_pohon);
                $('#edit_ukuran_1').val(ukuran_1);
                $('#edit_ukuran_2').val(ukuran_2);
                $('#edit_format_ukuran').val(format_ukuran);
                $('#edit_tingkatan').val(tingkatan);
                $('#foto_lama').val(foto_lama);

                const masaSplit = masa_pemeliharaan.split(" ");
                $('#edit_masa_pemeliharaan').val(parseInt(masaSplit[0]));
                $('#edit_format_masa').val(masaSplit[1] || 'bulan');
            });
        });
    </script>

@endsection
