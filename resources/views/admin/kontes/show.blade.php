@extends('layouts.app')

@section('title', 'Detail Kontes')
@section('button-toolbar')
    <a class="btn btn-sm btn-primary" href="{{ route('kontes.index') }}">
        Kembali Ke Daftar Kontes
    </a>
@endsection
@section('content')
    <div class="card">
        <div class="card-body">
            <h5>Data Kontes {{ $kontes->nama_kontes }}</h5>
            <div class="d-flex gap-5">
                <img src="{{ asset('assets/images/kontes/' . $kontes->poster_kontes) ?? 'https://st2.depositphotos.com/1561359/12101/v/950/depositphotos_121012076-stock-illustration-blank-photo-icon.jpg' }}"
                    alt="Poster Kontes" class="w-25 rounded">
                <table class="table table-bordered table-striped table-hover align-middle">
                    <tr>
                        <th>Nama Kontes</th>
                        <th>:</th>
                        <td>{{ $kontes->nama_kontes }}</td>

                        <th>Tempat/Lokasi Kontes</th>
                        <th>:</th>
                        <td>
                            {{ $kontes->tempat_kontes }}
                            @if ($kontes->link_gmaps)
                                <a href="{{ $kontes->link_gmaps }}" target="_blank" title="Lihat di google maps">
                                    <i class="bi bi-geo-alt-fill"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Tingkatan Kontes</th>
                        <th>:</th>
                        <td>{{ $kontes->tingkat_kontes == 1 ? 'Madya' : 'Utama' }}</td>

                        <th>Tanggal Pelaksanaan</th>
                        <th>:</th>
                        <td>{{ \Carbon\Carbon::parse($kontes->tanggal_mulai_kontes)->locale('id')->timezone('Asia/Jakarta')->translatedFormat('d F Y') . ' - ' . \Carbon\Carbon::parse($kontes->tanggal_selesai_kontes)->locale('id')->timezone('Asia/Jakarta')->translatedFormat('d F Y') }}
                        </td>
                    </tr>
                    <tr>
                        <th>Jumlah Peserta</th>
                        <th>:</th>
                        <td>{{ $kontes->jumlah_peserta }} Peserta/Bonsai</td>
                        <th>Peserta Yang Mendaftar</th>
                        <th>:</th>
                        <td>45 Peserta/Bonsai</td>
                    </tr>
                    <tr>
                        <th>Harga Tiket Kontes</th>
                        <th>:</th>
                        <td>Rp{{ number_format($kontes->harga_tiket_kontes, 0, ',', '.') }}</td>

                        <th>Status Kontes</th>
                        <th>:</th>
                        <td>
                            @if ($kontes->status == 1)
                                <span class="badge bg-success">Berlangsung</span>
                            @else
                                <span class="badge bg-danger">Selesai</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
@endsection
