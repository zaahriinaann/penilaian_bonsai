@extends('layouts.app')

@section('title', 'Detail Kontes')

@section('button-toolbar')
    <a class="btn btn-sm btn-primary" href="{{ route('master.kontes.index') }}">
        Kembali Ke Daftar Kontes
    </a>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="mb-4">Data Kontes: {{ $kontes->nama_kontes }}</h5>

            <div class="row">
                <div class="col-md-4 mb-3 text-center">
                    <img src="{{ $kontes->poster_kontes ? asset('assets/images/kontes/' . $kontes->poster_kontes) : 'https://via.placeholder.com/300x400?text=Poster+Kontes' }}"
                        alt="Poster Kontes" class="img-fluid rounded shadow-sm w-100"
                        style="max-height: 400px; object-fit: cover;">
                </div>

                <div class="col-md-8">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <tbody>
                                <tr>
                                    <th scope="row">Nama Kontes</th>
                                    <td>{{ $kontes->nama_kontes }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Tempat / Lokasi</th>
                                    <td>
                                        {{ $kontes->tempat_kontes }}
                                        @if ($kontes->link_gmaps)
                                            <a href="{{ $kontes->link_gmaps }}" target="_blank" class="ms-2"
                                                title="Lihat di Google Maps">
                                                <i class="bi bi-geo-alt-fill"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Tingkatan Kontes</th>
                                    <td>{{ $kontes->tingkat_kontes }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Tanggal Pelaksanaan</th>
                                    <td>
                                        {{ \Carbon\Carbon::parse($kontes->tanggal_mulai_kontes)->locale('id')->translatedFormat('d F Y') }}
                                        -
                                        {{ \Carbon\Carbon::parse($kontes->tanggal_selesai_kontes)->locale('id')->translatedFormat('d F Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Jumlah Peserta</th>
                                    <td>{{ $kontes->jumlah_peserta }} Peserta/Bonsai</td>
                                </tr>
                                <tr>
                                    <th scope="row">Peserta yang Mendaftar</th>
                                    <td>{{ $kontes->jumlah_peserta }} Peserta/Bonsai</td>
                                </tr>
                                <tr>
                                    <th scope="row">Harga Tiket Kontes</th>
                                    <td>Rp{{ number_format($kontes->harga_tiket_kontes, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Status Kontes</th>
                                    <td>
                                        @if ($kontes->status == 1)
                                            <span class="badge bg-success">Berlangsung</span>
                                        @else
                                            <span class="badge bg-danger">Selesai</span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
