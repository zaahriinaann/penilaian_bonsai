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
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Peserta</th>
                        <th>Nama Kontes</th>
                        <th>Tanggal Daftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection
