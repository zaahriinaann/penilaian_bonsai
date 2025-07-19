@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h1>Data Pendaftaran</h1>
                <button onclick="window.history.back()" class="btn btn-sm btn-primary">Kembali</button>
            </div>

            <div class="table-responsive">
                <table class="table text-nowrap">
                    <tbody>
                        <tr class="fw-bold">
                            <th colspan="3">
                                <h3>Detail Peserta</h3>
                            </th>
                            <th colspan="3">
                                <h3>Detail Pohon</h3>
                            </th>
                        </tr>
                        <tr>
                            <th>Nama Peserta</th>
                            <td>:</td>
                            <td>{{ $data->user->name }}</td>

                            <th>Nama Pohon</th>
                            <td>:</td>
                            <td>{{ $data->bonsai->nama_pohon }} / {{ $data->bonsai->nama_lokal }} /
                                {{ $data->bonsai->nama_latin }}</td>
                        </tr>
                        <tr>
                            <th>Nomor Anggota</th>
                            <td>:</td>
                            <td>{{ $data->user->no_anggota }}</td>

                            <th>Ukuran Pohon</th>
                            <td>:</td>
                            <td>{{ $data->bonsai->ukuran }}</td>
                        </tr>
                        <tr>
                            <th>Cabang</th>
                            <td>:</td>
                            <td>{{ $data->user->cabang }}</td>

                            <th>Nama Pohon</th>
                            <td>:</td>
                            <td>{{ $data->bonsai->masa_pemeliharann }} {{ $data->bonsai->format_masa }}</td>
                        </tr>
                        <tr>
                            <th>Nomor Telepon</th>
                            <td>:</td>
                            <td>{{ $data->user->no_hp }}</td>

                            <th>Nomor Juri</th>
                            <td>:</td>
                            <td>#{{ $data->nomor_juri }}</td>
                        </tr>
                        <tr>
                            <th>Alamat</th>
                            <td>:</td>
                            <td>{{ $data->user->alamat }}</td>

                            <th>Nomor Pendaftaran</th>
                            <td>:</td>
                            <td>#{{ $data->nomor_pendaftaran }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>:</td>
                            <td>{{ $data->user->email }}</td>

                            <th rowspan="1">Gambar Pohon</th>
                            <td rowspan="1">:</td>
                            <td rowspan="1">
                                <img class="rounded img-fluid w-25"
                                    src="{{ asset('assets/images/bonsai/' . $data->bonsai->foto) }}"
                                    alt="{{ $data->bonsai->foto }}">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
