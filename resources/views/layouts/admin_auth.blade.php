<!doctype html>
<html lang="en" dir="ltr">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', 'GR SILVER DESIGNS')</title>
    <!-- SEO -->
    <meta name="description" content="Dashnix Admin Dashboard Template">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Theme Mode -->
    <meta name="theme-style-mode" content="1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ URL::asset('default_images/favicon.ico') }}" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">


    <!-- Vendor CSS -->
    <link rel="stylesheet" href="{{ URL::asset('assets/admin/css/vendor/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('assets/admin/css/vendor/animate.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('assets/admin/css/vendor/spacing.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('assets/admin/css/main.css') }}">


    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    @stack('style')
</head>


<body class="body-area">

    <!-- preloader start -->
    {{-- <div id="loading">
        <div id="loading-center">
            <div id="loading-center-absolute">
                <div class="bd-preloader-content">
                    <div class="bd-preloader-logo">
                        <div class="bd-preloader-circle">
                            <svg width="190" height="190" viewBox="0 0 380 380" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <circle stroke="#F5F5F5" cx="190" cy="190" r="180" stroke-width="6"
                                    stroke-linecap="round">
                                </circle>
                                <circle stroke="red" cx="190" cy="190" r="180" stroke-width="6"
                                    stroke-linecap="round">
                                </circle>
                            </svg>
                        </div>
                        <img src="{{ URL::asset('assets/admin/images/logo/preloader-icon.svg') }}"
                            alt="image not found">
                    </div>
                    <p class="bd-preloader-subtitle">Dashnix</p>
                </div>
            </div>
        </div>
    </div> --}}
    <!-- preloader end -->

    <!-- Dashboard page wrapper start -->
    <div class="page">
        <!-- app-content-area-start -->
        @yield('content');
    </div>
    <script src="{{ asset('assets/admin/js/vendor/jquery-3.7.0.js') }}"></script>
    <script src="https://unpkg.com/just-validate@latest/dist/just-validate.production.min.js"></script>
    <script src="{{ asset('assets/admin/js/vendor/magnific-popup.min.js') }}"></script>

    <script src="{{ asset('assets/admin/js/plugins/validation.js') }}"></script>
    <script src="{{ asset('assets/admin/js/plugins/toastify/message.js') }}"></script>



    @stack('scripts');
</body>



</html>
