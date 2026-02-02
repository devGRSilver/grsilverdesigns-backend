<!doctype html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $title ?? 'GR SILVER DESIGNS' }}</title>

    <!-- SEO -->
    <meta name="description" content="Dashnix Admin Dashboard Template">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Theme Mode -->
    <meta name="theme-style-mode" content="1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('default_images/favicon.ico') }}" type="image/x-icon">

    <!-- Remixicon -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">

    <!-- Vendor CSS -->
    <link rel="stylesheet" href="{{ asset('assets/admin/css/vendor/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/vendor/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/vendor/spacing.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/vendor/magnific-popup.css') }}">

    <!-- Toastify CSS -->
    <link rel="stylesheet" href="{{ asset('assets/admin/css/plugins/toastify.min.css') }}">
    <script src="{{ asset('assets/admin/js/plugins/toastify/toastify.js') }}"></script>
    <script src="{{ asset('assets/admin/js/plugins/toastify/message.js') }}"></script>

    <!-- Animate.css CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <!-- Custom Blade styles -->
    @stack('style')

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('assets/admin/css/main.css') }}">
</head>

<body class="body-area">
    <!-- Dashboard Page Wrapper -->
    <div class="page">
        <!-- Sidebar -->
        @include('admin.components.sidebar')

        <!-- Topbar -->
        @include('admin.components.topbar')

        <!-- Main Content -->
        @yield('content')

        <!-- Footer -->
        @include('admin.components.footerbar')

        <!-- Search Modal -->
        <div class="modal fade" id="searchModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="input-group">
                            <input type="search" class="form-control" placeholder="Search...">
                            <button class="btn btn-primary" type="button" id="button-addon1">
                                <i class="ri-search-line"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Dashboard Page Wrapper End -->

    @include('admin.components.setting')

    <!-- JS Libraries -->
    <script src="{{ asset('assets/admin/js/vendor/jquery-3.7.0.js') }}"></script>
    <script src="{{ asset('assets/admin/js/plugins/popper.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/vendor/backtotop.js') }}"></script>
    <script src="{{ asset('assets/admin/js/vendor/sidebar.js') }}"></script>
    <script src="{{ asset('assets/admin/js/vendor/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/plugins/developer.js') }}"></script>
    <script src="{{ asset('assets/admin/js/main.js') }}"></script>

    <!-- Validation -->
    <script src="https://unpkg.com/just-validate@latest/dist/just-validate.production.min.js"></script>
    <script src="{{ asset('assets/admin/js/plugins/validation.js') }}"></script>

    @stack('scripts')
</body>

</html>
