<div class="modal fade" id="kt_modal_create_peserta" tabindex="-1" aria-labelledby="kt_modal_create_peserta"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">Tambah Peserta</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form action="{{ route('master.peserta.store') }}" enctype="multipart/form-data" method="POST">
                @csrf
                @method('POST')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" class="form-control" name="nama" id="nama" required>
                        </div>
                        <div class="col-md-6">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" id="username" required>
                            <span class="msg-slug small"></span>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" id="email" required>
                        </div>
                        <div class="col-md-6">
                            <label for="no_anggota" class="form-label">No Anggota</label>
                            <input type="text" class="form-control" name="no_anggota" id="no_anggota" required>
                        </div>
                        @php
                            // Urutkan cities berdasarkan name
                            $cities = collect($cities)->sortBy('name')->values()->all();
                        @endphp
                        <div class="col-md-6">
                            <label for="cabang_input" class="form-label">Cabang</label>
                            <input list="citiesList" id="cabang_input" name="cabang" class="form-control"
                                placeholder="Ketik dan pilih kota/kabupaten..." required>
                            <datalist id="citiesList">
                                @foreach ($cities as $c)
                                    <option value="{{ $c['name'] }}">
                                @endforeach
                            </datalist>
                        </div>
                        <div class="col-md-6">
                            <label for="no_hp" class="form-label">No HP</label>
                            <input type="text" class="form-control" name="no_hp" id="no_hp" required>
                        </div>
                        <div class="col-md-6">
                            <label for="alamat" class="form-label">Alamat</label>
                            <input type="text" class="form-control" name="alamat" id="alamat" required>
                        </div>
                        <div class="col-12">
                            <label for="foto" class="form-label">Foto</label>
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
