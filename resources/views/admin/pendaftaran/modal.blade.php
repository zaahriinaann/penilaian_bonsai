<div class="modal fade" id="kt_modal_create_pendaftaran" tabindex="-1" aria-labelledby="kt_modal_create_pendaftaran"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="kt_modal_create_pendaftaran">Data Pendaftaran Peserta Kontes</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('pendaftaran-peserta.store') }}" enctype="multipart/form-data" method="POST">
                @csrf
                @method('POST')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <label for="kontes" class="form-label">
                                Nama Kontes
                            </label>
                            <select id="kontes" name="kontes_id" class="text-capitalize kontes">
                            </select>
                        </div>
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
                            <div class="col-12">
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

@section('script')
    <script>
        $(document).ready(function() {
            $('#kontes').selectize({
                allowEmptyOption: true,
                placeholder: 'Pilih Kontes',
                theme: 'bootstrap-5',
                valueField: 'id',
                labelField: 'nama_kontes',
                searchField: 'name',
                maxItems: 1,
                options: @json($kontes),
            });

            $('#peserta').selectize({
                allowEmptyOption: true,
                placeholder: 'Pilih Peserta',
                theme: 'bootstrap-5',
                valueField: 'id',
                labelField: 'name',
                searchField: 'name',
                maxItems: 1,
                options: @json($peserta),
            });

            $('#peserta').on('change', function() {
                $.ajax({
                    url: 'get-bonsai-peserta/' + $(this).val(),
                    method: 'GET',
                    success: (data) => {
                        console.log(data);
                        if (data.length) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Mengambil data pohon!',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                $('#pohon-peserta').selectize({
                                    allowEmptyOption: true,
                                    placeholder: 'Pilih Pohon Peserta',
                                    theme: 'bootstrap-5',
                                    valueField: 'id',
                                    labelField: ['nama_pohon',
                                        'no_induk_pohon'
                                    ],
                                    render: {
                                        item: (data, escape) => {
                                            return `<div>${escape(data.nama_pohon)} (${escape(data.no_induk_pohon)})</div>`;
                                        },
                                        option: (data, escape) => {
                                            return `<div>${escape(data.nama_pohon)} (${escape(data.no_induk_pohon)})</div>`;
                                        },
                                    },
                                    searchField: 'nama_pohon',
                                    maxItems: 1,
                                    options: data,
                                });

                                $('#input-pohon-peserta').removeClass('d-none');
                            });
                        } else {
                            Swal.fire({
                                title: 'Gagal!',
                                text: 'Peserta belum memiliki pohon!',
                                icon: 'error',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                $('#input-pohon-peserta').addClass('d-none');
                            });
                        }
                    },
                    error: (err) => {
                        alert('Terjadi kesalahan');
                        console.log(err);
                    }
                })
            })
        })
    </script>
@endsection
