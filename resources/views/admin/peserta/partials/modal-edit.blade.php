<div class="modal fade" id="kt_modal_edit_peserta" tabindex="-1" aria-labelledby="kt_modal_edit_peserta" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">Edit Peserta</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form id="form_edit_peserta" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_peserta_id">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="edit_nama" class="form-label">Nama</label>
                            <input type="text" class="form-control" name="nama" id="edit_nama">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_username" class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" id="edit_username">
                            <span class="edit_msg-slug small"></span>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_email" class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" id="edit_email">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_no_anggota" class="form-label">No Anggota</label>
                            <input type="text" class="form-control" name="no_anggota" id="edit_no_anggota">
                        </div>
                        @php
                            // (Opsional) urutkan cities, jika belum di-sort di Controller
                            $cities = collect($cities)->sortBy('name')->values()->all();
                        @endphp
                        <div class="col-md-6">
                            <label for="edit_cabang_input" class="form-label">Cabang</label>
                            <input list="citiesList" id="edit_cabang_input" name="cabang" class="form-control"
                                placeholder="Ketik dan pilih kota/kabupaten..." required>
                            {{-- Reuse datalist citiesList yang sama --}}
                            <datalist id="citiesList">
                                @foreach ($cities as $c)
                                    <option value="{{ $c['name'] }}">
                                @endforeach
                            </datalist>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_no_hp" class="form-label">No HP</label>
                            <input type="text" class="form-control" name="no_hp" id="edit_no_hp">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_alamat" class="form-label">Alamat</label>
                            <input type="text" class="form-control" name="alamat" id="edit_alamat">
                        </div>
                        <div class="col-12">
                            <label for="edit_foto" class="form-label">Foto (Opsional)</label>
                            <input type="file" class="form-control" name="foto" id="edit_foto">
                            <input type="hidden" name="foto_lama" id="edit_foto_lama">
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="gantiPassword">
                                <label class="form-check-label" for="gantiPassword">Ganti Password</label>
                            </div>
                            <div class="input-group mt-2" id="form-password" style="display: none;">
                                <input type="password" class="form-control" name="password" id="input-password"
                                    placeholder="Masukkan Password">
                                <span class="input-group-text cursor-pointer" id="show-password">
                                    <i class="bi bi-eye-fill" id="show-eye"></i>
                                    <i class="bi bi-eye-slash-fill d-none" id="hide-eye"></i>
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
