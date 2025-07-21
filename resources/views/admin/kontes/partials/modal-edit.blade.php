<form id="form_edit_kontes" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="modal-header">
        <h5 class="modal-title">Edit Kontes Bonsai</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
    </div>

    <div class="modal-body" style="max-height: calc(100vh - 200px); overflow-y: auto;">
        @include('admin.kontes.partials.form-fields', [
            'prefix' => 'edit_',
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

        {{-- Hidden input untuk data tambahan --}}
        <input type="hidden" name="poster_lama" id="edit_poster_kontes_lama">
        <input type="hidden" name="slug" id="edit_kontes_slug">
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Perbarui</button>
    </div>
</form>
