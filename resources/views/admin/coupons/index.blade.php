@extends('layouts.admin')

@section('content')
    <div class="app-content-area">
        <div class="app-content-wrap">
            <div class="container-fluid">
                <!-- Page Header -->
                <div class="row mb-3">
                    <div class="col-xl-12">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h1 class="fs-18 mb-0">{{ $title ?? 'Coupons Management' }}</h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb breadcrumb-example1 mb-0">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.dashboard') }}">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active">Coupons</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- Alert Container -->
                <div id="alertContainer"></div>

                <!-- Filters Card -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-3 p-md-4">
                                <div class="row g-3 align-items-end">

                                    <!-- Date Range Filter -->
                                    @can('coupons.view.any')
                                        <div class="col-lg-2 col-md-6">
                                            <label for="rangeCalendar" class="form-label fw-semibold mb-2">
                                                <i class="ri-calendar-line me-1"></i>Date Range
                                            </label>
                                            <input id="rangeCalendar" type="text" class="form-control"
                                                placeholder="Select date range" readonly>
                                        </div>
                                    @endcan

                                    <!-- Status Filter -->
                                    @can('coupons.view.any')
                                        <div class="col-lg-2 col-md-6">
                                            <label for="filterStatus" class="form-label fw-semibold mb-2">
                                                <i class="ri-filter-line me-1"></i>Status
                                            </label>
                                            <select id="filterStatus" class="form-select" name="status">
                                                <option value="">All Status</option>
                                                <option value="1">Active</option>
                                                <option value="0">Inactive</option>
                                            </select>
                                        </div>
                                    @endcan

                                    <!-- Type Filter -->
                                    @can('coupons.view.any')
                                        <div class="col-lg-2 col-md-6">
                                            <label for="filterType" class="form-label fw-semibold mb-2">
                                                <i class="ri-coupon-line me-1"></i>Type
                                            </label>
                                            <select id="filterType" class="form-select" name="type">
                                                <option value="">All Types</option>
                                                <option value="percentage">Percentage</option>
                                                <option value="fixed_amount">Fixed Amount</option>
                                            </select>
                                        </div>
                                    @endcan

                                    <!-- Usage Status Filter -->
                                    @can('coupons.view.any')
                                        <div class="col-lg-2 col-md-6">
                                            <label for="filterUsage" class="form-label fw-semibold mb-2">
                                                <i class="ri-user-line me-1"></i>Usage Status
                                            </label>
                                            <select id="filterUsage" class="form-select" name="usage_status">
                                                <option value="">All</option>
                                                <option value="available">Available</option>
                                                <option value="fully_used">Fully Used</option>
                                                <option value="expired">Expired</option>
                                            </select>
                                        </div>
                                    @endcan

                                    <!-- Action Buttons - Your specified layout -->
                                    <div class="col-lg-4 col-md-8">
                                        <div class="d-flex gap-2 flex-wrap">
                                            <button id="filterSearchBtn" type="button" class="btn btn-success px-4">
                                                <i class="ri-search-line me-1"></i> Search
                                            </button>
                                            <button id="resetFilterBtn" type="button"
                                                class="btn btn-outline-secondary px-4">
                                                <i class="ri-refresh-line me-1"></i> Reset
                                            </button>
                                            @can('coupons.create')
                                                <a href="{{ route('coupons.create') }}" class="btn btn-primary px-4 modal_open">
                                                    <i class="ri-add-line me-1"></i> Add Coupon
                                                </a>
                                            @endcan
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Coupons Table -->
                @can('coupons.view.any')
                    <div class="row">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <!-- Table Header Controls -->
                                <div class="card-header bg-white border-bottom py-3">
                                    <div class="row align-items-center g-3">
                                        <div class="col-md-6">
                                            <div id="dataTable_length_wrapper"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <div id="dataTable_filter_wrapper"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Table Body -->
                                <div class="card-body p-0">
                                    <div class="table-responsive position-relative">
                                        <table id="dataTable" class="table table-bordered table-hover align-middle w-100">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="px-3 py-3 fw-semibold">#</th>
                                                    <th class="px-3 py-3 fw-semibold">Code</th>
                                                    <th class="px-3 py-3 fw-semibold">Name</th>
                                                    <th class="px-3 py-3 fw-semibold text-center">Discount</th>
                                                    <th class="px-3 py-3 fw-semibold text-center">Usage</th>
                                                    <th class="px-3 py-3 fw-semibold text-center">Status</th>
                                                    <th class="px-3 py-3 fw-semibold">Validity</th>
                                                    <th class="px-3 py-3 fw-semibold">Updated</th>
                                                    <th class="px-3 py-3 fw-semibold text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- DataTable will populate rows here -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Table Footer Controls -->
                                <div class="card-footer bg-white border-top py-3">
                                    <div class="row align-items-center g-3">
                                        <div class="col-md-6">
                                            <div id="dataTable_info_wrapper"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <div id="dataTable_paginate_wrapper"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- No Permission Alert -->
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-warning border-0 shadow-sm" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="ri-alert-line fs-18 me-2"></i>
                                    <div>
                                        <strong>Access Restricted!</strong> You don't have permission to view coupons.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan

            </div>
        </div>
    </div>
