@extends('layouts.admin')

@section('content')
    <div class="app-content-area">
        <div class="app-content-wrap">
            <div class="container-fluid">
                <!-- Page Header -->
                <div class="row mb-3">
                    <div class="col-xl-12">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h1 class="fs-18 mb-0">{{ $title ?? 'Products' }}</h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb breadcrumb-example1 mb-0">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.dashboard') }}">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active">Products</li>
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

                                    <!-- Parent Category Filter -->
                                    <div class="col-lg-2 col-md-6">
                                        <label for="filterParent" class="form-label fw-semibold mb-2">
                                            <i class="ri-folder-line me-1"></i>Parent Category
                                        </label>
                                        <select id="filterParent" class="form-select select2" name="parent_id">
                                            <option value="">All Categories</option>
                                            @foreach ($categories ?? [] as $cat)
                                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Sub Category Filter -->
                                    <div class="col-lg-2 col-md-6">
                                        <label for="subCategory" class="form-label fw-semibold mb-2">
                                            <i class="ri-folder-2-line me-1"></i>Sub Category
                                        </label>
                                        <select id="subCategory" class="form-select select2 sub_category"
                                            name="sub_category_id">
                                            <option value="">Select Sub Category</option>
                                        </select>
                                    </div>

                                    <!-- Stock Filter -->
                                    <div class="col-lg-2 col-md-6">
                                        <label for="stockQuantity" class="form-label fw-semibold mb-2">
                                            <i class="ri-box-3-line me-1"></i>Stock
                                        </label>
                                        <select id="stockQuantity" class="form-select" name="stock_quantity">
                                            <option value="">All</option>
                                            <option value="1">In Stock</option>
                                            <option value="0">Out of Stock</option>
                                        </select>
                                    </div>

                                    <!-- Status Filter -->
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
                                            @can('products.create')
                                                @php
                                                    $uid = Str::random(32);
                                                @endphp
                                                <a href="{{ route('products.create', ['uid' => $uid]) }}"
                                                    class="btn btn-primary px-4">
                                                    <i class="ri-add-line me-1"></i> Add Product
                                                </a>
                                            @endcan
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Products Table -->
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
                                                <th class="px-3 py-3 fw-semibold text-center">Image</th>
                                                <th class="px-3 py-3 fw-semibold">Title</th>
                                                <th class="px-3 py-3 fw-semibold">Category</th>
                                                <th class="px-3 py-3 fw-semibold text-center">Total Variant</th>
                                                <th class="px-3 py-3 fw-semibold text-center">Status</th>
                                                <th class="px-3 py-3 fw-semibold">Published</th>
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
                        <p class="text-muted fw-semibold mb-0">Loading products...</p>
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
                $('.select2').select2({
                    width: "100%",
                    placeholder: "Select Option",
                    allowClear: true,
                    theme: "classic"
                });

                // Trigger search when select2 changes
                $('#filterParent, #subCategory').on('change', function() {
                    isFilterChange = true;
                    $searchBtn.click();
                });
            }

            /* ------------------------------------
             * Load Subcategories
             * ------------------------------------ */
            function initSubcategoryLoader() {
                $('#filterParent').on('change', function() {
                    let categoryId = $(this).val();
                    let $sub = $('#subCategory');

                    if (!categoryId) {
                        $sub.html('<option value="">Select Sub Category</option>').trigger('change');
                        return;
                    }

                    $sub.html('<option value="">Loading...</option>').prop('disabled', true);

                    let url = "{{ route('subcategories.ajax', ':id') }}".replace(':id', categoryId);

                    $.get(url)
                        .done(function(data) {
                            $sub.html('<option value="">Select Sub Category</option>');
                            $.each(data, function(_, item) {
                                $sub.append(`<option value="${item.id}">${item.name}</option>`);
                            });
                            $sub.prop('disabled', false);
                        })
                        .fail(function() {
                            $sub.html('<option value="">Error loading data</option>');
                            $sub.prop('disabled', false);
                            showAlert(
                                'Error loading subcategories. Please try again.',
                                'danger',
                                'error-warning-line',
                                3000
                            );
                        });
                });
            }

            /* ------------------------------------
             * Show Loading Overlay
             * ------------------------------------ */
            function showLoadingOverlay() {
                // Disable filter inputs
                $('#filterStatus, #filterParent, #subCategory, #stockQuantity').prop('disabled', true);
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
                $('#filterStatus, #filterParent, #subCategory, #stockQuantity').prop('disabled', false);
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
                            url: "{{ route('products.index') }}",
                            data: function(d) {
                                d.category_id = $('#filterParent').val();
                                d.sub_category_id = $('#subCategory').val();
                                d.stock_quantity = $('#stockQuantity').val();
                                d.status = $('#filterStatus').val();
                                d._token = "{{ csrf_token() }}";
                            },
                            error: function(xhr, error, thrown) {
                                hideLoadingOverlay();
                                console.error('DataTable error:', error);

                                showAlert(
                                    '<strong>Error!</strong> Unable to load products. Please check your connection and try again.',
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
                                data: 'image',
                                className: 'px-3 py-2 text-center',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'product_name',
                                className: 'px-3 py-2 fw-medium'
                            },
                            {
                                data: 'category',
                                className: 'px-3 py-2'
                            },
                            {
                                data: 'total_variant',
                                className: 'px-3 py-2 text-center fw-semibold'
                            },
                            {
                                data: 'status',
                                className: 'px-3 py-2 text-center'
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
                            searchPlaceholder: "Search products...",
                            lengthMenu: "_MENU_ per page",
                            zeroRecords: `
                                <div class="text-center py-5 my-5">
                                    <i class="ri-box-3-line display-1 text-muted mb-3 d-block"></i>
                                    <h5 class="text-muted mb-2">No Products Found</h5>
                                    <p class="text-muted mb-0">Try adjusting your filters or search criteria</p>
                                </div>
                            `,
                            info: "Showing _START_ to _END_ of _TOTAL_ products",
                            infoEmpty: "No products to display",
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
                                    `<strong>Success!</strong> Loaded ${recordsTotal} product(s) successfully.`,
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
                    $('#filterStatus').val('');
                    $('#filterParent').val('').trigger('change');
                    $('#subCategory').val('').trigger('change');
                    $('#stockQuantity').val('');

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
                            '<strong>Filters Reset!</strong> Showing all products.',
                            'info',
                            'information-line',
                            2500
                        );
                    }, 300);
                });

                // Auto-search on status and stock change
                $('#filterStatus, #stockQuantity').on('change', function() {
                    isFilterChange = true;
                    $searchBtn.click();
                });

                // Enter key to search in filter inputs
                $('#filterStatus, #stockQuantity').on('keypress', function(e) {
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
             * Delete Product Handler
             * ------------------------------------ */
            function initDeleteHandlers() {
                $(document).on('click', '.delete-product', function(e) {
                    e.preventDefault();
                    const $this = $(this);
                    const url = $this.data('url');
                    const productName = $this.data('name');

                    $.confirm({
                        title: 'Confirm Delete',
                        content: `Are you sure you want to delete <strong>"${productName}"</strong>?<br><small class="text-muted">This will also delete all variants and images. This action cannot be undone.</small>`,
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
                                                `<strong>Success!</strong> Product "${productName}" deleted successfully.`,
                                                'success',
                                                'checkbox-circle-line',
                                                4000
                                            );
                                            table.draw();
                                        },
                                        error: function(xhr) {
                                            showAlert(
                                                `<strong>Error!</strong> ${xhr.responseJSON?.message || 'Unable to delete product.'}`,
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
             * Initialize Everything
             * ------------------------------------ */
            $(document).ready(function() {
                initSelect2();
                initSubcategoryLoader();
                initDataTable();
                bindFilterEvents();
                initTooltips();
                // initDeleteHandlers();

                // Show welcome message on initial load
                setTimeout(function() {
                    if (isInitialLoad) {
                        showAlert(
                            '<strong>Welcome!</strong> Product management system loaded successfully.',
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
