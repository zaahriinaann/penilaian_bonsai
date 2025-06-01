<!DOCTYPE html>
<html lang="en">

<head>
    <base href="">
    <title> Login | {{ env('APP_NAME') }} | Aplikasi Penilaian Estetika Bonsai</title>

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
    <link href="{{ asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}" type="text/css" />
</head>

<body id="kt_body" style="background-image: url(assets/media/patterns/header-bg-green.png)"
    class="header-fixed header-tablet-and-mobile-fixed toolbar-enabled">
    <div class="d-flex flex-column flex-root">
        <div class="page d-flex flex-row flex-column-fluid">
            <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
                @yield('content')
                
                @include('layouts.footer')
            </div>
        </div>
    </div>
    
    @yield('script')
    <script src="{{ asset('template/src/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
    <script src="{{ asset('template/src/plugins/custom/fullcalendar/fullcalendar.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/custom/widgets.js') }}"></script>
</body>

</html>
