@extends('layouts.app')

@section('title', 'Kelola Kriteria Penilaian')

@section('button-toolbar')
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_create_kriteria" type="button">
            Tambah Kriteria
        </button>
        <form action="{{ route('admin.penilaian.fuzzy-rules.auto-generate') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-sm btn-warning">
                <i class="fa fa-cogs"></i> Generate Fuzzy Rules
            </button>
        </form>
    </div>
@endsection

@section('content')
    {{-- form hidden untuk tombol "Generate Rules Sekarang" di SweetAlert --}}
    <form id="gen-rules-form" action="{{ route('admin.penilaian.fuzzy-rules.auto-generate') }}" method="POST"
        class="d-none">
        @csrf
    </form>

    <ul class="nav nav-tabs mb-3 nav-line-tabs" id="tabMenu" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="kelola-tab" data-bs-toggle="tab" data-bs-target="#kelola-tab-pane"
                type="button" role="tab">Kelola Kriteria</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="data-tab" data-bs-toggle="tab" data-bs-target="#data-tab-pane" type="button"
                role="tab">Data Kriteria</button>
        </li>
    </ul>

    <div class="tab-content" id="tabContent">
        {{-- Kelola Kriteria Penilaian --}}
        <div class="tab-pane fade show active" id="kelola-tab-pane" role="tabpanel" aria-labelledby="kelola-tab">
            <div class="card w-100 mb-5 card-kelola" style="z-index: 1">
                <div class="card-header sticky-top bg-white">
                    <h1 class="card-title">Kelola Kriteria Penilaian</h1>
                </div>
                <div class="card-body text-capitalize">
                    @if ($isEmpty)
                        <div class="alert alert-danger text-center">
                            <span>Belum ada kriteria penilaian</span>
                        </div>
                    @endif

                    <div class="accordion text-capitalize" id="accordionExample">
                        @php $index = 0; @endphp

                        @foreach ($kategori as $namaKategori => $subkriteria)
                            <div class="d-flex justify-content-between align-items-center">
                                <h4>{{ $namaKategori }}</h4>
                                <button class="btn btn-sm btn-primary d-none" data-bs-toggle="modal"
                                    data-bs-target="#modal_sub_{{ $namaKategori }}" type="button">
                                    Tambah Data {{ $namaKategori }}
                                </button>
                            </div>
                            <input type="text" hidden name="kriteria" value="{{ $namaKategori }}" class="form-control">

                            @foreach ($subkriteria as $item)
                                @php
                                    $collapseId = 'collapse' . $index++;
                                    $slug = Str::slug($item, '_');
                                    $penilaiansItem = $penilaians[$slug] ?? [];
                                @endphp

                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed text-capitalize" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}"
                                            aria-expanded="false" aria-controls="{{ $collapseId }}">
                                            {{ $item }}
                                        </button>
                                    </h2>

                                    <div id="{{ $collapseId }}" class="accordion-collapse collapse text-capitalize"
                                        data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            <form method="POST" action="{{ route('master.penilaian.update', $slug) }}"
                                                id="form-{{ $collapseId }}">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="sub_kriteria" value="{{ $item }}">
                                                <input type="hidden" name="kategori" value="{{ $namaKategori }}">
                                                <input type="hidden" name="slug" value="{{ $slug }}">
                                                <input type="hidden" name="himpunan"
                                                    value="{{ json_encode($penilaiansItem) }}">
                                                <div class="table-responsive">
                                                    <table class="table table-borderless table-hover">
                                                        <tr>
                                                            <th class="align-middle">Himpunan</th>
                                                            <th class="align-middle text-center">Min</th>
                                                            <th class="align-middle text-center">Max</th>
                                                            <th class="align-middle">
                                                                <button type="button"
                                                                    onclick="deleteAll('{{ $item }}')"
                                                                    class="btn btn-sm btn-danger w-100 ">
                                                                    <i class="fa fa-trash mx-0 px-0"></i>
                                                                    Hapus Data
                                                                </button>
                                                            </th>
                                                        </tr>
                                                        @foreach ($penilaiansItem as $huruf => $range)
                                                            <tr>
                                                                <td class="align-middle">
                                                                    {{ strtoupper($huruf) }}</td>

                                                                <td class="align-middle">
                                                                    <input type="number"
                                                                        class="form-control form-control-sm text-center min-input"
                                                                        style="min-width: 70px;"
                                                                        name="{{ $slug }}[{{ $huruf }}][min]"
                                                                        value="{{ $range['min'] }}" placeholder="Min">
                                                                </td>
                                                                <td class="align-middle">
                                                                    <input type="number"
                                                                        class="form-control form-control-sm text-center min-input"
                                                                        style="min-width: 70px;"
                                                                        name="{{ $slug }}[{{ $huruf }}][max]"
                                                                        value="{{ $range['max'] }}" placeholder="Max">
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </table>
                                                </div>
                                                <div
                                                    class="mt-4 btn-simpan-nilai {{ count($penilaians) < 1 ? 'd-none' : 'd-none' }}">
                                                    <button type="submit" class="btn btn-primary">Simpan
                                                        Perubahan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        {{-- Data Kriteria Penilaian --}}
        <div class="tab-pane fade" id="data-tab-pane" role="tabpanel" aria-labelledby="data-tab">
            <div class="card w-100 mb-5 card-kriteria">
                <div class="card-header sticky-top bg-white">
                    <h1 class="card-title">Data Kriteria Penilaian</h1>
                </div>
                <div class="card-body">
                    <div class="accordion" id="accordionExample">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle">

                                <thead class="table-light">
                                    <tr class="fw-bold">
                                        <th class="text-center" style="width: 5%;">#</th>
                                        <th style="width: 20%;">Kriteria</th>
                                        <th>Sub Kriteria</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($kategori as $namaKategori => $subkriteria)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td><strong>{{ $namaKategori }}</strong></td>
                                            <td>
                                                <ul class="list-group list-group-flush">
                                                    @foreach ($subkriteria as $item)
                                                        <li class="list-group-item bg-transparent p-2">
                                                            <div>
                                                                <strong>{{ $item }}</strong>
                                                                <ul class="mt-1 ps-3 list-unstyled">
                                                                    @foreach ($himpunan as $huruf => $range)
                                                                        <li>
                                                                            <span class="text-muted">
                                                                                {{ $huruf }} : [{{ $range[0] }}
                                                                                -
                                                                                {{ $range[1] }}]
                                                                            </span>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL CREATE KRITERIA --}}
    <div class="modal fade" id="kt_modal_create_kriteria" tabindex="-1" aria-labelledby="kt_modal_create_kriteria"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="kt_modal_create_kriteria">Data Kriteria</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('master.penilaian.store') }}" enctype="multipart/form-data" method="POST">
                    @csrf
                    @method('POST')
                    <input type="hidden" name="add_kriteria" value="1">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="kriteria" class="form-label">Kriteria</label>
                                <select class="form-select" name="kriteria" id="kriteria" aria-describedby="kriteria"
                                    title="Kriteria" onchange="changeKriteria()" required>
                                    <option value="" disabled selected>Pilih Kriteria</option>
                                    @foreach ($kriteria as $itemKriteria)
                                        <option value="{{ $itemKriteria['id'] }}">{{ $itemKriteria['kriteria'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="sub_kriteria" class="form-label">Sub Kriteria</label>
                                <input type="text" class="form-control" name="sub_kriteria" id="sub_kriteria"
                                    aria-describedby="sub_kriteria" title="Kriteria" placeholder="Masukkan Kriteria"
                                    required>
                            </div>
                            <?php
                            $himpunan = ['Baik Sekali', 'Baik', 'Cukup', 'Kurang'];
                            ?>
                            @foreach ($himpunan as $index => $huruf)
                                <input type="hidden" name="himpunan[]" value="{{ $huruf }}" id="">
                                <div class="col-12 row align-items-center himpunan-set" style="display: none"
                                    id="himpunan-{{ $huruf }}">
                                    <div class="col-12 col-md-4 mb-3">
                                        <span>Himpunan
                                            <span class="fw-bold">
                                                {{ $huruf }}
                                            </span>
                                        </span>
                                    </div>
                                    <div class="col-12 col-md-4 mb-3">
                                        <label for="min_{{ $huruf }}" class="form-label">Min</label>
                                        <input type="number"
                                            class="form-control {{ $huruf == 'Baik Sekali' ? 'min_baik_sekali' : '' }}"
                                            name="min[]" id="min_{{ $huruf }}"
                                            aria-describedby="min_{{ $huruf }}" min="0" required>
                                    </div>
                                    <div class="col-12 col-md-4 mb-3">
                                        <label for="max_{{ $huruf }}" class="form-label">Max</label>
                                        <input type="number"
                                            class="form-control {{ $huruf == 'Baik Sekali' ? 'max_baik_sekali' : '' }}"
                                            name="max[]" id="max_{{ $huruf }}"
                                            aria-describedby="max_{{ $huruf }}" min="0" required>
                                    </div>
                                </div>
                            @endforeach
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

    {{-- MODAL CREATE SUB KRITERIA --}}
    @foreach ($kategori as $namaKategori => $subkriteria)
        <div class="modal fade" id="modal_sub_{{ $namaKategori }}" tabindex="-1"
            aria-labelledby="modal_sub_{{ $namaKategori }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="modal_sub_{{ $namaKategori }}">Data Kriteria
                            {{ $namaKategori }}</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('master.penilaian.store') }}" enctype="multipart/form-data" method="POST"
                        id="form-create-{{ $namaKategori }}">
                        @csrf
                        @method('POST')
                        <input type="hidden" name="add_sub_kriteria" value="1">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label for="kriteria" class="form-label">Kriteria</label>
                                    <input type="text" class="form-control" name="kriteria" id="kriteria"
                                        aria-describedby="kriteria" title="Kriteria" placeholder="Masukkan Kriteria"
                                        value="{{ $namaKategori }}">
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="sub_kriteria" class="form-label">Sub Kriteria</label>
                                    <input type="text" class="form-control" name="sub_kriteria" id="sub_kriteria"
                                        aria-describedby="sub_kriteria" title="Kriteria"
                                        placeholder="Masukkan Sub Kriteria">
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

    {{-- HAPUS SUB KRITERIA --}}
    <form action="{{ route('master.penilaian.destroy', true) }}" method="POST" id="form-delete-all">
        @csrf
        @method('DELETE')
        <input type="hidden" name="sub_kriteria">
        <input type="hidden" name="himpunan">
    </form>
@endsection

@section('script')
    <script>
        function deleteAll(e) {
            $('#form-delete-all').find('input[name="sub_kriteria"]').val(e);
            $('#form-delete-all').submit();
        }

        function deleteNilai(himpunan, sub_kriteria) {
            $('#form-delete-all').find('input[name="sub_kriteria"]').val(sub_kriteria);
            $('#form-delete-all').find('input[name="himpunan"]').val(himpunan);
            $('#form-delete-all').submit();
        }

        function changeKriteria() {
            $('.himpunan-set').show();
            let kriteria = $('#kriteria').val();
            const data = @json($kriteria);

            let detailKriteria = data.filter(item => item.kriteria == kriteria).map(item => item.himpunan).join(',');
            let detailMinKriteria = data.filter(item => item.kriteria == kriteria).map(item => item.min).join(',');
            let detailMaxKriteria = data.filter(item => item.kriteria == kriteria).map(item => item.max).join(',');

            detailKriteria = detailKriteria.split(',');
            detailMinKriteria = detailMinKriteria.split(',');
            detailMaxKriteria = detailMaxKriteria.split(',');

            detailKriteria.forEach((item, index) => {
                if (item === 'Baik Sekali') {
                    $('.min_baik_sekali').val(detailMinKriteria[3]).attr('readonly', false);
                    $('.max_baik_sekali').val(detailMaxKriteria[3]).attr('readonly', false);
                }

                $('#min_' + item).val(detailMinKriteria[index]).attr('readonly', false);
                $('#max_' + item).val(detailMaxKriteria[index]).attr('readonly', false);
            })
        }

        $(document).ready(function() {
            $('.min-input, .max-input').on('change', function() {
                $('.btn-simpan-nilai').removeClass('d-none');
            });

            $('.min-input, .max-input').on('input', function() {
                $(this).closest('.card-body').find('.btn-simpan-nilai').removeClass('d-none');
            });

            // SweetAlert notifikasi tengah
            // SweetAlert notifikasi tengah (pakai localStorage biar tombol generate hilang setelah sukses)
            @if (session('success'))
                if (window.Swal) {
                    const pesan = `{{ addslashes(session('success')) }}`;
                    // deteksi variasi "fuzzy rules berhasil di/di-generate/digenerate/generated"
                    let sudahGenerate = /(fuzzy\s*rules?).*(di\s*-?\s*generate|digenerate|generated)/i.test(pesan);

                    // kalau sebelumnya kita baru klik generate, paksa treat sebagai "sudah generate"
                    if (localStorage.getItem('rulesJustGenerated') === '1') {
                        sudahGenerate = true;
                    }

                    const opts = {
                        title: 'Berhasil!',
                        html: `
      <div class="mb-2"><strong>${pesan}</strong></div>
      ${!sudahGenerate ? '<div class="text-muted">Untuk menjaga konsistensi FIS, segera generate ulang fuzzy rules.</div>' : ''}
    `,
                        icon: 'success',
                        reverseButtons: true,
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: '',
                            cancelButton: 'btn btn-light'
                        }
                    };

                    if (sudahGenerate) {
                        // Setelah generate: hanya tombol Tutup
                        opts.showCancelButton = false;
                        opts.showConfirmButton = true;
                        opts.confirmButtonText = 'Tutup';
                        opts.customClass.confirmButton = 'btn btn-light';
                    } else {
                        // Ada perubahan: tampilkan tombol Generate + Tutup
                        opts.showCancelButton = true;
                        opts.showConfirmButton = true;
                        opts.confirmButtonText = '⚙️  Generate Rules Sekarang';
                        opts.cancelButtonText = 'Tutup';
                        opts.customClass.confirmButton = 'btn btn-warning';
                    }

                    Swal.fire(opts).then((res) => {
                        if (res.isConfirmed && !sudahGenerate) {
                            // set flag -> begitu reload, tombol generate tidak muncul lagi
                            localStorage.setItem('rulesJustGenerated', '1');
                            document.getElementById('gen-rules-form').submit();
                        } else {
                            // bersihin flag setelah pesan sukses "sudah generate" sudah ditampilkan sekali
                            if (sudahGenerate) localStorage.removeItem('rulesJustGenerated');
                        }
                    });
                }
            @endif

        });
    </script>
@endsection

@section('style')
    <style>
        .card-kriteria,
        .card-kelola {
            overflow-x: auto;
            overflow-y: visible;
        }

        @media (max-width: 768px) {
            .card-body {
                padding: 1rem;
            }

            .accordion-button {
                font-size: 14px;
                padding: 0.5rem 1rem;
            }

            .table th,
            .table td {
                font-size: 13px;
                padding: 0.5rem;
            }

            .nav-tabs .nav-link {
                font-size: 14px;
                padding: 6px 10px;
            }

            .modal-dialog {
                width: 95% !important;
                margin: auto;
            }

            .btn-sm {
                font-size: 12px;
                padding: 0.4rem 0.6rem;
            }

            .card-title {
                font-size: 1.2rem;
            }

            .himpunan-set .col {
                flex: 1 0 100%;
            }
        }
    </style>
@endsection
