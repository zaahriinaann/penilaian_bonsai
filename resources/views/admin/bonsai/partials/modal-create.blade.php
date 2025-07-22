<div class="modal fade" id="kt_modal_create_bonsai" tabindex="-1" aria-labelledby="kt_modal_create_bonsai"
    aria-hidden="true">
    <div class="modal-dialog change-modal modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="kt_modal_create_bonsai">Data Bonsai Peserta</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-create-bonsai" action="{{ route('master.bonsai.store') }}" enctype="multipart/form-data"
                method="POST">
                @csrf
                @method('POST')
                <div class="modal-body">
                    <div id="form-sudah-daftar" class="row">
                        <div class="col-12 mb-3">
                            <label for="peserta" class="form-label">Nama Peserta</label>
                            <select id="peserta" name="peserta" class="text-capitalize peserta" required>
                                <option value="">Pilih Peserta</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3 pt-2" style="border-top: 1px dashed #ABABAB;">
                            <span class="fw-bold fs-5">Data Pohon</span>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="nama_pohon" class="form-label">Nama Pohon</label>
                            <input type="text" class="form-control" name="nama_pohon" id="nama_pohon"
                                placeholder="Masukkan Nama Pohon" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="d-flex gap-2">
                                <div class="w-100">
                                    <label for="nama_lokal" class="form-label">Nama Lokal Pohon</label>
                                    <input type="text" class="form-control" name="nama_lokal" id="nama_lokal"
                                        placeholder="Masukkan Nama Lokal">
                                </div>
                                <div class="w-100">
                                    <label for="nama_latin" class="form-label">Nama Latin Pohon</label>
                                    <input type="text" class="form-control" name="nama_latin" id="nama_latin"
                                        placeholder="Masukkan Nama Latin">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="kelas" class="form-label">Kelas</label>
                            <select name="kelas" id="kelas" class="form-select form-control" required>
                                <option selected disabled>Pilih kelas</option>
                                <option value="Bahan">Bahan</option>
                                <option value="Pratama">Pratama</option>
                                <option value="Madya">Madya</option>
                                <option value="Utama">Utama</option>
                                <option value="Bintang">Bintang</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="ukuran" class="form-label">Ukuran Pohon</label>
                            <div class="input-group">
                                <select name="ukuran_1" id="ukuran_1" class="form-select form-control" required>
                                    <option selected disabled>Pilih Ukuran</option>
                                    <option value="1">Small</option>
                                    <option value="2">Medium</option>
                                    <option value="3">Large</option>
                                </select>
                                <input type="number" class="form-control" name="ukuran_2" id="ukuran_2"
                                    placeholder="Ukuran Pohon" min="0" required>
                                <select name="format_ukuran" id="format_ukuran"
                                    class="form-select form-control text-capitalize" required>
                                    <option selected value="cm">cm</option>
                                    <option value="m">m</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="masa_pemeliharaan" class="form-label">Masa Pemeliharaan</label>
                            <div class="d-flex gap-2">
                                <input type="number" min="0" class="form-control" name="masa_pemeliharaan"
                                    id="masa_pemeliharaan" placeholder="Masukkan Masa Pemeliharaan">
                                <select name="format_masa" id="format_masa"
                                    class="form-select form-control text-capitalize">
                                    <option selected value="bulan">bulan</option>
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
                    <button type="reset" id="reset-btn" class="btn btn-sm btn-danger"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
