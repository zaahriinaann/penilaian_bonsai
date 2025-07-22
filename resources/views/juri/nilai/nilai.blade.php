@extends('layouts.app')

@section('title', 'Form Penilaian')

@section('content')
    <div class="container">
        {{-- <h3 class="mb-4">📝 Form Penilaian Bonsai</h3> --}}

        <div class="card shadow-sm rounded-4 mb-4">
            <div class="card-body">
                <h5 class="card-title">📌 Informasi Bonsai</h5>
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 30%">Nama Pemilik</th>
                        <td>{{ $bonsai->user->name }}</td>
                    </tr>
                    <tr>
                        <th>Nama Bonsai</th>
                        <td>{{ $bonsai->nama_pohon }}</td>
                    </tr>
                    {{-- <tr>
                        <th>Jenis</th>
                        <td>{{ $bonsai->jenis }}</td>
                    </tr>
                    <tr>
                        <th>Asal</th>
                        <td>{{ $bonsai->asal }}</td>
                    </tr> --}}
                </table>
            </div>
        </div>

        @if ($domains->isEmpty())
            <div class="alert alert-warning">
                Tidak ada data penilaian yang tersedia untuk bonsai ini.
            </div>
        @else
            <form action="{{ route('juri.nilai.store') }}" method="POST">
                @csrf
                <input type="hidden" name="bonsai_id" value="{{ $bonsai->id }}">

                @foreach ($domains as $idKriteria => $groupedSubKriteria)
                    @php
                        $namaKriteria = $groupedSubKriteria->first()?->subKriteria->kriteria ?? null;
                        $groupedBySub = $groupedSubKriteria->groupBy('id_sub_kriteria');
                    @endphp

                    @if ($namaKriteria && $groupedBySub->isNotEmpty())
                        <div class="card mb-4 border-0 shadow rounded-4">
                            <div class="card-body">
                                <span class="fw-bold fs-4">{{ $namaKriteria }}</span>
                                <hr>

                                @foreach ($groupedBySub as $idSubKriteria => $domainsPerSub)
                                    @php
                                        $subNama = $domainsPerSub->first()?->subKriteria?->sub_kriteria ?? null;
                                        $min = $domainsPerSub->last()?->domain_min ?? null;
                                        $max = $domainsPerSub->first()?->domain_max ?? null;
                                    @endphp

                                    @if ($subNama)
                                        <div class="mb-3">
                                            <label class="form-label">Nilai untuk
                                                <strong>{{ $subNama }}</strong>
                                            </label>

                                            <input type="number" name="nilai[{{ $idSubKriteria }}]" class="form-control"
                                                required min="{{ $min }}" max="{{ $max }}">
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach

                {{-- Tombol hanya tampil jika domain tidak kosong --}}
                <button type="submit" class="btn btn-success px-4 py-2 rounded-3 shadow">Simpan Nilai</button>
            </form>
        @endif


    </div>
@endsection
