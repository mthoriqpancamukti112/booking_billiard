<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard')</title>
    <link rel="shortcut icon" type="image/png" href="/backend/assets/images/logos/logo.png" />
    <link rel="stylesheet" href="/backend/assets/css/styles.min.css" />
    @stack('css')
</head>

<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">

        {{-- Panggil Sidebar --}}
        @include('layout.be.sidebar')

        <div class="body-wrapper">
            {{-- Panggil Navbar --}}
            @include('layout.be.navbar')

            {{-- Konten Utama Halaman --}}
            <div class="container-fluid">
                @yield('content')
            </div>

        </div>
    </div>
    <script src="/backend/assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="/backend/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/backend./assets/js/sidebarmenu.js"></script>
    <script src="/backend/assets/js/app.min.js"></script>
    <script src="/backend/assets/libs/simplebar/dist/simplebar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('js')
</body>

</html>
