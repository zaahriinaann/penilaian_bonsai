<form action="{{ route('master.kontes.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="modal-header">
        <h5 class="modal-title">Tambah Kontes Bonsai</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
    </div>

    <div class="modal-body" style="max-height: calc(100vh - 200px); overflow-y: auto;">
        @include('admin.kontes.partials.form-fields', [
            'prefix' => '',
            'values' => [
                'nama_kontes' => '',
                'tempat_kontes' => '',
                'link_gmaps' => '',
                'tanggal_mulai_kontes' => '',
                'tanggal_selesai_kontes' => '',
                'tingkat_kontes' => '',
                'jumlah_peserta' => '',
            ],
        ])
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>

</form>
