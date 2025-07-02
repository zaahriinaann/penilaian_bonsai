@extends('layouts.app')

@section('title', 'Pendaftaran Peserta Kontes')

@section('button-toolbar')
    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_create_pendaftaran">
        Tambah Pendaftaran
    </button>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <h5>Data Pendaftaran Peserta Kontes</h5>
            <table class="table table-striped table-hover table-data">
                <thead class="text-nowrap">
                    <tr>
                        <th>#</th>
                        <th>Nomor Pendaftaran</th>
                        <th>Nomor Juri</th>
                        <th>Nama Peserta</th>
                        <th>Nama Bonsai</th>
                        <th>Nama Kontes</th>
                        <th>Kelas</th>
                        <th>Tanggal Daftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-nowrap text-capitalize">
                    @foreach ($pendaftaran as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->nomor_pendaftaran ?? 1 }}</td>
                            <td>{{ $item->nomor_juri ?? 1 }}</td>
                            <td>{{ $item->user->name }}</td>
                            <td>{{ $item->bonsai->nama_pohon }} - {{ $item->bonsai->no_induk_pohon }}</td>
                            <td>{{ $item->kontes->nama_kontes }}</td>
                            <td>{{ $item->kelas }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->created_at)->locale('id')->timezone('Asia/Jakarta')->translatedFormat('d F Y') }}
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#kt_modal_detail_pendaftaran">Detail</button>
                                <button class="btn btn-sm btn-warning">Edit</button>
                                <button class="btn btn-sm btn-danger btn-delete" title="Hapus data"
                                    data-id="{{ $item->id }}"
                                    data-route="{{ route('pendaftaran-peserta.destroy', $item->id) }}">Hapus</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @include('admin.pendaftaran.modal')
@endsection

@section('script')
    <script>
        // 
    </script>
@endsection
