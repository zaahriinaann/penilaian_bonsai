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
                    <tr>
                        <th>Jenis</th>
                        <td>{{ $bonsai->jenis }}</td>
                    </tr>
                    <tr>
                        <th>Asal</th>
                        <td>{{ $bonsai->asal }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <form action="{{ $nilaiTersimpan->isNotEmpty() ? route('nilai.update', $bonsai->id) : route('nilai.store') }}"
            method="POST">
            @csrf
            @if ($nilaiTersimpan->isNotEmpty())
                @method('PUT')
            @endif

            <input type="hidden" name="bonsai_id" value="{{ $bonsai->id }}">

            @foreach ($penilaians as $kriteria => $subGroups)
                <div class="card mb-4 border-0 shadow rounded-4">
                    <div class="card-header bg-primary text-white fw-bold">
                        {{ $kriteria }}
                    </div>
                    <div class="card-body">
                        @foreach ($subGroups as $subKriteria => $himpunans)
                            @php
                                $item = $himpunans->first(); // ambil satu entri saja (karena hanya beda himpunan)
                                $nilai = $nilaiTersimpan[$item->id]->d_keanggotaan ?? '';
                            @endphp
                            <div class="mb-3">
                                <label class="form-label">
                                    <strong>{{ $subKriteria }}</strong>
                                </label>
                                <input type="number" name="nilai[{{ $item->id }}]" class="form-control" step="0.01"
                                    value="{{ $nilai }}" required>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach




            <button type="submit" class="btn btn-success px-4 py-2 rounded-3 shadow">
                {{ $nilaiTersimpan->isNotEmpty() ? '💾 Perbarui Nilai' : '💾 Simpan Nilai' }}
            </button>
        </form>
    </div>
@endsection
