<div class="modal fade" id="kt_modal_edit_juri" tabindex="-1" aria-labelledby="kt_modal_edit_juri" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="form_edit_juri" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Edit Data Juri</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="slug" id="edit_juri_slug">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="edit_nama_juri" class="form-label">Nama Juri</label>
                            <input type="text" name="nama_juri" id="edit_nama_juri" class="form-control">
                        </div>
                        <div class="col-12">
                            <label for="edit_email" class="form-label">Email</label>
                            <input type="text" name="email" id="edit_email" class="form-control">
                        </div>
                        <div class="col-12">
                            <label for="edit_username" class="form-label">Username</label>
                            <input type="text" name="username" id="edit_username" class="form-control">
                            <span class="edit_msg-slug"></span>
                        </div>
                        <div class="col-12">
                            <label for="edit_no_telepon" class="form-label">No Telepon</label>
                            <input type="text" name="no_telepon" id="edit_no_telepon" class="form-control">
                        </div>
                        <div class="col-12">
                            <label for="edit_status_juri" class="form-label">Status</label>
                            <select name="status" id="edit_status_juri" class="form-select">
                                <option disabled selected>Pilih Status</option>
                                <option value="1">Aktif</option>
                                <option value="2">Non Aktif</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_foto" class="form-label">Foto (Opsional)</label>
                            <input type="file" name="foto" id="edit_foto" class="form-control">
                            <input type="hidden" name="foto_lama" id="edit_foto_lama">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_sertifikat" class="form-label">Sertifikat (Opsional)</label>
                            <input type="file" name="sertifikat" id="edit_sertifikat" class="form-control">
                            <input type="hidden" name="sertifikat_lama" id="edit_sertifikat_lama">
                        </div>
                        <div class="col-12">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="gantiPassword">
                                <label class="form-check-label" for="gantiPassword">Ganti Password</label>
                            </div>
                            <div class="input-group" id="form-password" style="display: none;">
                                <input type="password" class="form-control" name="password" id="input-password"
                                    placeholder="Masukkan Password">
                                <span class="input-group-text cursor-pointer" id="show-password">
                                    <i class="bi bi-eye-slash-fill d-none" id="hide-eye"></i>
                                    <i class="bi bi-eye-fill" id="show-eye"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="reset" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary">Perbarui</button>
                </div>
            </form>
        </div>
    </div>
</div>
