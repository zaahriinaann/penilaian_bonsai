@extends('layouts.app')

@section('title', 'Kelola Kriteria Penilaian')


{{-- @section('button-toolbar')
    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_create_kriteria" type="button">
        Tambah Kriteria
    </button>
@endsection --}}

@section('content')
    <div class="card mb-5">
        <div class="card-header">
            <h1 class="card-title">Data Kriteria Penilaian</h1>
            <div class="card-toolbar">
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                    data-bs-target="#kt_modal_create_kriteria">
                    Tambah Kriteria
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="accordion" id="accordionExample">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseTable" aria-expanded="true" aria-controls="collapseTable">
                            Table Kriteria
                        </button>
                    </h2>
                    <div id="collapseTable" class="accordion-collapse collapse p-3" aria-labelledby="headingOne"
                        data-bs-parent="#accordionExample">
                        <table class="table table-hover table-bordered table-data">
                            <thead>
                                <tr class="fw-bold">
                                    <th style="width: 25%;">Kriteria</th>
                                    <th>Sub Kriteria</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($kategori as $namaKategori => $subkriteria)
                                    <tr>
                                        <td><strong>{{ $namaKategori }}</strong></td>
                                        <td>
                                            <ul class="list-group list-group-flush">
                                                @foreach ($subkriteria as $item)
                                                    <li class="list-group-item bg-transparent">
                                                        <div>
                                                            <strong>{{ $item }}</strong>
                                                            <ul class="mt-1 ps-3 list-unstyled">
                                                                @foreach ($himpunan as $huruf => $range)
                                                                    <li>
                                                                        <span class="text-muted">
                                                                            {{ $huruf }} :
                                                                            [{{ $range[0] }} -
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

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Kelola Kriteria Penilaian</h1>
        </div>
        <div class="card-body text-capitalize">
            @if ($isEmpty)
                <div class="alert alert-danger text-center">
                    <span>Belum ada kriteria penilaian</span>
                </div>
            @endif
            <form method="POST" action="{{ route('penilaian.store') }}">
                @csrf

                <div class="accordion text-capitalize" id="accordionExample">
                    @php $index = 0; @endphp

                    @foreach ($kategori as $namaKategori => $subkriteria)
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mt-4">{{ $namaKategori }}</h4>

                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                data-bs-target="#modal_sub_{{ $namaKategori }}" type="button">
                                Tambah Data {{ $namaKategori }}
                            </button>
                        </div>

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
                                        <table class="table table-borderless table-hover">
                                            <tr>
                                                <th class="align-middle">Himpunan</th>
                                                <th class="align-middle text-center">Min</th>
                                                <th class="align-middle text-center">Max</th>
                                                <th class="align-middle">
                                                    <button type="button" onclick="deleteAll('{{ $item }}')"
                                                        class="btn btn-sm btn-danger w-100">
                                                        <i class="fa fa-trash mx-0 px-0"></i> Hapus Data
                                                        {{ $item }}
                                                    </button>
                                                </th>
                                            </tr>
                                            {{-- <tr>
                                                <th>Semesta Pembicaraan</th>
                                                <th colspan="3">[50 - 90]</th>
                                            </tr> --}}

                                            @foreach ($penilaiansItem as $huruf => $range)
                                                <tr>
                                                    <td class="align-middle">{{ strtoupper($huruf) }}</td>
                                                    <td class="align-middle">
                                                        <input type="number"
                                                            class="form-control form-control-sm text-center w-25 mx-auto"
                                                            name="{{ $slug }}[{{ $huruf }}][min]"
                                                            value="{{ $range['min'] }}" readonly placeholder="Min">
                                                    </td>
                                                    <td class="align-middle">
                                                        <input type="number"
                                                            class="form-control form-control-sm text-center w-25 mx-auto"
                                                            name="{{ $slug }}[{{ $huruf }}][max]"
                                                            value="{{ $range['max'] }}" readonly placeholder="Max">
                                                    </td>
                                                    <td class="align-middle">
                                                        <button type="button" class="btn btn-sm btn-danger w-100"
                                                            id="delete-nilai-{{ $huruf }}-{{ $item }}"
                                                            onclick="deleteNilai('{{ $huruf }}', '{{ $item }}')">
                                                            <i class="fa fa-trash mx-0 px-0"></i>
                                                            Hapus
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                </div>

                <div class="mt-4 {{ count($penilaians) < 1 ? 'd-none' : '' }}">
                    <button type="submit" class="btn btn-primary">Simpan Penilaian</button>
                </div>
            </form>
        </div>
    </div>

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
                                <select class="form-select" name="kriteria" id="kriteria" aria-describedby="kriteria"
                                    title="Kriteria" onchange="changeKriteria()">
                                    <option value="" disabled selected>Pilih Kriteria</option>
                                    @foreach ($kriteria as $itemKriteria)
                                        <option value="{{ $itemKriteria }}">{{ $itemKriteria }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="sub_kriteria" class="form-label">Sub Kriteria</label>
                                <input type="text" class="form-control" name="sub_kriteria" id="sub_kriteria"
                                    aria-describedby="sub_kriteria" title="Kriteria" placeholder="Masukkan Kriteria">
                            </div>
                            <?php
                            $himpunan = ['Baik Sekali', 'Baik', 'Cukup', 'Kurang'];
                            ?>
                            @foreach ($himpunan as $huruf)
                                <input type="hidden" name="himpunan_{{ $huruf }}" value="{{ $huruf }}"
                                    id="">
                                <div class="col-12 row align-items-center himpunan-set" style="display: none"
                                    id="himpunan-{{ $huruf }}">
                                    <div class="col mb-3">
                                        <span>Himpunan
                                            <span class="fw-bold">
                                                {{ $huruf }}
                                            </span>
                                        </span>
                                    </div>
                                    <div class="col mb-3">
                                        <label for="min_{{ $huruf }}" class="form-label">Min</label>
                                        <input type="number"
                                            class="form-control {{ $huruf == 'Baik Sekali' ? 'min_baik_sekali' : '' }}"
                                            name="min_{{ $huruf }}" id="min_{{ $huruf }}" readonly
                                            aria-describedby="min_{{ $huruf }}" min="0">
                                    </div>
                                    <div class="col mb-3">
                                        <label for="max_{{ $huruf }}" class="form-label">Max</label>
                                        <input type="number"
                                            class="form-control {{ $huruf == 'Baik Sekali' ? 'max_baik_sekali' : '' }}"
                                            name="max_{{ $huruf }}" id="max_{{ $huruf }}" readonly
                                            aria-describedby="max_{{ $huruf }}" min="0">
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
                    <form action="{{ route('penilaian.store') }}" enctype="multipart/form-data" method="POST"
                        id="form-create-{{ $namaKategori }}">
                        @csrf
                        @method('POST')
                        <input type="hidden" name="add_sub_kriteria" value="1">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="kriteria" class="form-label">Kriteria</label>
                                    <input type="text" class="form-control" name="kriteria" id="kriteria"
                                        aria-describedby="kriteria" title="Kriteria" placeholder="Masukkan Kriteria"
                                        value="{{ $namaKategori }}" readonly>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="sub_kriteria" class="form-label">Sub Kriteria</label>
                                    <input type="text" class="form-control" name="sub_kriteria" id="sub_kriteria"
                                        aria-describedby="sub_kriteria" title="Kriteria" placeholder="Masukkan Sub Kriteria">
                                </div>
                                {{-- @foreach ($himpunan as $index => $huruf)
                                    <span>Himpunan <b>{{ $huruf }}</b></span>
                                    <input type="hidden" name="himpunan_{{ $huruf }}"
                                        value="{{ $huruf }}" id="">
                                    <div class="col-12 d-flex gap-1">
                                        <div class="col mb-3">
                                            <label for="min_{{ $huruf }}" class="form-label">Min</label>
                                            <input type="number" class="form-control" name="min_{{ $huruf }}"
                                                id="min_{{ $huruf }}" value="{{ $himpunan[0] }}"
                                                aria-describedby="min_{{ $huruf }}" min="0">
                                        </div>
                                        <div class="col mb-3">
                                            <label for="max_{{ $huruf }}" class="form-label">Max</label>
                                            <input type="number" class="form-control" name="max_{{ $huruf }}"
                                                id="max_{{ $huruf }}" value=""
                                                aria-describedby="max_{{ $huruf }}" min="0">
                                        </div>
                                    </div>
                                @endforeach --}}
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

    <form action="{{ route('penilaian.destroy', true) }}" method="POST" id="form-delete-all">
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
            const data = @json($helperKriteria);

            let detailKriteria = data.filter(item => item.kriteria == kriteria).map(item => item.himpunan).join(',');
            let detailMinKriteria = data.filter(item => item.kriteria == kriteria).map(item => item.min).join(',');
            let detailMaxKriteria = data.filter(item => item.kriteria == kriteria).map(item => item.max).join(',');

            detailKriteria = detailKriteria.split(',');
            detailMinKriteria = detailMinKriteria.split(',');
            detailMaxKriteria = detailMaxKriteria.split(',');

            detailKriteria.forEach((item, index) => {
                if (item === 'Baik Sekali') {
                    $('.min_baik_sekali').val(detailMinKriteria[3]).attr('readonly', true);
                    $('.max_baik_sekali').val(detailMaxKriteria[3]).attr('readonly', true);
                }

                $('#min_' + item).val(detailMinKriteria[index]).attr('readonly', true);
                $('#max_' + item).val(detailMaxKriteria[index]).attr('readonly', true);
            })
        }
    </script>
@endsection
