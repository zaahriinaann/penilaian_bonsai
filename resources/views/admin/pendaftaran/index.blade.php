@extends('layouts.app')

@section('title', 'Kelola Pendaftaran Kontes')

@section('button-toolbar')
    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_create_pendaftaran">
        Tambah Pendaftaran
    </button>
@endsection

@section('content')
    <div class="card">
        <div class="card-header align-items-center">
            <h5>Data Pendaftaran Peserta Kontes</h5>
        </div>
        <div class="card-body">
            {{-- Form Pencarian --}}
            <form method="GET" action="{{ route('kontes.pendaftaran-peserta.index') }}" class="mb-3">
                <div class="row g-2 align-items-center">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" name="search" class="form-control border-start-0"
                                placeholder="Cari nomor pendaftaran / nama bonsai / pemilik..."
                                value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-filter-circle"></i> Filter
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('kontes.pendaftaran-peserta.index') }}" class="btn btn-secondary w-100">
                            <i class="bi bi-x-circle"></i> Reset
                        </a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover table-borderless align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nomor Pendaftaran</th>
                            <th>Nomor Juri</th>
                            <th>Nama Bonsai</th>
                            <th>Pemilik</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pendaftaran as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->nomor_pendaftaran }}</td>
                                <td>{{ $item->nomor_juri }}</td>
                                <td>
                                    {{ $item->bonsai->nama_pohon }}<br>
                                    <small class="text-muted">No. {{ $item->bonsai->no_induk_pohon }}
                                        ({{ $item->kelas }})
                                    </small>
                                </td>
                                <td>{{ $item->user->name }}</td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        <a href="{{ route('kontes.pendaftaran-peserta.show', $item->id) }}"
                                            class="btn btn-sm btn-primary" title="Detail">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        <button class="btn btn-sm btn-danger btn-delete" title="Hapus data"
                                            data-id="{{ $item->id }}"
                                            data-route="{{ route('kontes.pendaftaran-peserta.destroy', $item->id) }}">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $pendaftaran->links('vendor.pagination.bootstrap-5') }}
            </div>

        </div>
    </div>

    {{-- Include Modals --}}
    @include('admin.pendaftaran.modal')
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            // Reset form setiap modal tampil
            $('#kt_modal_create_pendaftaran').on('show.bs.modal', function() {
                $(this).find('form')[0].reset();
            });

            // Inisialisasi delete
            $('.btn-delete').on('click', function() {
                const btn = $(this);
                if (confirm('Yakin ingin menghapus pendaftaran ini?')) {
                    $.ajax({
                        url: btn.data('route'),
                        method: 'DELETE',
                        success() {
                            location.reload();
                        },
                        error() {
                            alert('Gagal menghapus.');
                        }
                    });
                }
            });
        });
    </script>