@endsection

@push('style')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/plugins/dataTables.dataTables.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/plugins/jquery-confirm.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/plugins/flatpickr.min.css') }}">
    <style>
        .statistics-card {
            transition: all 0.3s ease;
        }

        .statistics-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .coupon-code {
            font-family: 'Courier New', monospace;
            background: #f8f9fa;
            padding: 2px 8px;
            border-radius: 4px;
            border: 1px dashed #dee2e6;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('assets/admin/js/plugins/dataTables.js') }}"></script>
    <script src="{{ asset('assets/admin/js/plugins/jquery-confirm.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/plugins/flatpickr.min.js') }}"></script>

    <script>
        (function($) {
            "use strict";

            let table = null;
            let calendar = null;
            let isInitialLoad = true;
            let isFilterChange = false;

            const $searchBtn = $('#filterSearchBtn');
            const $resetBtn = $('#resetFilterBtn');
            const $dataTableWrapper = $('.table-responsive');
            const $alertContainer = $('#alertContainer');

            // Loading overlay template
            const LOADING_OVERLAY = `
                <div class="loading-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-white bg-opacity-75" style="z-index: 10; min-height: 300px;">
                    <div class="text-center">
                        <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted fw-semibold mb-0">Loading coupons...</p>
                        <small class="text-muted">Please wait</small>
                    </div>
                </div>
            `;

            /* ------------------------------------
             * Alert System
             * ------------------------------------ */
            function showAlert(message, type = 'success', icon = 'checkbox-circle-line', duration = 4000) {
                const alertId = 'alert-' + Date.now();
                const alert = `
                    <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show mb-3" role="alert">
                        <i class="ri-${icon} me-2"></i>
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;

                $alertContainer.append(alert);

                if (duration > 0) {
                    setTimeout(function() {
                        $('#' + alertId).fadeOut(300, function() {
                            $(this).remove();
                        });
                    }, duration);
                }
            }

            function clearAlerts() {
                $alertContainer.empty();
            }

            /* ------------------------------------
             * Initialize Flatpickr
             * ------------------------------------ */
            function initDateRangePicker() {
                calendar = flatpickr("#rangeCalendar", {
                    mode: "range",
                    altInput: true,
                    altFormat: "j M Y",
                    dateFormat: "Y-m-d",
                    maxDate: "today",
                    showMonths: window.innerWidth >= 768 ? 2 : 1,
                    locale: {
                        firstDayOfWeek: 1
                    },
                    onClose: function(selectedDates, dateStr, instance) {
                        if (selectedDates.length === 2) {
                            isFilterChange = true;
                            $searchBtn.click();
                        }
                    }
                });
            }

            /* ------------------------------------
             * Show Loading Overlay
             * ------------------------------------ */
            function showLoadingOverlay() {
                // Disable filter inputs
                $('#rangeCalendar, #filterStatus, #filterType, #filterUsage').prop('disabled', true);

                // Update search button
                $searchBtn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Searching...'
                );

                // Update reset button
                $resetBtn.prop('disabled', true).addClass('disabled');

                // Add loading overlay to table
                if ($dataTableWrapper.find('.loading-overlay').length === 0) {
                    $dataTableWrapper.append(LOADING_OVERLAY);
                }
            }

            /* ------------------------------------
             * Hide Loading Overlay
             * ------------------------------------ */
            function hideLoadingOverlay() {
                // Enable filter inputs
                $('#rangeCalendar, #filterStatus, #filterType, #filterUsage').prop('disabled', false);

                // Reset search button
                $searchBtn.prop('disabled', false).html('<i class="ri-search-line me-1"></i> Search');

                // Reset reset button
                $resetBtn.prop('disabled', false).removeClass('disabled');

                // Remove loading overlay
                $dataTableWrapper.find('.loading-overlay').fadeOut(200, function() {
                    $(this).remove();
                });
            }

            /* ------------------------------------
             * Smooth Scroll to Table
             * ------------------------------------ */
            function scrollToTable() {
                if (isFilterChange && !isInitialLoad) {
                    setTimeout(function() {
                        $('html, body').animate({
                            scrollTop: $("#dataTable").offset().top - 120
                        }, 400);
                    }, 100);
                }
                isFilterChange = false;
            }





            /* ------------------------------------
             * Initialize DataTable
             * ------------------------------------ */
            function initDataTable() {
                if ($.fn.DataTable.isDataTable('#dataTable')) {
                    $('#dataTable').DataTable().clear().destroy();
                }

                table = $('#dataTable')
                    .on('preXhr.dt', function(e, settings, data) {
                        showLoadingOverlay();
                        clearAlerts();
                    })
                    .on('xhr.dt', function(e, settings, json, xhr) {
                        hideLoadingOverlay();
                        // Update statistics from response
                        if (json && json.statistics) {
                            updateStatistics(json.statistics);
                        }
                    })
                    .DataTable({
                        processing: false,
                        serverSide: true,
                        responsive: true,
                        autoWidth: false,
                        pagingType: "full_numbers",
                        pageLength: 25,
                        lengthMenu: [
                            [10, 25, 50, 100],
                            [10, 25, 50, 100]
                        ],

                        ajax: {
                            url: "{{ route('coupons.index') }}",
                            data: function(d) {
                                d.status = $('#filterStatus').val();
                                d.type = $('#filterType').val();
                                d.usage_status = $('#filterUsage').val();
                                d.date_range = $('#rangeCalendar').val();
                                d._token = "{{ csrf_token() }}";
                            },
                            error: function(xhr, error, thrown) {
                                hideLoadingOverlay();
                                console.error('DataTable error:', error);

                                showAlert(
                                    '<strong>Error!</strong> Unable to load coupons. Please check your connection and try again.',
                                    'danger',
                                    'error-warning-line',
                                    6000
                                );
                            }
                        },

                        columns: [{
                                data: 'id',
                                className: 'px-3 py-2 fw-semibold text-primary',
                                width: '60px'
                            },
                            {
                                data: 'code',
                                className: 'px-3 py-2 fw-medium',
                                render: function(data) {
                                    return `<span class="coupon-code">${data}</span>`;
                                }
                            },
                            {
                                data: 'name',
                                className: 'px-3 py-2'
                            },

                            {
                                data: 'discount',
                                className: 'px-3 py-2 text-center fw-semibold'
                            },
                            {
                                data: 'usage',
                                className: 'px-3 py-2 text-center',
                                orderable: false
                            },
                            {
                                data: 'status',
                                className: 'px-3 py-2 text-center'
                            },
                            {
                                data: 'validity',
                                className: 'px-3 py-2 text-muted small'
                            },
                            {
                                data: 'updated_at',
                                className: 'px-3 py-2 text-muted small'
                            },
                            {
                                data: 'action',
                                orderable: false,
                                searchable: false,
                                className: 'px-3 py-2 text-center'
                            }
                        ],

                        order: [
                            [0, 'desc']
                        ],

                        language: {
                            search: "",
                            searchPlaceholder: "Search coupon code, name...",
                            lengthMenu: "_MENU_ per page",
                            zeroRecords: `
                                <div class="text-center py-5 my-5">
                                    <i class="ri-coupon-line display-1 text-muted mb-3 d-block"></i>
                                    <h5 class="text-muted mb-2">No Coupons Found</h5>
                                    <p class="text-muted mb-0">Try adjusting your filters or search criteria</p>
                                </div>
                            `,
                            info: "Showing _START_ to _END_ of _TOTAL_ coupons",
                            infoEmpty: "No coupons to display",
                            infoFiltered: "(filtered from _MAX_ total)",
                            paginate: {
                                first: '<i class="ri-skip-back-mini-line"></i>',
                                last: '<i class="ri-skip-forward-mini-line"></i>',
                                next: '<i class="ri-arrow-right-s-line"></i>',
                                previous: '<i class="ri-arrow-left-s-line"></i>'
                            },
                            loadingRecords: "Loading...",
                            processing: "Processing..."
                        },

                        initComplete: function() {
                            // Move DataTable controls to custom containers
                            const $wrapper = $('#dataTable_wrapper');

                            // Move length menu to header
                            const $lengthMenu = $wrapper.find('.dataTables_length');
                            $lengthMenu.appendTo('#dataTable_length_wrapper');

                            // Move search box to header and style it
                            const $filter = $wrapper.find('.dataTables_filter');
                            $filter.appendTo('#dataTable_filter_wrapper');
                            $filter.addClass('text-end');
                            $filter.find('input').addClass('form-control form-control-sm').css('display',
                                'inline-block');

                            // Move info to footer
                            const $info = $wrapper.find('.dataTables_info');
                            $info.appendTo('#dataTable_info_wrapper');

                            // Move pagination to footer
                            const $paginate = $wrapper.find('.dataTables_paginate');
                            $paginate.appendTo('#dataTable_paginate_wrapper');
                            $paginate.addClass('d-flex justify-content-end');
                        },

                        drawCallback: function(settings) {
                            // Initialize tooltips for action buttons
                            $('[data-bs-toggle="tooltip"]').tooltip();

                            const api = this.api();
                            const recordsTotal = api.page.info().recordsTotal;

                            // Show success message only on initial load or filter change
                            if ((isInitialLoad || isFilterChange) && recordsTotal > 0) {
                                showAlert(
                                    `<strong>Success!</strong> Loaded ${recordsTotal} coupon(s) successfully.`,
                                    'success',
                                    'checkbox-circle-line',
                                    3000
                                );
                                isInitialLoad = false;
                            }

                            // Scroll to table on filter change
                            scrollToTable();
                        }
                    });
            }

            /* ------------------------------------
             * Bind Filter Events
             * ------------------------------------ */
            function bindFilterEvents() {
                // Search button
                $searchBtn.on('click', function() {
                    isFilterChange = true;
                    table.draw();
                    loadStatistics();
                });

                // Reset button
                $resetBtn.on('click', function() {
                    const $this = $(this);

                    // Clear filters
                    if (calendar) {
                        calendar.clear();
                    }
                    $('#filterStatus').val('');
                    $('#filterType').val('');
                    $('#filterUsage').val('');

                    // Visual feedback
                    $this.prop('disabled', true).html(
                        '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Resetting...'
                    );

                    isFilterChange = true;

                    setTimeout(function() {
                        table.draw();
                        loadStatistics();
                        $this.prop('disabled', false).html(
                            '<i class="ri-refresh-line me-1"></i> Reset');

                        showAlert(
                            '<strong>Filters Reset!</strong> Showing all coupons.',
                            'info',
                            'information-line',
                            2500
                        );
                    }, 300);
                });

                // Auto-search on filter changes
                $('#filterStatus, #filterType, #filterUsage').on('change', function() {
                    isFilterChange = true;
                    $searchBtn.click();
                });

                // Enter key to search
                $('#filterStatus, #filterType, #filterUsage').on('keypress', function(e) {
                    if (e.which === 13) {
                        $searchBtn.click();
                    }
                });
            }

            /* ------------------------------------
             * Initialize Tooltips
             * ------------------------------------ */
            function initTooltips() {
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl, {
                        trigger: 'hover'
                    });
                });
            }

            /* ------------------------------------
             * Delete Coupon Handler
             * ------------------------------------ */
            function initDeleteHandlers() {
                $(document).on('click', '.delete-coupon', function(e) {
                    e.preventDefault();
                    const $this = $(this);
                    const url = $this.data('url');
                    const couponCode = $this.data('code');
                    const couponName = $this.data('name');
                    const usageCount = $this.data('usage') || 0;

                    let warningMessage =
                        `Are you sure you want to delete coupon <strong>"${couponCode}"</strong>?`;

                    if (usageCount > 0) {
                        warningMessage += `<br><span class="text-danger">
                            <i class="ri-alert-line me-1"></i>
                            Warning: This coupon has been used ${usageCount} time(s). 
                            Deleting it may affect order history.
                        </span>`;
                    }

                    warningMessage += `<br><small class="text-muted">This action cannot be undone.</small>`;

                    $.confirm({
                        title: 'Confirm Delete',
                        content: warningMessage,
                        icon: 'ri-delete-bin-6-line',
                        type: 'red',
                        buttons: {
                            confirm: {
                                text: 'Delete',
                                btnClass: 'btn-danger',
                                action: function() {
                                    $.ajax({
                                        url: url,
                                        type: 'DELETE',
                                        data: {
                                            _token: "{{ csrf_token() }}"
                                        },
                                        beforeSend: function() {
                                            $this.prop('disabled', true).html(
                                                '<span class="spinner-border spinner-border-sm"></span> Deleting...'
                                            );
                                        },
                                        success: function(response) {
                                            showAlert(
                                                `<strong>Success!</strong> Coupon "${couponCode}" deleted successfully.`,
                                                'success',
                                                'checkbox-circle-line',
                                                4000
                                            );
                                            table.draw();
                                            loadStatistics();
                                        },
                                        error: function(xhr) {
                                            showAlert(
                                                `<strong>Error!</strong> ${xhr.responseJSON?.message || 'Unable to delete coupon.'}`,
                                                'danger',
                                                'error-warning-line',
                                                5000
                                            );
                                            $this.prop('disabled', false).html(
                                                '<i class="ri-delete-bin-6-line"></i>'
                                            );
                                        }
                                    });
                                }
                            },
                            cancel: {
                                text: 'Cancel',
                                btnClass: 'btn-secondary'
                            }
                        }
                    });
                });
            }

            /* ------------------------------------
             * Status Update Handler
             * ------------------------------------ */
            function initStatusHandlers() {
                $(document).on('click', '.change-coupon-status', function(e) {
                    e.preventDefault();
                    const $this = $(this);
                    const url = $this.data('url');
                    const couponCode = $this.data('code');
                    const currentStatus = $this.data('status');
                    const isActive = currentStatus === '1';
                    const action = isActive ? 'deactivate' : 'activate';

                    let warningMessage =
                        `Are you sure you want to ${action} coupon <strong>"${couponCode}"</strong>?`;

                    if (isActive) {
                        warningMessage += `<br><span class="text-warning">
                            <i class="ri-alert-line me-1"></i>
                            Warning: Deactivating this coupon will make it unavailable for future orders.
                        </span>`;
                    }

                    warningMessage +=
                        `<br><small class="text-muted">This will affect coupon availability for customers.</small>`;

                    $.confirm({
                        title: `Confirm ${action.charAt(0).toUpperCase() + action.slice(1)}`,
                        content: warningMessage,
                        icon: isActive ? 'ri-stop-circle-line' : 'ri-play-circle-line',
                        type: isActive ? 'orange' : 'blue',
                        buttons: {
                            confirm: {
                                text: isActive ? 'Deactivate' : 'Activate',
                                btnClass: isActive ? 'btn-warning' : 'btn-primary',
                                action: function() {
                                    $.ajax({
                                        url: url,
                                        type: 'POST',
                                        data: {
                                            _token: "{{ csrf_token() }}",
                                            _method: 'PUT'
                                        },
                                        beforeSend: function() {
                                            $this.prop('disabled', true).html(
                                                '<span class="spinner-border spinner-border-sm"></span> Processing...'
                                            );
                                        },
                                        success: function(response) {
                                            const message = isActive ?
                                                `Coupon "${couponCode}" has been deactivated.` :
                                                `Coupon "${couponCode}" has been activated successfully.`;

                                            showAlert(
                                                `<strong>Success!</strong> ${message}`,
                                                'success',
                                                'checkbox-circle-line',
                                                4000
                                            );
                                            table.draw();
                                            loadStatistics();
                                        },
                                        error: function(xhr) {
                                            showAlert(
                                                `<strong>Error!</strong> ${xhr.responseJSON?.message || `Unable to ${action} coupon.`}`,
                                                'danger',
                                                'error-warning-line',
                                                5000
                                            );
                                            $this.prop('disabled', false);
                                        }
                                    });
                                }
                            },
                            cancel: {
                                text: 'Cancel',
                                btnClass: 'btn-secondary'
                            }
                        }
                    });
                });
            }

            /* ------------------------------------
             * Initialize Everything
             * ------------------------------------ */
            $(document).ready(function() {
                @can('coupons.view.any')
                    initDateRangePicker();
                    initDataTable();
                    bindFilterEvents();
                    initTooltips();
                    initDeleteHandlers();
                    initStatusHandlers();
                    loadStatistics();

                    // Show welcome message on initial load
                    setTimeout(function() {
                        if (isInitialLoad) {
                            showAlert(
                                '<strong>Welcome!</strong> Coupon management system loaded successfully.',
                                'info',
                                'information-line',
                                3000
                            );
                        }
                    }, 500);
                @endcan
            });

        })(jQuery);
    </script>
@endpush
