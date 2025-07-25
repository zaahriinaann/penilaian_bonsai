<div class="modal fade" id="kt_modal_create_bonsai" tabindex="-1" aria-labelledby="kt_modal_create_bonsai"
    aria-hidden="true">
    <div class="modal-dialog change-modal modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">Tambah Bonsai</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-create-bonsai" action="{{ route('peserta.bonsaiSaya.store') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="nama_pohon" class="form-label">Nama Pohon</label>
                            <input type="text" class="form-control" name="nama_pohon" id="nama_pohon" required>
                        </div>
                        <div class="col-md-12 mb-3 d-flex gap-2">
                            <div class="w-100">
                                <label for="nama_lokal" class="form-label">Nama Lokal</label>
                                <input type="text" class="form-control" name="nama_lokal" id="nama_lokal">
                            </div>
                            <div class="w-100">
                                <label for="nama_latin" class="form-label">Nama Latin</label>
                                <input type="text" class="form-control" name="nama_latin" id="nama_latin">
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="kelas" class="form-label">Kelas</label>
                            <select name="kelas" id="kelas" class="form-select" required>
                                <option selected disabled>Pilih kelas</option>
                                <option value="Bahan">Bahan</option>
                                <option value="Pratama">Pratama</option>
                                <option value="Madya">Madya</option>
                                <option value="Utama">Utama</option>
                                <option value="Bintang">Bintang</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="ukuran_1" class="form-label">Ukuran</label>
                            <div class="input-group">
                                <select name="ukuran_1" id="ukuran_1" class="form-select" required>
                                    <option selected disabled>Pilih</option>
                                    <option value="1">Small</option>
                                    <option value="2">Medium</option>
                                    <option value="3">Large</option>
                                </select>
                                <input type="number" name="ukuran_2" class="form-control" min="0" required>
                                <select name="format_ukuran" class="form-select text-capitalize" required>
                                    <option value="cm">cm</option>
                                    <option value="m">m</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="masa_pemeliharaan" class="form-label">Masa Pemeliharaan</label>
                            <div class="input-group">
                                <input type="number" name="masa_pemeliharaan" class="form-control" min="0">
                                <select name="format_masa" class="form-select text-capitalize">
                                    <option value="bulan">bulan</option>
                                    <option value="tahun">tahun</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="foto" class="form-label">Foto Bonsai</label>
                            <input type="file" class="form-control" name="foto" id="foto">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="reset" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
