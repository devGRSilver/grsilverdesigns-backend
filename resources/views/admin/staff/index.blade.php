@extends('layouts.admin')

@section('content')
    <div class="app-content-area">
        <div class="app-content-wrap">
            <div class="container-fluid">
                <!-- Page Header -->
                <div class="row mb-3">
                    <div class="col-xl-12">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h1 class="fs-18 mb-0">{{ $title ?? 'Staff Management' }}</h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb breadcrumb-example1 mb-0">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.dashboard') }}">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active">Staff</li>
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

                                    <!-- Role Filter -->
                                    @if ($roles && $roles->count())
                                        <div class="col-lg-4 col-md-6">
                                            <label for="filterRole" class="form-label fw-semibold mb-2">
                                                <i class="ri-user-shared-line me-1"></i>Role
                                            </label>
                                            <select id="filterRole" class="form-select select2" name="role_id">
                                                <option value="">All Roles</option>
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif

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


                                            <button type="button" class="btn btn-primary px-4 modal_open"
                                                href="{{ route('staff.create') }}">
                                                <i class="ri-add-line me-1"></i> Add Staff
                                            </button>



                                        </div>
                                    </div>



                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Staff Table -->
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
                                                <th class="px-3 py-3 fw-semibold">Name</th>
                                                <th class="px-3 py-3 fw-semibold">Mobile</th>
                                                <th class="px-3 py-3 fw-semibold">Email</th>
                                                <th class="px-3 py-3 fw-semibold text-center">Status</th>
                                                <th class="px-3 py-3 fw-semibold">Role</th>
                                                <th class="px-3 py-3 fw-semibold">Created</th>
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

            </div>
        </div>
    </div>
@endsection

@push('style')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/plugins/dataTables.dataTables.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/plugins/jquery-confirm.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/plugins/select2.min.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('assets/admin/js/plugins/dataTables.js') }}"></script>
    <script src="{{ asset('assets/admin/js/plugins/jquery-confirm.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/plugins/select2.min.js') }}"></script>

    <script>
        (function($) {
            "use strict";

            let table = null;
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
                        <p class="text-muted fw-semibold mb-0">Loading staff members...</p>
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
             * Initialize Select2
             * ------------------------------------ */
            function initSelect2() {
                if ($.fn.select2) {
                    $('#filterRole').select2({
                        width: "100%",
                        placeholder: "Select Role",
                        allowClear: true,
                        theme: "classic"
                    });

                    // Trigger search when select2 changes
                    $('#filterRole').on('change', function() {
                        isFilterChange = true;
                        $searchBtn.click();
                    });
                }
            }

            /* ------------------------------------
             * Show Loading Overlay
             * ------------------------------------ */
            function showLoadingOverlay() {
                // Disable filter inputs
                $('#filterRole').prop('disabled', true);
                $('.select2').prop('disabled', true);

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
                $('#filterRole').prop('disabled', false);
                $('.select2').prop('disabled', false);

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
                            url: "{{ route('staff.index') }}",
                            data: function(d) {
                                d.role_id = $('#filterRole').val();
                                d._token = "{{ csrf_token() }}";
                            },
                            error: function(xhr, error, thrown) {
                                hideLoadingOverlay();
                                console.error('DataTable error:', error);

                                showAlert(
                                    '<strong>Error!</strong> Unable to load staff members. Please check your connection and try again.',
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
                                data: 'name',
                                className: 'px-3 py-2 fw-medium'
                            },
                            {
                                data: 'phone',
                                className: 'px-3 py-2'
                            },
                            {
                                data: 'email',
                                className: 'px-3 py-2 text-muted'
                            },
                            {
                                data: 'status',
                                className: 'px-3 py-2 text-center'
                            },
                            {
                                data: 'role',
                                className: 'px-3 py-2'
                            },
                            {
                                data: 'created_at',
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
                            searchPlaceholder: "Search staff...",
                            lengthMenu: "_MENU_ per page",
                            zeroRecords: `
                                <div class="text-center py-5 my-5">
                                    <i class="ri-user-search-line display-1 text-muted mb-3 d-block"></i>
                                    <h5 class="text-muted mb-2">No Staff Members Found</h5>
                                    <p class="text-muted mb-0">Try adjusting your filters or search criteria</p>
                                </div>
                            `,
                            info: "Showing _START_ to _END_ of _TOTAL_ staff members",
                            infoEmpty: "No staff members to display",
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
                                    `<strong>Success!</strong> Loaded ${recordsTotal} staff member(s) successfully.`,
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
                });

                // Reset button
                $resetBtn.on('click', function() {
                    const $this = $(this);

                    // Clear filters
                    $('#filterRole').val('').trigger('change');

                    // Visual feedback
                    $this.prop('disabled', true).html(
                        '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Resetting...'
                    );

                    isFilterChange = true;

                    setTimeout(function() {
                        table.draw();
                        $this.prop('disabled', false).html(
                            '<i class="ri-refresh-line me-1"></i> Reset');

                        showAlert(
                            '<strong>Filters Reset!</strong> Showing all staff members.',
                            'info',
                            'information-line',
                            2500
                        );
                    }, 300);
                });

                // Enter key to search in filter inputs
                $('#filterRole').on('keypress', function(e) {
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
             * Initialize Everything
             * ------------------------------------ */
            $(document).ready(function() {
                initSelect2();
                initDataTable();
                bindFilterEvents();
                initTooltips();
                // initDeleteHandlers();

                // Show welcome message on initial load
                setTimeout(function() {
                    if (isInitialLoad) {
                        showAlert(
                            '<strong>Welcome!</strong> Staff management system loaded successfully.',
                            'info',
                            'information-line',
                            3000
                        );
                    }
                }, 500);
            });

        })(jQuery);
    </script>
@endpush
