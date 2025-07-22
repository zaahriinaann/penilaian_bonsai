@foreach ($dataRender as $item)
    <div class="modal fade" id="kt_modal_edit_bonsai" tabindex="-1" aria-labelledby="kt_modal_edit_bonsai"
        aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <form id="form_edit_bonsai" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" name="slug" id="edit_bonsai_slug">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="peserta" class="form-label">Nama Peserta</label>
                                <select id="peserta" class="text-capitalize peserta" name="user_id"></select>
                            </div>
                            <div class="col-md-12 mb-3 pt-2" style="border-top: 1px dashed #ABABAB;">
                                <span class="fw-bold fs-5">Data Pohon</span>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label>Nama Pohon</label>
                                <input type="text" class="form-control" name="nama_pohon" id="edit_nama_pohon"
                                    required>
                            </div>
                            <div class="col-12 d-flex gap-2 mb-3">
                                <div class="w-100">
                                    <label>Nama Lokal</label>
                                    <input type="text" class="form-control" name="nama_lokal" id="edit_nama_lokal">
                                </div>
                                <div class="w-100">
                                    <label>Nama Latin</label>
                                    <input type="text" class="form-control" name="nama_latin" id="edit_nama_latin">
                                </div>
                            </div>
                            <div class="col-md-12 mb-3 d-none">
                                <label for="kelas" class="form-label">kelas</label>
                                <select name="kelas" id="edit_kelas" class="form-select form-control text-capitalize"
                                    required>
                                    <option value="Bahan">Bahan</option>
                                    <option value="Pratama">Pratama</option>
                                    <option value="Madya">Madya</option>
                                    <option value="Utama">Utama</option>
                                    <option value="Bintang">Bintang</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label>Ukuran</label>
                                <div class="input-group">
                                    <select class="form-select" name="ukuran_1" id="edit_ukuran_1" required>
                                        <option value="1">Small</option>
                                        <option value="2">Medium</option>
                                        <option value="3">Large</option>
                                    </select>
                                    <input type="number" class="form-control" name="ukuran_2" id="edit_ukuran_2"
                                        min="0" required>
                                    <select class="form-select" name="format_ukuran" id="edit_format_ukuran" required>
                                        <option value="cm">cm</option>
                                        <option value="m">m</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label>Masa Pemeliharaan</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="masa_pemeliharaan"
                                        id="edit_masa_pemeliharaan" min="0">
                                    <select class="form-select" name="format_masa" id="edit_format_masa">
                                        <option value="hari">hari</option>
                                        <option value="bulan">bulan</option>
                                        <option value="tahun">tahun</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="edit_foto" class="form-label">Foto Bonsai</label>
                                <input type="file" class="form-control" name="foto" id="edit_foto">
                                <input type="hidden" name="foto_lama" id="edit_foto_lama">
                                <div class="mt-2" id="edit_foto_container">
                                    <label class="form-label">Foto Lama</label>
                                    <img src="" alt="Foto Bonsai" class="img-fluid rounded"
                                        id="edit_foto_preview" style="max-height:150px;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="reset" id="reset-btn" class="btn btn-sm btn-danger"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
