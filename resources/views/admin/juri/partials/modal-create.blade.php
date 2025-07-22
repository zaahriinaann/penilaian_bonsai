<div class="modal fade" id="kt_modal_create_juri" tabindex="-1" aria-labelledby="kt_modal_create_juri" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('master.juri.store') }}" enctype="multipart/form-data" method="POST">
                @csrf
                @method('POST')
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Tambah Juri Kontes</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="nama_juri" class="form-label">Nama Juri</label>
                            <input type="text" name="nama_juri" id="nama_juri" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label for="email" class="form-label">Email</label>
                            <input type="text" name="email" id="email" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" name="username" id="username" class="form-control" required>
                            <span class="msg-slug"></span>
                        </div>
                        <div class="col-12">
                            <label for="no_telepon" class="form-label">No Telepon</label>
                            <input type="text" name="no_telepon" id="no_telepon" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="foto" class="form-label">Foto</label>
                            <input type="file" name="foto" id="foto" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="sertifikat" class="form-label">Sertifikat</label>
                            <input type="file" name="sertifikat" id="sertifikat" class="form-control" required>
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
