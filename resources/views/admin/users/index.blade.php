@extends('layouts.admin')

@section('content')
    <div class="app-content-area">
        <div class="app-content-wrap">
            <div class="container-fluid">

                <!-- Page Header -->
                <div class="row mb-3">
                    <div class="col-xl-12">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h1 class="fs-18 mb-0">{{ $title ?? 'Users' }}</h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb breadcrumb-example1 mb-0">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.dashboard') }}">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active">Users</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- Filters + Add Button -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <div class="row g-3 align-items-end">
                                    <!-- Date Range -->
                                    <div class="col-md-4">
                                        <label class="form-label">Date Range</label>
                                        <input id="rangeCalendar" type="text" class="form-control"
                                            placeholder="Select date range" readonly>
                                    </div>

                                    <!-- Search / Reset -->
                                    <div class="col-md-4 d-flex align-items-end">
                                        <div class="btn-group gap-2" role="group" aria-label="Filter actions">
                                            <button id="filterSearchBtn" type="button" class="btn btn-success btn-md">
                                                <i class="ri-search-line me-1"></i> Search
                                            </button>
                                            <button id="resetFilterBtn" type="button"
                                                class="btn btn-outline-secondary btn-md">
                                                <i class="ri-refresh-line me-1"></i> Reset
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Add User Button with Permission Check -->
                                    <div class="col-md-4 text-end">
                                        @can('users.create')
                                            <a href="{{ route('users.create') }}"
                                                class="btn btn-primary btn-md px-4 modal_open">
                                                Add User
                                            </a>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="dataTable" class="table table-bordered table-hover align-middle w-100">
                                        <thead class="table-light sticky-top">
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Mobile</th>
                                                <th>Email</th>
                                                <th>Status</th>
                                                <th>Total Order</th>
                                                <th>Created</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
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
    <link rel="stylesheet" href="{{ asset('assets/admin/css/plugins/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/plugins/dataTables.dataTables.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/plugins/jquery-confirm.min.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('assets/admin/js/plugins/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/plugins/dataTables.js') }}"></script>
    <script src="{{ asset('assets/admin/js/plugins/jquery-confirm.min.js') }}"></script>


    <script>
        (function($) {
            "use strict";

            let table = null;
            let calendar = null;

            /* ------------------------------------
             * Initialize Flatpickr
             * ------------------------------------ */
            function initDateRangePicker() {
                calendar = flatpickr("#rangeCalendar", {
                    mode: "range",
                    altInput: true,
                    altFormat: "j M Y",
                    dateFormat: "Y-m-d"
                });
            }

            /* ------------------------------------
             * Initialize DataTable (Safe Destroy & Recreate)
             * ------------------------------------ */
            function initDataTable() {
                // Destroy existing DataTable if it exists
                if ($.fn.DataTable.isDataTable('#dataTable')) {
                    $('#dataTable').DataTable().clear().destroy();
                }

                table = $('#dataTable').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    autoWidth: false,
                    pagingType: "simple_numbers",

                    ajax: {
                        url: "{{ route('users.index') }}",
                        data: function(d) {
                            d.date_range = $('#rangeCalendar').val();
                        }
                    },

                    columns: [{
                            data: 'id',
                            className: 'fw-semibold'
                        },
                        {
                            data: 'name'
                        },
                        {
                            data: 'phone'
                        },
                        {
                            data: 'email'
                        },
                        {
                            data: 'status'
                        },
                        {
                            data: 'total_order'
                        },
                        {
                            data: 'created_at'
                        },
                        {
                            data: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ],

                    order: [
                        [0, 'desc']
                    ],

                    language: {
                        search: "",
                        searchPlaceholder: "Search...",
                        lengthMenu: "Show _MENU_",
                        zeroRecords: "No matching records found"
                    }
                });
            }

            /* ------------------------------------
             * Bind Filter Events
             * ------------------------------------ */
            function bindFilterEvents() {
                // Unbind previous events to prevent duplicates
                $('#filterSearchBtn').off('click').on('click', function() {
                    table.draw();
                });

                $('#resetFilterBtn').off('click').on('click', function() {
                    if (calendar) {
                        calendar.clear();
                    }
                    table.draw();
                });
            }

            /* ------------------------------------
             * Initialize Everything
             * ------------------------------------ */
            $(document).ready(function() {
                initDateRangePicker();
                initDataTable();
                bindFilterEvents();
            });

        })(jQuery);
    </script>
@endpush
