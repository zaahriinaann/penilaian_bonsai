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

        @if (!$data)
            <div class="alert alert-warning">
                Tidak ada data penilaian yang tersedia untuk bonsai ini.
            </div>
        @else
            <form action="{{ route('nilai.update', $bonsai->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="bonsai_id" value="{{ $bonsai->id }}">

                @foreach ($data as $kriteria)
                    <div class="card mb-4 border-0 shadow rounded-4">
                        @foreach ($kriteria['sub_kriterias'] as $sub)
                            <div class="card-body">
                                <span class="fw-bold fs-4">{{ $kriteria['kriteria'] }}</span>
                                <hr>
                                <div class="mb-3">
                                    <label class="form-label">
                                        Nilai untuk <strong>{{ $sub['nama_sub_kriteria'] }}</strong>
                                    </label>
                                    <input type="number" name="nilai[{{ $sub['id_sub_kriteria'] }}]" class="form-control"
                                        value="{{ $sub['nilai_awal'] ?? 0 }}">
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach

                {{-- Tombol hanya tampil jika domain tidak kosong --}}
                <button type="submit" class="btn btn-success px-4 py-2 rounded-3 shadow">Simpan Nilai</button>
            </form>
        @endif


    </div>
@endsection
