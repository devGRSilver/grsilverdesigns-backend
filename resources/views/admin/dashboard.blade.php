@extends('layouts.admin')
@section('content')
    <style>
        .stats-card-hover {
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .stats-card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08) !important;
        }

        .bg-primary-soft {
            background-color: rgba(13, 110, 253, 0.1);
        }

        .bg-info-soft {
            background-color: rgba(13, 202, 240, 0.1);
        }

        .bg-danger-soft {
            background-color: rgba(220, 53, 69, 0.1);
        }

        .bg-success-soft {
            background-color: rgba(25, 135, 84, 0.1);
        }

        .bg-warning-soft {
            background-color: rgba(255, 193, 7, 0.1);
        }

        .avatar-lg {
            width: 56px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .fs-13 {
            font-size: 13px;
        }

        .fs-14 {
            font-size: 14px;
        }

        .fs-16 {
            font-size: 16px;
        }

        .fs-32 {
            font-size: 32px;
        }

        .fs-42 {
            font-size: 42px;
        }

        .rounded-3 {
            border-radius: 12px !important;
        }

        /* Fixed height cards for consistency */
        .height-equal {
            min-height: 450px;
        }

        /* Loading skeleton */
        .skeleton {
            animation: skeleton-loading 1s linear infinite alternate;
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
        }

        @keyframes skeleton-loading {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }

        .skeleton-text {
            height: 16px;
            margin-bottom: 8px;
            border-radius: 4px;
        }

        .skeleton-title {
            height: 24px;
            width: 60%;
            margin-bottom: 12px;
            border-radius: 4px;
        }

        /* Consistent card body padding */
        .card-body {
            padding: 1.5rem;
        }

        /* Fixed table wrapper height */
        .table-wrapper-fixed {
            min-height: 400px;
        }



        .transactions-list::-webkit-scrollbar {
            width: 6px;
        }

        .transactions-list::-webkit-scrollbar-thumb {
            background: #ddd;
            border-radius: 3px;
        }



        #topCustomersList::-webkit-scrollbar {
            width: 6px;
        }

        #topCustomersList::-webkit-scrollbar-thumb {
            background: #ddd;
            border-radius: 3px;
        }

        /* Chart container fixed height */
        #order-status {
            min-height: 352px;
        }

        /* Sales countries list fixed height */
        #salesCountriesList {
            min-height: 320px;
        }

        /* Trending products fixed height */
        .card-carousel {
            min-height: 380px;
        }

        /* Global loader */
        #globalLoader {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.95);
            z-index: 99999;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(4px);
        }

        #globalLoader.show {
            display: flex !important;
        }

        .loader-content {
            text-align: center;
        }

        .loader-spinner {
            width: 60px;
            height: 60px;
            border: 6px solid #f3f3f3;
            border-top: 6px solid #3498db;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin: 0 auto 20px;
        }

        .loader-text {
            color: #3498db;
            font-size: 16px;
            font-weight: 600;
            margin-top: 10px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .avatar-lg {
                width: 48px;
                height: 48px;
            }

            .fs-32 {
                font-size: 28px;
            }

            .card-body {
                padding: 1rem !important;
            }

            h2 {
                font-size: 1.5rem;
            }

            .height-equal {
                min-height: auto;
            }
        }
    </style>
    <div class="app-content-area">
        <div class="app-content-wrap">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="page-title-box d-flex-between flex-wrap gap-15 mb-4">
                            <h1 class="page-title fs-18 lh-1 mb-0">Dashboard</h1>
                            <nav aria-label="breadcrumb" style="width: 230px">
                                <div class="col-md-12">
                                    <input id="rangeCalendar" type="text" class="form-control"
                                        placeholder="Select date range" readonly>
                                </div>
                            </nav>
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="col-12">
                        <div class="row" id="stats-wrapper">
                            <!-- Total Orders -->
                            <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 mb-3">
                                <a href="{{ route('orders.index') }}" class="text-decoration-none">
                                    <div class="card border-0 shadow-sm h-100 stats-card-hover">
                                        <div class="card-body d-flex align-items-start gap-2">
                                            <div
                                                class="avatar avatar-lg bg-primary-soft text-primary rounded-3 p-3 flex-shrink-0">
                                                <i class="ri-shopping-bag-3-line fs-32"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <span class="fs-14 text-muted fw-medium d-block mb-2">Total Orders</span>
                                                <h2 class="fw-bold mb-0 stat-value" data-key="total_orders">0</h2>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 mb-3">
                                <a href="{{ route('orders.index') }}" class="text-decoration-none">
                                    <div class="card border-0 shadow-sm h-100 stats-card-hover">
                                        <div class="card-body d-flex align-items-start gap-2">
                                            <div
                                                class="avatar avatar-lg bg-danger-soft text-danger rounded-3 p-3 flex-shrink-0">
                                                <i class="ri-shopping-bag-3-line fs-32"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <span class="fs-14 text-muted fw-medium d-block mb-2">New
                                                    Orders</span>
                                                <h2 class="fw-bold mb-0 stat-value" data-key="new_orders">0</h2>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>



                            <!-- Processing Orders -->
                            <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 mb-3">
                                <a href="{{ route('orders.index') }}" class="text-decoration-none">
                                    <div class="card border-0 shadow-sm h-100 stats-card-hover">
                                        <div class="card-body d-flex align-items-start gap-2">
                                            <div
                                                class="avatar avatar-lg bg-info-soft text-info rounded-3 p-3 flex-shrink-0">
                                                <i class="ri-loader-4-line fs-32"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <span class="fs-14 text-muted fw-medium d-block mb-2">Processing
                                                    Orders</span>
                                                <h2 class="fw-bold mb-0 stat-value" data-key="processing_orders">0</h2>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <!-- Delivered Orders -->
                            <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 mb-3">
                                <a href="{{ route('orders.index') }}" class="text-decoration-none">
                                    <div class="card border-0 shadow-sm h-100 stats-card-hover">
                                        <div class="card-body d-flex align-items-start gap-2">
                                            <div
                                                class="avatar avatar-lg bg-success-soft text-success rounded-3 p-3 flex-shrink-0">
                                                <i class="ri-check-line fs-32"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <span class="fs-14 text-muted fw-medium d-block mb-2">Complete
                                                    Orders</span>
                                                <h2 class="fw-bold mb-0 stat-value" data-key="completed_orders">0</h2>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>



                            <!-- Total Customers -->
                            <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 mb-3">
                                <a href="{{ route('users.index') }}" class="text-decoration-none">
                                    <div class="card border-0 shadow-sm h-100 stats-card-hover">
                                        <div class="card-body d-flex align-items-start gap-2">
                                            <div
                                                class="avatar avatar-lg bg-primary-soft text-primary rounded-3 p-3 flex-shrink-0">
                                                <i class="ri-user-3-line fs-32"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <span class="fs-14 text-muted fw-medium d-block mb-2">Total Customers</span>
                                                <h2 class="fw-bold mb-0 stat-value" data-key="total_customers">0</h2>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <!-- Active Customers -->
                            <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 mb-3">
                                <a href="{{ route('users.index') }}" class="text-decoration-none">
                                    <div class="card border-0 shadow-sm h-100 stats-card-hover">
                                        <div class="card-body d-flex align-items-start gap-2">
                                            <div
                                                class="avatar avatar-lg bg-info-soft text-info rounded-3 p-3 flex-shrink-0">
                                                <i class="ri-user-add-line fs-32"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <span class="fs-14 text-muted fw-medium d-block mb-2">Active
                                                    Customers</span>
                                                <h2 class="fw-bold mb-0 stat-value" data-key="active_customers">0</h2>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <!-- Total Products -->
                            <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 mb-3">
                                <a href="{{ route('users.index') }}" class="text-decoration-none">
                                    <div class="card border-0 shadow-sm h-100 stats-card-hover">
                                        <div class="card-body d-flex align-items-start gap-2">
                                            <div
                                                class="avatar avatar-lg bg-warning-soft text-warning rounded-3 p-3 flex-shrink-0">
                                                <i class="ri-box-3-line fs-32"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <span class="fs-14 text-muted fw-medium d-block mb-2">Total Products</span>
                                                <h2 class="fw-bold mb-0 stat-value" data-key="total_products">0</h2>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <!-- Active Products -->
                            <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 mb-3">
                                <a href="{{ route('users.index') }}" class="text-decoration-none">
                                    <div class="card border-0 shadow-sm h-100 stats-card-hover">
                                        <div class="card-body d-flex align-items-start gap-2">
                                            <div
                                                class="avatar avatar-lg bg-success-soft text-success rounded-3 p-3 flex-shrink-0">
                                                <i class="ri-money-rupee-circle-line fs-32"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <span class="fs-14 text-muted fw-medium d-block mb-2">Active
                                                    Products</span>
                                                <h2 class="fw-bold mb-0 stat-value" data-key="active_products">0</h2>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Revenue Chart & Top Countries -->
                    <div class="col-xxl-6 col-xl-12 mb-3">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-header bg-transparent border-0 pt-3 pb-0">
                                <h4 class="mb-0">Revenue Report</h4>
                            </div>
                            <div class="card-body">
                                <div id="order-status"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-3 col-xl-6 col-lg-6 col-md-6 mb-3">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-header bg-transparent border-0 pt-3 pb-0">
                                <h4 class="mb-0">Sales by Top 10 Locations</h4>
                            </div>
                            <div class="card-body">
                                <div id="salesCountriesList">
                                    <!-- Loading skeleton -->
                                    <div class="skeleton skeleton-text mb-3"></div>
                                    <div class="skeleton skeleton-text mb-3"></div>
                                    <div class="skeleton skeleton-text mb-3"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-3 col-xl-6 col-lg-6 col-md-6 mb-3">
                        <div class="card-carousel p-relative shadow-sm border-0 card">
                            <div
                                class="card-header bg-transparent border-0 pt-3 pb-0 d-flex justify-content-between align-items-center">
                                <h4 class="mb-0 text-black">Trending Product</h4>
                                <span class="badge bg-danger d-inline-flex align-items-center gap-2">
                                    🔥 <span>Hot Deal</span>
                                </span>
                            </div>
                            <div class="card-body">
                                <div class="swiper trendingProduct p-relative">
                                    <div class="swiper-wrapper" id="trendingProductsWrapper">
                                        <!-- Loading state -->
                                        <div class="swiper-slide">
                                            <div class="skeleton skeleton-title"></div>
                                            <div class="skeleton skeleton-text"></div>
                                        </div>
                                    </div>
                                    <div class="card-slide-pagination tranding">
                                        <div class="bd-pagination"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Transactions, Top Customers, Top Categories -->
                    <div class="col-xxl-4 col-xl-6 col-lg-6 mb-3">
                        <div class="card height-equal shadow-sm border-0">
                            <div class="card-header bg-transparent border-0 pt-3 pb-0">
                                <h4 class="mb-0">Recent Transactions</h4>
                            </div>
                            <div class="card-body">
                                <ul id="recentTransactionsList" class="transactions-list">
                                    <!-- Loading skeleton -->
                                    <li class="mb-3">
                                        <div class="skeleton skeleton-text"></div>
                                    </li>
                                    <li class="mb-3">
                                        <div class="skeleton skeleton-text"></div>
                                    </li>
                                    <li class="mb-3">
                                        <div class="skeleton skeleton-text"></div>
                                    </li>
                                </ul>
                                <div class="d-flex-between stats-card mt-3" id="recentTransactionsStats"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-3 col-xl-6 col-lg-6 mb-3">
                        <div class="card height-equal shadow-sm border-0">
                            <div class="card-header bg-transparent border-0 pt-3 pb-0">
                                <h4 class="mb-0">Top Customers</h4>
                            </div>
                            <div class="card-body">
                                <ul id="topCustomersList">
                                    <!-- Loading skeleton -->
                                    <li class="mb-3">
                                        <div class="skeleton skeleton-text"></div>
                                    </li>
                                    <li class="mb-3">
                                        <div class="skeleton skeleton-text"></div>
                                    </li>
                                    <li class="mb-3">
                                        <div class="skeleton skeleton-text"></div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-5 col-xl-12 mb-3">
                        <div class="card height-equal shadow-sm border-0">
                            <div class="card-header bg-transparent border-0 pt-3 pb-0">
                                <h4 class="mb-0">Top Selling Categories</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive table-wrapper-fixed">
                                    <table class="table text-nowrap" id="topCategoriesTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Category</th>
                                                <th class="text-end">Revenue</th>
                                                <th class="text-end">Orders</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Recent Orders & Best Selling Products -->
                    <div class="col-xxl-8 col-xl-12 mb-3">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-transparent border-0 pt-3 pb-0">
                                <h4 class="mb-0">Recent Orders</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive table-wrapper-fixed">
                                    <table class="table text-nowrap w-100" id="recentOrdersTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Order ID</th>
                                                <th>Customer</th>
                                                <th>Product</th>
                                                <th>Quantity</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Loading skeleton -->
                                            <tr>
                                                <td>
                                                    <div class="skeleton skeleton-text"></div>
                                                </td>
                                                <td>
                                                    <div class="skeleton skeleton-text"></div>
                                                </td>
                                                <td>
                                                    <div class="skeleton skeleton-text"></div>
                                                </td>
                                                <td>
                                                    <div class="skeleton skeleton-text"></div>
                                                </td>
                                                <td>
                                                    <div class="skeleton skeleton-text"></div>
                                                </td>
                                                <td>
                                                    <div class="skeleton skeleton-text"></div>
                                                </td>
                                                <td>
                                                    <div class="skeleton skeleton-text"></div>
                                                </td>
                                                <td>
                                                    <div class="skeleton skeleton-text"></div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-4 col-xl-12 mb-3">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-transparent border-0 pt-3 pb-0">
                                <h4 class="mb-0">Best Selling Products</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive table-wrapper-fixed">
                                    <table class="table text-nowrap" id="bestSellingProductsTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Product</th>
                                                <th>Price</th>
                                                <th>Order</th>
                                                <th>Available</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Loading skeleton -->
                                            <tr>
                                                <td>
                                                    <div class="skeleton skeleton-text"></div>
                                                </td>
                                                <td>
                                                    <div class="skeleton skeleton-text"></div>
                                                </td>
                                                <td>
                                                    <div class="skeleton skeleton-text"></div>
                                                </td>
                                                <td>
                                                    <div class="skeleton skeleton-text"></div>
                                                </td>
                                                <td>
                                                    <div class="skeleton skeleton-text"></div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/plugins/swiper.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/plugins/flatpickr.min.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('assets/admin/js/vendor/height-equal.js') }}"></script>
    <script src="{{ asset('assets/admin/js/vendor/isotope.pkgd.js') }}"></script>
    <script src="{{ asset('assets/admin/js/vendor/magnific-popup.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/plugins/smooth-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/admin/js/plugins/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/plugins/swiper.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/vendor/ecommerce-dashboard.js') }}"></script>
    <script src="{{ asset('assets/admin/js/plugins/flatpickr.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            let orderChart;
            let trendingSwiper;

            /* =========================
               DATE RANGE PICKER
            ========================== */
            (function initDateRange() {
                const today = new Date();
                today.setHours(0, 0, 0, 0); // 🔥 important

                const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);

                const fp = flatpickr("#rangeCalendar", {
                    mode: "range",
                    altInput: true,
                    altFormat: "j M Y",
                    dateFormat: "Y-m-d",
                    maxDate: today,
                    defaultDate: [firstDay, today],
                    onChange: function(selectedDates) {
                        if (selectedDates.length === 2) {
                            reloadAllDashboardData();
                        }
                    }
                });

                fp.setDate([firstDay, today], true);
            })();



            /* =========================
               GLOBAL LOADER
            ========================== */
            function showLoader() {
                $('#globalLoader').addClass('show').fadeIn(200);
            }

            function hideLoader() {
                $('#globalLoader').removeClass('show').fadeOut(200);
            }

            /* =========================
               LOAD STATS
            ========================== */
            function loadStats() {
                return $.ajax({
                    url: '/admin/dashboard/stats',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        date_range: $('#rangeCalendar').val()
                    },
                    success: function(res) {
                        console.log('Stats loaded:', res);

                        if (!res.status || !res.response) {
                            console.warn('Invalid stats response');
                            $('.stat-value').text('0');
                            return;
                        }

                        // Animate all stat values
                        $('.stat-value').each(function() {
                            const key = $(this).data('key');
                            const value = res.response[key] ?? 0;
                            animateValue($(this), 0, value, 600);
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading stats:', error);
                        $('.stat-value').text('0');
                    }
                });
            }

            /* =========================
               ANIMATE NUMBERS
            ========================== */
            function animateValue(element, start, end, duration) {
                let startTimestamp = null;
                const step = (timestamp) => {
                    if (!startTimestamp) startTimestamp = timestamp;
                    const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                    const current = Math.floor(progress * (end - start) + start);
                    element.text(current.toLocaleString());
                    if (progress < 1) {
                        window.requestAnimationFrame(step);
                    }
                };
                window.requestAnimationFrame(step);
            }

            /* =========================
               INIT CHART
            ========================== */
            function initOrderChart() {
                const options = {
                    chart: {
                        height: 352,
                        type: 'line',
                        toolbar: {
                            show: false
                        },
                        animations: {
                            enabled: true,
                            easing: 'easeinout',
                            speed: 800
                        }
                    },
                    series: [{
                        name: 'Orders',
                        type: 'area',
                        data: []
                    }, {
                        name: 'Revenue',
                        type: 'column',
                        data: []
                    }, {
                        name: 'Profit',
                        type: 'line',
                        data: []
                    }],
                    stroke: {
                        width: [2, 0, 3],
                        curve: 'smooth'
                    },
                    fill: {
                        opacity: [0.25, 1, 1],
                        type: ['gradient', 'solid', 'solid'],
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.4,
                            opacityTo: 0.1,
                            stops: [0, 90, 100]
                        }
                    },
                    labels: [],
                    markers: {
                        size: [0, 0, 4],
                        strokeWidth: 2,
                        hover: {
                            size: 6
                        }
                    },
                    xaxis: {
                        type: 'category'
                    },
                    yaxis: [{
                        title: {
                            text: 'Orders'
                        }
                    }, {
                        opposite: true,
                        title: {
                            text: 'Revenue ($)'
                        }
                    }],
                    legend: {
                        position: 'top',
                        horizontalAlign: 'left',
                        offsetY: 0
                    },
                    tooltip: {
                        shared: true,
                        intersect: false,
                        y: {
                            formatter: function(val, {
                                seriesIndex
                            }) {
                                if (seriesIndex === 0) {
                                    return (val || 0).toLocaleString() + ' Orders';
                                }
                                return '$' + (val || 0).toLocaleString(undefined, {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                            }
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    grid: {
                        borderColor: '#f1f1f1',
                        strokeDashArray: 4
                    }
                };

                orderChart = new ApexCharts(
                    document.querySelector("#order-status"),
                    options
                );

                orderChart.render();
            }

            /* =========================
               LOAD CHART DATA
            ========================== */
            function loadOrderChart() {
                return $.ajax({
                    url: '/admin/dashboard/revenue-chart',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        date_range: $('#rangeCalendar').val()
                    },
                    success: function(res) {
                        console.log('Chart data loaded:', res);

                        if (!res.status || !res.response) {
                            console.warn('Invalid chart response');
                            return;
                        }

                        // Update chart with new data
                        orderChart.updateOptions({
                            labels: res.response.labels || [],
                            xaxis: {
                                categories: res.response.labels || []
                            }
                        });

                        orderChart.updateSeries([{
                            name: 'Orders',
                            data: res.response.orders || []
                        }, {
                            name: 'Revenue',
                            data: res.response.revenue || []
                        }, {
                            name: 'Profit',
                            data: res.response.profit || []
                        }]);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading chart:', error);
                        // Reset chart to empty state
                        orderChart.updateSeries([{
                            name: 'Orders',
                            data: []
                        }, {
                            name: 'Revenue',
                            data: []
                        }, {
                            name: 'Profit',
                            data: []
                        }]);
                    }
                });
            }

            /* =========================
               LOAD TOP COUNTRIES
            ========================== */
            function loadTopCountries() {
                return $.ajax({
                    url: '/admin/dashboard/top-countries',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        date_range: $('#rangeCalendar').val()
                    },
                    success: function(res) {
                        console.log('Top countries loaded:', res);

                        if (!res.status || !res.response || !res.response.length) {
                            $('#salesCountriesList').html(
                                '<div class="text-muted text-center py-4"><i class="ri-map-pin-line fs-2 d-block mb-2"></i><p>No data available for selected period</p></div>'
                            );
                            return;
                        }

                        let html = '';
                        $.each(res.response, function(index, item) {
                            html += `
                            <div class="single-progress mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="fs-14 mb-0 fw-semibold">${item.country}</h6>
                                    <span class="progress-number text-muted fw-medium">${item.percentage}%</span>
                                </div>
                                <div class="progress" style="height: 8px; border-radius: 4px;">
                                    <div class="progress-bar ${item.color || 'bg-primary'}"
                                        role="progressbar"
                                        style="width: ${item.percentage}%"
                                        aria-valuenow="${item.percentage}"
                                        aria-valuemin="0"
                                        aria-valuemax="100">
                                    </div>
                                </div>
                                <small class="text-muted">${item.total_orders} orders</small>
                            </div>
                        `;
                        });

                        $('#salesCountriesList').html(html);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading top countries:', error);
                        $('#salesCountriesList').html(
                            '<div class="text-danger text-center py-4"><i class="ri-error-warning-line fs-2 d-block mb-2"></i><p>Error loading data</p></div>'
                        );
                    }
                });
            }

            /* =========================
                LOAD TRENDING PRODUCTS
             ========================== */
            function loadTrendingProducts() {
                return $.ajax({
                    url: '/admin/dashboard/trending-products',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        date_range: $('#rangeCalendar').val()
                    },
                    success: function(res) {
                        if (!res.status || !res.response || !res.response.length) {
                            $('#trendingProductsWrapper').html(
                                '<div class="text-muted text-center p-5">No trending products</div>'
                            );
                            return;
                        }

                        let html = '';
                        $.each(res.response, function(index, product) {
                            html += `
                                <div class="swiper-slide">
                                    <div class="card-slide-wrapper p-relative">
                                        <div class="card-slide-thumb">
                                            <img src="${product.main_image}" alt="${product.name}" style="width: 100%; height: 250px; object-fit: cover;">
                                        </div>
                                        <div class="card-slide-bottom p-3">
                                            <h5 class="text-white mb-2">
                                                <a href="${product.url}" class="text-white text-decoration-none">${product.name}</a>
                                            </h5>
                                            <div class="bd-price">
                                                <span class="current-price text-white fw-bold">${product.price}</span>
                                                ${product.old_price ? `<span class="old-price text-muted text-decoration-line-through ms-2">${product.old_price}</span>` : ''}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });

                        $('#trendingProductsWrapper').html(html);
                        initTrendingSwiper();
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading trending products:', error);
                    }
                });
            }

            /* =========================
               INIT TRENDING SWIPER
            ========================== */
            function initTrendingSwiper() {
                if (trendingSwiper) {
                    trendingSwiper.destroy(true, true);
                }

                trendingSwiper = new Swiper('.trendingProduct', {
                    slidesPerView: 1,
                    loop: true,
                    spaceBetween: 20,
                    autoplay: {
                        delay: 3500,
                        disableOnInteraction: false
                    },
                    pagination: {
                        el: '.bd-pagination',
                        clickable: true
                    }
                });
            }

            /* =========================
               LOAD BEST SELLING PRODUCTS
            ========================= */
            function loadBestSellingProducts() {
                return $.ajax({
                    url: '/admin/dashboard/best-selling-products',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        date_range: $('#rangeCalendar').val()
                    },
                    success: function(res) {
                        console.log('Best selling products loaded:', res);

                        if (!res.status || !res.response || !res.response.length) {
                            $('#bestSellingProductsTable tbody').html(
                                '<tr><td colspan="5" class="text-center text-muted py-4">No data available</td></tr>'
                            );
                            return;
                        }

                        let html = '';
                        $.each(res.response, function(index, product) {
                            let available = product.available > 0 ?
                                `<span class="badge bg-success-light text-success">${product.available} in stock</span>` :
                                '<span class="badge bg-danger-light text-danger">Out of Stock</span>';

                            html += `
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar flex-shrink-0">
                                            <img class="rounded" 
                                                 src="${product.image}" 
                                                 alt="${product.name}" 
                                                 style="width: 45px; height: 45px; object-fit: cover;"
                                                 >
                                        </div>
                                        <h6 class="text-dark fw-semibold mb-0">${product.name}</h6>
                                    </div>
                                </td>
                                <td class="text-dark fw-medium">$${product.price}</td>
                                <td class="text-muted">${product.orders}</td>
                                <td>${available}</td>
                                <td class="text-dark fw-bold">$${product.total}</td>
                            </tr>
                        `;
                        });

                        $('#bestSellingProductsTable tbody').html(html);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading best selling products:', error);
                        $('#bestSellingProductsTable tbody').html(
                            '<tr><td colspan="5" class="text-center text-danger py-4">Error loading data</td></tr>'
                        );
                    }
                });
            }

            /* =========================
               LOAD RECENT TRANSACTIONS
            ========================= */
            function loadRecentTransactions() {
                return $.ajax({
                    url: '/admin/dashboard/recent-transactions',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        date_range: $('#rangeCalendar').val()
                    },
                    success: function(res) {
                        console.log('Recent transactions loaded:', res);

                        if (!res.status || !res.response || !res.response.length) {
                            $('#recentTransactionsList').html(
                                '<li class="text-muted text-center py-4">No transactions found</li>'
                            );
                            return;
                        }

                        let html = '';
                        $.each(res.response, function(index, tx) {
                            let srNo = index + 1; // ✅ serial number

                            html += `
                        <li class="d-flex justify-content-between align-items-start mb-3 pb-3 border-bottom">
                        
                                <div>
                                    <h6 class="fs-14 mb-1 fw-semibold">${tx.title}</h6>
                                    <div class="d-flex gap-2 align-items-center">
                                        <span class="text-muted">${tx.method}</span>
                                        ${tx.date ? `<span class="text-muted">• ${tx.date}</span>` : ''}
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <h6 class="${tx.amount_class || 'text-dark'} mb-1 fw-bold">${tx.amount}</h6>
                                <span class="badge ${tx.status_class || 'bg-secondary'} badge-sm">${tx.status}</span>
                            </div>
                        </li>`;
                        });

                        $('#recentTransactionsList').html(html);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading transactions:', error);
                        $('#recentTransactionsList').html(
                            '<li class="text-danger text-center py-4">Error loading transactions</li>'
                        );
                    }
                });
            }


            /* =========================
               LOAD TOP CUSTOMERS
            ========================= */
            function loadTopCustomers() {
                return $.ajax({
                    url: '/admin/dashboard/top-customers',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        date_range: $('#rangeCalendar').val()
                    },
                    success: function(res) {

                        if (!res.status || !res.response || !res.response.length) {
                            $('#topCustomersList').html(
                                '<li class="text-muted text-center py-4">No customers found</li>'
                            );
                            return;
                        }

                        let html = '';
                        $.each(res.response, function(i, customer) {

                            let avatar = customer.avatar ?
                                `<img src="${customer.avatar}"
                           class="rounded-circle border"
                           width="44" height="44"
                           alt="${customer.name}"
                           >` :
                                `<div class="rounded-circle bg-primary text-white fw-semibold d-flex align-items-center justify-content-center"
                           style="width:44px;height:44px;">
                           ${customer.initials}
                       </div>`;

                            html += `
                <li class="d-flex justify-content-between align-items-center py-3 border-bottom">
                    
                    <div class="d-flex align-items-center gap-3">
                        ${avatar}
                        <div>
                            <div class="fw-semibold text-dark">
                                ${customer.name}
                            </div>
                            ${customer.email ? `<div class="text-muted">${customer.email}</div>` : ''}
                        </div>
                    </div>

                    <div class="text-end">
                        <div class="fw-bold text-dark">
                            ${customer.orders}
                        </div>
                        <div class="text-muted">
                            Order${customer.orders > 1 ? 's' : ''}
                        </div>
                        ${customer.total_spent ? `<div class="text-success small fw-semibold">${customer.total_spent}</div>` : ''}
                    </div>

                </li>`;
                        });

                        $('#topCustomersList').html(html);
                    },
                    error: function() {
                        $('#topCustomersList').html(
                            '<li class="text-danger text-center py-4">Error loading customers</li>'
                        );
                    }
                });
            }


            /* =========================
               LOAD TOP CATEGORIES
            ========================= */
            function loadTopCategories() {
                return $.ajax({
                    url: '/admin/dashboard/top-categories',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        date_range: $('#rangeCalendar').val()
                    },
                    success: function(res) {
                        console.log('Top categories loaded:', res);

                        if (!res.status || !res.response || !res.response.length) {
                            $('#topCategoriesTable tbody').html(
                                '<tr><td colspan="3" class="text-center text-muted py-4">No categories found</td></tr>'
                            );
                            return;
                        }

                        let html = '';
                        $.each(res.response, function(i, category) {
                            html += `
                            <tr>
                                <td>
                                    <h6 class="mb-1 fs-14 fw-semibold">${category.name}</h6>
                                    ${category.slug ? `<span class="text-muted">${category.slug}</span>` : ''}
                                </td>
                                <td class="text-end fw-bold text-success">$${category.revenue}</td>
                                <td class="text-end text-muted">${category.orders}</td>
                            </tr>
                        `;
                        });

                        $('#topCategoriesTable tbody').html(html);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading top categories:', error);
                        $('#topCategoriesTable tbody').html(
                            '<tr><td colspan="3" class="text-center text-danger py-4">Failed to load categories</td></tr>'
                        );
                    }
                });
            }

            /* =========================
               LOAD RECENT ORDERS
            ========================= */
            function loadRecentOrders() {
                return $.ajax({
                    url: '/admin/dashboard/recent-orders',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        date_range: $('#rangeCalendar').val()
                    },
                    success: function(res) {
                        console.log('Recent orders loaded:', res);

                        if (!res.status || !res.response || !res.response.length) {
                            $('#recentOrdersTable tbody').html(
                                '<tr><td colspan="6" class="text-center text-muted py-4">No orders found</td></tr>'
                            );
                            return;
                        }

                        let html = '';
                        $.each(res.response, function(i, order) {
                            html += `
                            <tr>
                                <td>
                                    <a href="/admin/orders/${order.id}" class="text-primary fw-bold">${order.order_id}</a>
                                    ${order.date ? `<div class="text-muted">${order.date}</div>` : ''}
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                       
                                        <div>
                                            <h6 class="mb-0 fs-14 fw-semibold">${order.customer_name}</h6>
                                            <span class="fs-12 text-muted">${order.customer_email}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                    
                                        <span class="fw-medium text-dark">${order.product_name}</span>
                                    </div>
                                </td>
                                <td class="text-muted">${order.quantity}</td>
                                <td class="fw-bold text-dark">$${order.amount}</td>
                                 <td>
                                    <span class="badge ${order.status_class || 'bg-secondary'}">${order.status}</span>
                                </td>
                            </tr>
                        `;
                        });

                        $('#recentOrdersTable tbody').html(html);

                        // Reinitialize tooltips if Bootstrap is available
                        if (typeof bootstrap !== 'undefined') {
                            const tooltipTriggerList = document.querySelectorAll(
                                '[data-bs-toggle="tooltip"]');
                            tooltipTriggerList.forEach(el => new bootstrap.Tooltip(el));
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading recent orders:', error);
                        $('#recentOrdersTable tbody').html(
                            '<tr><td colspan="6" class="text-center text-danger py-4">Error loading orders</td></tr>'
                        );
                    }
                });
            }

            /* =========================
               RELOAD ALL DATA
            ========================== */
            function reloadAllDashboardData() {
                console.log('=== Starting Dashboard Reload ===');
                console.log('Date Range:', $('#rangeCalendar').val());

                showLoader();
                resetToLoadingState();

                // Load all data concurrently
                $.when(
                    loadStats(),
                    loadOrderChart(),
                    loadTopCountries(),
                    loadTrendingProducts(),
                    loadBestSellingProducts(),
                    loadRecentTransactions(),
                    loadTopCustomers(),
                    loadTopCategories(),
                    loadRecentOrders()
                ).done(function() {
                    console.log('=== All Dashboard Data Loaded Successfully ===');
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    console.error('=== Dashboard Data Loading Failed ===');
                    console.error('Status:', textStatus);
                    console.error('Error:', errorThrown);
                    showErrorNotification('Failed to load some dashboard data. Please refresh the page.');
                }).always(function() {
                    hideLoader();
                    console.log('=== Dashboard Reload Complete ===');
                });
            }

            /* =========================
               RESET TO LOADING STATE
            ========================== */
            function resetToLoadingState() {
                // Create skeleton loader HTML
                const skeletonText =
                    '<div class="skeleton-loader" style="height: 20px; background: #e0e0e0; border-radius: 4px; animation: pulse 1.5s ease-in-out infinite;"></div>';

                // Stats cards
                $('.stat-value').html('<span class="text-muted">...</span>');

                // Lists and tables
                $('#salesCountriesList').html(
                    `<div class="py-3">${skeletonText}</div><div class="py-3">${skeletonText}</div><div class="py-3">${skeletonText}</div>`
                );

                $('#trendingProductsWrapper').html(
                    '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>'
                );

                $('#recentTransactionsList').html(
                    `<li class="mb-3">${skeletonText}</li><li class="mb-3">${skeletonText}</li>`
                );

                $('#topCustomersList').html(
                    `<li class="mb-3">${skeletonText}</li><li class="mb-3">${skeletonText}</li>`
                );

                $('#topCategoriesTable tbody').html(
                    `<tr><td>${skeletonText}</td><td>${skeletonText}</td><td>${skeletonText}</td></tr>`
                );

                $('#bestSellingProductsTable tbody').html(
                    `<tr><td>${skeletonText}</td><td>${skeletonText}</td><td>${skeletonText}</td><td>${skeletonText}</td><td>${skeletonText}</td></tr>`
                );

                $('#recentOrdersTable tbody').html(
                    `<tr><td>${skeletonText}</td><td>${skeletonText}</td><td>${skeletonText}</td><td>${skeletonText}</td><td>${skeletonText}</td><td>${skeletonText}</td></tr>`
                );
            }

            /* =========================
               ERROR NOTIFICATION
            ========================== */
            function showErrorNotification(message = 'Failed to load dashboard data. Please try again.') {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: message,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 4000,
                        timerProgressBar: true
                    });
                } else if (typeof toastr !== 'undefined') {
                    toastr.error(message, 'Error');
                } else {
                    alert(message);
                }
            }

            /* =========================
               ADD PULSE ANIMATION STYLE
            ========================== */
            $('<style>')
                .prop('type', 'text/css')
                .html(`
                @keyframes pulse {
                    0%, 100% { opacity: 1; }
                    50% { opacity: 0.5; }
                }
                .skeleton-loader {
                    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
                    background-size: 200% 100%;
                    animation: loading 1.5s ease-in-out infinite;
                }
                @keyframes loading {
                    0% { background-position: 200% 0; }
                    100% { background-position: -200% 0; }
                }
            `)
                .appendTo('head');

            /* =========================
               INITIAL LOAD
            ========================== */
            console.log('Initializing dashboard...');
            initOrderChart();

            // Small delay to ensure DOM is fully ready
            setTimeout(function() {
                reloadAllDashboardData();
            }, 100);

            // Add export/refresh button handlers if they exist
            $('#refreshDashboard').on('click', function(e) {
                e.preventDefault();
                reloadAllDashboardData();
            });
        });
    </script>
@endpush
