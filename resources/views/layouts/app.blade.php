<!DOCTYPE html>
<html lang="en">

<head>
    <base href="">
    <title> @yield('title') | {{ env('APP_NAME') }} | Aplikasi Penilaian Estetika Bonsai</title>
    <meta name="description"
        content="Ceres admin dashboard live demo. Check out all the features of the admin panel. A large number of settings, additional services and widgets." />
    <meta name="keywords"
        content="Ceres theme, bootstrap, bootstrap 5, admin themes, free admin themes, bootstrap admin, bootstrap dashboard" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta charset="utf-8" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="Ceres HTML Free - Bootstrap 5 HTML Multipurpose Admin Dashboard Theme" />
    <meta property="og:url" content="https://keenthemes.com/products/ceres-html-pro" />
    <meta property="og:site_name" content="Keenthemes | Ceres HTML Free" />
    <link rel="canonical" href="Https://preview.keenthemes.com/ceres-html-free" />
    {{-- <link rel="shortcut icon" href="{{ asset('assets/media/logos/favicon.ico') }}" /> --}}
    {{-- https://ppbindonesia.com/wp-content/uploads/2024/05/cropped-Logo-PPBI-300x155.png --}}
    <link rel="shortcut icon"
        href="https://ppbindonesia.com/wp-content/uploads/2024/05/cropped-Logo-PPBI-300x155.png" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
    <link href="{{ asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}" rel="stylesheet" <link
        href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('style')
</head>

<body id="kt_body" style="background-image: url({{ asset('assets/media/patterns/header-bg-green.png') }})"
    class="header-fixed header-tablet-and-mobile-fixed toolbar-enabled">
    <div class="d-flex flex-column flex-root">
        <div class="page d-flex flex-row flex-column-fluid">
            <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
                <div id="kt_header" class="header align-items-stretch" data-kt-sticky="true"
                    data-kt-sticky-name="header" data-kt-sticky-offset="{default: '200px', lg: '300px'}">
                    @include('layouts.header')
                </div>
                <div class="container">
                    @include('layouts.toolbar')
                </div>
                <div id="kt_content_container" class="d-flex flex-column-fluid align-items-start container-xxl">
                    <div class="container">
                        @yield('content')
                    </div>
                </div>
                @include('layouts.footer')
            </div>
        </div>
    </div>

    {{-- @include('layouts.etc') --}}
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/custom/widgets.js') }}"></script>
    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.js') }}"></script>
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('assets/plugins/custom/sweetalert/sweetalert.bundle.js') }}"></script>
    @yield('script')

    <script>
        $(document).ready(function() {
            $('.table-data').DataTable();

            setTimeout(() => {
                $('.custom-left-alert').fadeOut();
            }, 5000);

            $('#search-input').on('keyup', function() {
                const val = this.value.toLowerCase();
                let hasVisibleRows = false;

                $('.table-data tbody tr').not('.no-data').each(function() {
                    const isVisible = $(this).text().toLowerCase().includes(val);
                    $(this).toggle(isVisible);
                    if (isVisible) hasVisibleRows = true;
                });

                $('.table-data tbody .no-data').remove();

                if (!hasVisibleRows) {
                    $('.table-data tbody').append(
                        '<tr class="no-data"><td colspan="7" class="text-center">Data tidak ada</td></tr>'
                    );
                }
            });
        });

        // Initialize SweetAlert
        $(document).on('click', '.btn-delete', function() {
            const button = $(this);
            const id = button.data('id');
            const route = button.data('route');

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Anda tidak bisa mengembalikan data ini!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: route,
                        method: 'POST',
                        data: {
                            _method: 'DELETE',
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        // headers: {
                        //     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        // },
                        success: function(response) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Data telah dihapus!',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => location.reload());
                        },
                        error: function(xhr) {
                            Swal.fire({
                                title: 'Gagal!',
                                text: 'Ada kesalahan saat menghapus data. Silakan coba lagi.',
                                icon: 'error'
                            });
                            console.error('Error:', xhr.responseText);
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Dibatalkan',
                        text: 'Data tidak jadi dihapus.',
                        icon: 'info'
                    });
                }
            });
        });
    </script>
</body>

</html>
