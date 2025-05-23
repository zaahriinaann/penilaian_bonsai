@extends('layouts.app')

@section('title', 'Kelola Penilaian')


@section('button-toolbar')
    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_create_kriteria">
        Tambah Kriteria
    </button>
@endsection

@section('content')
    <div class="card mb-5">
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

    <div class="card">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="card-body text-capitalize">
            <h1>Penilaian</h1>
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
                        <h4 class="mt-4">{{ $namaKategori }}</h4>

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
                                                <th>Himpunan</th>
                                                <th>Min</th>
                                                <th>Max</th>
                                            </tr>
                                            <tr>
                                                <th>Semesta Pembicaraan</th>
                                                <th colspan="3">[50 - 90]</th>
                                            </tr>

                                            @foreach ($penilaiansItem as $huruf => $range)
                                                <tr>
                                                    <td>{{ strtoupper($huruf) }}</td>
                                                    <td>
                                                        <input type="number" class="form-control form-control-sm"
                                                            name="{{ $slug }}[{{ $huruf }}][min]"
                                                            value="{{ $range['min'] }}" placeholder="Min">
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control form-control-sm"
                                                            name="{{ $slug }}[{{ $huruf }}][max]"
                                                            value="{{ $range['max'] }}" placeholder="Max">
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-danger">
                                                            <i class="fa fa-trash"></i>
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

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Simpan Penilaian</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="kt_modal_create_kriteria" tabindex="-1" aria-labelledby="kt_modal_create_kriteria"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
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
                                <input type="text" class="form-control" name="kriteria" id="kriteria"
                                    aria-describedby="kriteria" title="Kriteria" placeholder="Masukkan Kriteria">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="sub_kriteria" class="form-label">Sub Kriteria</label>
                                <input type="text" class="form-control" name="sub_kriteria" id="sub_kriteria"
                                    aria-describedby="sub_kriteria" title="Kriteria" placeholder="Masukkan Kriteria">
                            </div>
                            <?php
                            $himpunan = ['a', 'b', 'c', 'd'];
                            ?>
                            @foreach ($himpunan as $huruf)
                                <span>Himpunan {{ $huruf }}</span>
                                <input type="hidden" name="himpunan_{{ $huruf }}" value="{{ $huruf }}"
                                    id="">
                                <div class="col-12 d-flex gap-1">
                                    <div class="col mb-3">
                                        <label for="min_{{ $huruf }}" class="form-label">Min</label>
                                        <input type="number" class="form-control" name="min_{{ $huruf }}"
                                            id="min_{{ $huruf }}" aria-describedby="min_{{ $huruf }}"
                                            min="0">
                                    </div>
                                    <div class="col mb-3">
                                        <label for="max_{{ $huruf }}" class="form-label">Max</label>
                                        <input type="number" class="form-control" name="max_{{ $huruf }}"
                                            id="max_{{ $huruf }}" aria-describedby="max_{{ $huruf }}"
                                            min="0">
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
@endsection

@section('script')
    <script></script>
@endsection
