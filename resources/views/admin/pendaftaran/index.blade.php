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
            <div class="table-responsive">
                <table class="table table-striped table-hover table-data text-nowrap">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nomor Pendaftaran</th>
                            <th>Nomor Juri</th>
                            <th>Nama Bonsai</th>
                            <th>Pemilik</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-nowrap text-capitalize">
                        @foreach ($pendaftaran as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->nomor_pendaftaran ?? 1 }}</td>
                                <td>{{ $item->nomor_juri ?? 1 }}</td>
                                <td>{{ $item->bonsai->nama_pohon }} - {{ $item->bonsai->no_induk_pohon }} - {{ $item->kelas }}</td>
                                <td>{{ $item->user->name }}</td>
                                <td>
                                    <a href="{{ route('pendaftaran-peserta.show', $item->id) }}"
                                        class="btn btn-sm btn-primary">Detail</a>
                                    {{-- <button class="btn btn-sm btn-warning btn-edit" data-bs-toggle="modal"
                                        data-bs-target="#kt_modal_edit_pendaftaran" title="Edit data"
                                        data-id="{{ $item->id }}" data-nama="{{ $item->user->name }}"
                                        data-user-id="{{ $item->user_id }}" data-bonsai-id="{{ $item->bonsai_id }}"
                                        data-kelas="{{ $item->kelas }}"
                                        data-action="{{ route('pendaftaran-peserta.update', $item->id) }}">
                                        Edit
                                    </button> --}}
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
    </div>

    @include('admin.pendaftaran.modal')
@endsection

@section('script')
    <script>
        // $(document).ready(function() {

        // })
    </script>
@endsection
