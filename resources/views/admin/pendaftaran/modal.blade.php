<div class="modal fade" id="kt_modal_create_pendaftaran" tabindex="-1" aria-labelledby="kt_modal_create_pendaftaran"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="kt_modal_create_pendaftaran">Data Pendaftaran Peserta Kontes</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('kontes.pendaftaran-peserta.store') }}" enctype="multipart/form-data" method="POST">
                @csrf
                @method('POST')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <label for="peserta" class="form-label">
                                Nama Peserta
                            </label>
                            <select id="peserta" name="user_id" class="text-capitalize peserta">
                            </select>
                        </div>
                        <div id="input-pohon-peserta" class="d-none">
                            <div class="col-12">
                                <label for="pohon-peserta" class="form-label">
                                    Pohon peserta
                                </label>
                                <select id="pohon-peserta" name="bonsai_id" class="text-capitalize pohon-peserta">
                                </select>
                            </div>
                            <div class="col-12 d-none">
                                <label for="kelas-pohon" class="form-label">
                                    Kelas Pohon
                                </label>
                                <select id="kelas-pohon" name="kelas" class="text-capitalize kelas-pohon form-select">
                                    <option selected disabled>Pilih Kelas</option>
                                    <option value="bahan">Bahan</option>
                                    <option value="matang">Matang</option>
                                </select>
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

<div class="modal fade" id="kt_modal_edit_pendaftaran" tabindex="-1" aria-labelledby="kt_modal_edit_pendaftaran"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="kt_modal_edit_pendaftaran">Edit Data Pendaftaran Peserta Kontes</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('kontes.pendaftaran-peserta.store') }}" enctype="multipart/form-data" method="POST">
                @csrf
                @method('POST')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <label for="peserta" class="form-label">
                                Nama Peserta
                            </label>
                            {{-- <select id="peserta-edit" readonly name="user_id" class="text-capitalize peserta">
                            </select> --}}
                            <input type="text" placeholder="Nama Peserta" readonly class="form-control"
                                id="peserta-edit">
                        </div>
                        <div id="input-pohon-peserta">
                            <div class="col-12">
                                <label for="pohon-peserta" class="form-label">
                                    Pohon peserta
                                </label>
                                <select id="pohon-peserta-edit" name="bonsai_id" class="text-capitalize pohon-peserta">
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="kelas-pohon" class="form-label">
                                    Kelas Pohon
                                </label>
                                <select id="kelas-pohon-edit" name="kelas"
                                    class="text-capitalize kelas-pohon form-select d-none">
                                    <option selected disabled>Pilih Kelas</option>
                                    <option value="bahan">Bahan</option>
                                    <option value="matang">Matang</option>
                                </select>
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
@section('script')
    <script>
        $(document).ready(function() {
            const pesertaSelect = $('#peserta');
            const pohonSelect = $('#pohon-peserta');
            const pohonWrapper = $('#input-pohon-peserta');

            // Inisialisasi Selectize untuk peserta
            pesertaSelect.selectize({
                allowEmptyOption: true,
                placeholder: 'Pilih Peserta',
                theme: 'bootstrap-5',
                valueField: 'id',
                labelField: 'name',
                searchField: 'name',
                maxItems: 1,
                options: @json($peserta)
            });

            // Event change peserta
            pesertaSelect.on('change', function() {
                const pesertaId = $(this).val();
                if (!pesertaId) return;

                $.ajax({
                    url: 'get-bonsai-peserta/' + pesertaId,
                    method: 'GET',
                    success: function(data) {
                        if (data.length) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Mengambil data pohon!',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });

                            // Destroy selectize sebelumnya jika ada
                            pohonSelect.each(function() {
                                if ($(this)[0].selectize) {
                                    $(this)[0].selectize.destroy();
                                }
                            });

                            // Inisialisasi ulang Selectize untuk pohon
                            pohonSelect.selectize({
                                allowEmptyOption: true,
                                placeholder: 'Pilih Pohon Peserta',
                                theme: 'bootstrap-5',
                                valueField: 'id',
                                labelField: 'nama_pohon',
                                searchField: 'nama_pohon',
                                maxItems: 1,
                                options: data,
                                render: {
                                    item: (data, escape) => {
                                        return `<div>${escape(data.nama_pohon)} (${escape(data.no_induk_pohon)} | ${escape(data.kelas)})</div>`;
                                    },
                                    option: (data, escape) => {
                                        return `<div>${escape(data.nama_pohon)} (${escape(data.no_induk_pohon)} | ${escape(data.kelas)})</div>`;
                                    }
                                }
                            });

                            pohonWrapper.removeClass('d-none');
                        } else {
                            Swal.fire({
                                title: 'Gagal!',
                                text: 'Peserta belum memiliki pohon!',
                                icon: 'error',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            pohonWrapper.addClass('d-none');
                        }
                    },
                    error: function(err) {
                        alert('Terjadi kesalahan saat mengambil data bonsai.');
                        console.error(err);
                    }
                });
            });

            // Tombol edit (kalau kamu akan kembangkan fitur ini)
            $('.btn-edit').on('click', function() {
                const id = $(this).data('id');
                const nama = $(this).data('nama');
                const userId = $(this).data('user-id');
                const bonsaiId = $(this).data('bonsai-id');
                const kelas = $(this).data('kelas');
                const actionUrl = $(this).data('action');

                // Set form action
                $('#form-edit').attr('action', actionUrl);

                // Set nilai kelas
                $('#kelas-pohon-edit').val(kelas);


                // Atur ulang selectize peserta
                const pesertaEdit = $('#peserta-edit');
                pesertaEdit.val(nama);

                $.ajax({
                    url: 'get-bonsai-peserta/' + userId,
                    method: 'GET',
                    success: function(data) {
                        if (data.length) {
                            const pohonEdit = $('#pohon-peserta-edit');

                            $('#pohon-peserta-edit').selectize({
                                allowEmptyOption: true,
                                placeholder: 'Pilih Pohon Peserta',
                                theme: 'bootstrap-5',
                                valueField: 'id',
                                labelField: 'nama_pohon',
                                searchField: 'nama_pohon',
                                maxItems: 1,
                                options: data,
                                render: {
                                    item: (data, escape) => {
                                        return `<div>${escape(data.nama_pohon)} (${escape(data.no_induk_pohon)})</div>`;
                                    },
                                    option: (data, escape) => {
                                        return `<div>${escape(data.nama_pohon)} (${escape(data.no_induk_pohon)})</div>`;
                                    }
                                },
                                onInitialize: function() {
                                    this.setValue(bonsaiId);
                                }
                            });

                            $('#input-pohon-peserta-edit').removeClass(
                                'd-none');
                        } else {
                            Swal.fire({
                                title: 'Gagal!',
                                text: 'Peserta belum memiliki pohon!',
                                icon: 'error',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            $('#input-pohon-peserta-edit').addClass(
                                'd-none');
                        }
                    }
                });
            });
        });
    </script>
@endsection
