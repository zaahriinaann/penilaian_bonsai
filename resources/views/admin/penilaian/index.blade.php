@extends('layouts.app')

@section('title', 'Kelola Kriteria Penilaian')

@section('button-toolbar')
    <div class="d-flex flex-wrap gap-2">
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_create_kriteria" type="button">
            Tambah Kriteria
        </button>
        <form action="{{ route('fuzzy-rules.auto-generate') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-sm btn-warning">
                <i class="fa fa-cogs"></i> Generate Fuzzy Rules
            </button>
        </form>
    </div>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success d-flex align-items-center justify-content-between d-none">
            <span>
                <i class="bi bi-check-square-fill text-success"></i>
                {{ session('success') }}
            </span>
            <i class="bi bi-x-square-fill cursor-pointer text-danger" id="close-alert"></i>
        </div>
    @endif

    {{-- Tabs Navigation --}}
    <ul class="nav nav-tabs mb-3" id="kriteriaTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="kelola-tab" data-bs-toggle="tab" data-bs-target="#kelola" type="button"
                role="tab">
                Kelola Kriteria
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="data-tab" data-bs-toggle="tab" data-bs-target="#data" type="button"
                role="tab">
                Data Kriteria
            </button>
        </li>
    </ul>

    <div class="tab-content" id="kriteriaTabContent">
        <div class="tab-pane fade show active" id="kelola" role="tabpanel">
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
                            <div class="d-flex justify-content-between align-items-center flex-wrap mb-2">
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

                                <div class="accordion-item mb-2">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed text-capitalize" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}"
                                            aria-expanded="false" aria-controls="{{ $collapseId }}">
                                            {{ $item }}
                                        </button>
                                    </h2>

                                    <div id="{{ $collapseId }}" class="accordion-collapse collapse text-capitalize"
                                        data-bs-parent="#accordionExample">
                                        <div class="accordion-body" style="overflow-x:auto;">
                                            <form method="POST" action="{{ route('penilaian.update', $slug) }}"
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
                                                                    class="btn btn-sm btn-danger w-100">
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
                                                                        name="{{ $slug }}[{{ $huruf }}][min]"
                                                                        value="{{ $range['min'] }}" placeholder="Min">
                                                                </td>
                                                                <td class="align-middle">
                                                                    <input type="number"
                                                                        class="form-control form-control-sm text-center max-input"
                                                                        name="{{ $slug }}[{{ $huruf }}][max]"
                                                                        value="{{ $range['max'] }}" placeholder="Max">
                                                                </td>
                                                                <td></td>
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
        <div class="tab-pane fade" id="data" role="tabpanel">
            <div class="card w-100 mb-5 card-kriteria">
                <div class="card-header sticky-top bg-white">
                    <h1 class="card-title">Data Kriteria Penilaian</h1>
                </div>

                <div class="card-body">
                    {{-- Navigasi Daftar Isi --}}
                    <div class="mb-4 d-flex flex-wrap gap-2">
                        @foreach ($kategori as $namaKategori => $subkriteria)
                            <a href="#kriteria-{{ Str::slug($namaKategori, '-') }}"
                                class="btn btn-sm btn-outline-primary">
                                {{ $namaKategori }}
                            </a>
                        @endforeach
                    </div>

                    {{-- Isi Data Kriteria --}}
                    @foreach ($kategori as $namaKategori => $subkriteria)
                        <div class="card mb-4 shadow-sm border" id="kriteria-{{ Str::slug($namaKategori, '-') }}">
                            <div class="card-header bg-light">
                                <strong class="text-uppercase">{{ $namaKategori }}</strong>
                            </div>
                            <div class="card-body">
                                @if (count($subkriteria) > 0)
                                    <ul class="list-group list-group-flush">
                                        @foreach ($subkriteria as $item)
                                            <li class="list-group-item bg-transparent">
                                                <div>
                                                    <strong>{{ $item }}</strong>
                                                    <ul class="mt-1 ps-3 list-unstyled small">
                                                        @foreach ($himpunan as $huruf => $range)
                                                            <li>
                                                                <span class="text-muted">
                                                                    {{ $huruf }} : [{{ $range[0] }} -
                                                                    {{ $range[1] }}]
                                                                </span>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-muted">Belum ada sub-kriteria</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Tombol kembali ke atas --}}
    <a href="#" class="btn btn-secondary btn-sm position-fixed bottom-0 end-0 m-4 d-inline-block d-md-inline-block"
        style="z-index:999;">
        ↑ Kembali ke atas
    </a>

    {{-- Modal Tambah Kriteria --}}
    <div class="modal fade" id="kt_modal_create_kriteria" tabindex="-1" aria-labelledby="kt_modal_create_kriteria"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="kt_modal_create_kriteria">Data Kriteria</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('penilaian.store') }}" enctype="multipart/form-data" method="POST">
                    @csrf
                    @method('POST')
                    <input type="hidden" name="add_kriteria" value="1">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="kriteria" class="form-label">Kriteria</label>
                                <select class="form-select" name="kriteria" id="kriteria" onchange="changeKriteria()"
                                    required>
                                    <option value="" disabled selected>Pilih Kriteria</option>
                                    @foreach ($kriteria as $itemKriteria)
                                        <option value="{{ $itemKriteria['id'] }}">{{ $itemKriteria['kriteria'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="sub_kriteria" class="form-label">Sub Kriteria</label>
                                <input type="text" class="form-control" name="sub_kriteria" id="sub_kriteria"
                                    placeholder="Masukkan Kriteria" required>
                            </div>
                            @php $himpunan = ['Baik Sekali', 'Baik', 'Cukup', 'Kurang']; @endphp
                            @foreach ($himpunan as $huruf)
                                <input type="hidden" name="himpunan[]" value="{{ $huruf }}">
                                <div class="col-12 row align-items-center himpunan-set" style="display: none"
                                    id="himpunan-{{ $huruf }}">
                                    <div class="col-md-4 col-12 mb-2">
                                        <span>Himpunan <b>{{ $huruf }}</b></span>
                                    </div>
                                    <div class="col-md-4 col-6 mb-2">
                                        <label for="min_{{ $huruf }}" class="form-label">Min</label>
                                        <input type="number" class="form-control" name="min[]"
                                            id="min_{{ $huruf }}" min="0" required>
                                    </div>
                                    <div class="col-md-4 col-6 mb-2">
                                        <label for="max_{{ $huruf }}" class="form-label">Max</label>
                                        <input type="number" class="form-control" name="max[]"
                                            id="max_{{ $huruf }}" min="0" required>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="reset" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
                $('#min_' + item).val(detailMinKriteria[index]).attr('readonly', false);
                $('#max_' + item).val(detailMaxKriteria[index]).attr('readonly', false);
            });
        }

        $(document).ready(function() {
            document.querySelectorAll('a[href="#"]').forEach(function(anchor) {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
            });

            const alertBox = $('.alert-success');
            if (alertBox.length) {
                alertBox.removeClass('d-none').hide().fadeIn('slow').delay(5000).fadeOut('slow', function() {
                    $(this).addClass('d-none');
                });
            }

            $('#close-alert').on('click', function() {
                $(this).parent().fadeOut('slow', function() {
                    $(this).addClass('d-none');
                });
            });

            $('.min-input, .max-input').on('change', function() {
                $('.btn-simpan-nilai').removeClass('d-none');
            });
        });
    </script>
@endsection

@section('style')
    <style>
        .table-responsive {
            overflow-x: auto;
        }

        .accordion-button {
            word-break: break-word;
        }

        .min-input,
        .max-input {
            min-width: 60px;
        }
    </style>
@endsection
