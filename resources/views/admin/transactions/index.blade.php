@extends('layouts.admin')

@use(App\Enums\TransactionStatus)




@section('content')
    <div class="app-content-area">
        <div class="app-content-wrap">
            <div class="container-fluid">

                <!-- Page Header -->
                <div class="row mb-3">
                    <div class="col-xl-12">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h1 class="fs-18 mb-0">{{ $title ?? 'Transactions' }}</h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb breadcrumb-example1 mb-0">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.dashboard') }}">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active">{{ $resourceName ?? 'Transactions' }}</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>


                <!-- Filters -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <div class="row g-3 align-items-end">

                                    <!-- Filters (only if can view transactions) -->
                                    @can('transactions.view.any')
                                        <!-- Transaction Status -->
                                        <div class="col-md-3">
                                            <label class="form-label">Transaction Status</label>
                                            <select id="filterStatus" class="form-control filter-input">
                                                <option value="">ALL</option>
                                                @foreach (TransactionStatus::cases() as $status)
                                                    <option value="{{ $status->value }}">
                                                        {{ \Illuminate\Support\Str::headline($status->value) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Date Range -->
                                        <div class="col-md-3">
                                            <label class="form-label">Date Range</label>
                                            <input id="rangeCalendar" type="text" class="form-control"
                                                placeholder="Select date range" readonly>
                                        </div>

                                        <!-- Payment Method (optional) -->
                                        <div class="col-md-2">
                                            <label class="form-label">Payment Method</label>
                                            <select id="filterPaymentMethod" class="form-control filter-input"
                                                name="payment_gateway">
                                                <option value="">All Methods</option>
                                                <option value="stripe">Stripe</option>
                                                <option value="paypal">PayPal</option>
                                                <option value="razorpay">Razorpay</option>
                                                <option value="cod">Cash on Delivery</option>
                                            </select>
                                        </div>

                                        <!-- Actions -->
                                        <div class="col-md-4 d-flex align-items-end">
                                            <div class="btn-group gap-2 w-100">
                                                <button id="filterSearchBtn" type="button" class="btn btn-success">
                                                    <i class="ri-search-line me-1"></i> Search
                                                </button>

                                                <button id="resetFilterBtn" type="button" class="btn btn-outline-secondary">
                                                    <i class="ri-refresh-line me-1"></i> Reset
                                                </button>

                                                <!-- Export Button -->
                                                @can('transactions.export')
                                                    {{-- <a href="#" id="exportBtn" class="btn btn-primary" target="_blank">
                                                        <i class="ri-download-line me-1"></i> Export
                                                    </a> --}}
                                                @endcan
                                            </div>
                                        </div>
                                    @else
                                        <!-- No permission message -->
                                        <div class="col-12">
                                            <div class="alert alert-warning">
                                                <i class="ri-alert-line me-2"></i>
                                                You don't have permission to view transactions.
                                            </div>
                                        </div>
                                    @endcan

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                @can('transactions.view.any')
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="dataTable" class="table table-bordered align-middle w-100">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Transaction ID</th>
                                                    <th>Order ID</th>
                                                    <th>User</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                    <th>Created</th>
                                                    @canany(['transactions.view', 'transactions.update.status',
                                                        'transactions.refund'])
                                                        <th>Action</th>
                                                    @endcanany
                                                </tr>
                                            </thead>
                                        </table>
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
@endpush

@push('scripts')
    <script src="{{ asset('assets/admin/js/plugins/dataTables.js') }}"></script>
    <script src="{{ asset('assets/admin/js/plugins/jquery-confirm.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/plugins/flatpickr.min.js') }}"></script>

    <script>
        $(function() {

            let calendar = flatpickr("#rangeCalendar", {
                mode: "range",
                altInput: true,
                altFormat: "j M Y",
                dateFormat: "Y-m-d"
            });

            let table = $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,

                ajax: {
                    url: "{{ route($resource . '.index') }}",
                    data: function(d) {
                        d.status = $('#filterStatus').val();
                        d.date_range = $('#rangeCalendar').val();
                        d.payment_method = $('#filterPaymentMethod').val();
                    }
                },

                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'transaction_id'
                    },
                    {
                        data: 'order_id'
                    },
                    {
                        data: 'user'
                    },
                    {
                        data: 'amount'
                    },
                    {
                        data: 'status'
                    },
                    {
                        data: 'created_at'
                    },
                    @canany(['transactions.view', 'transactions.update.status', 'transactions.refund'])
                        {
                            data: 'action',
                            orderable: false,
                            searchable: false
                        }
                    @endcanany
                ],

                order: [
                    [0, 'desc']
                ],

                language: {
                    search: "",
                    searchPlaceholder: "Search transaction...",
                    lengthMenu: "Show _MENU_",
                    zeroRecords: "No transactions found"
                }
            });

            // Update statistics on data load
            table.on('draw', function() {
                updateStatistics();
            });

            // Initialize statistics
            function updateStatistics() {
                // You can make an AJAX call to get statistics
                // or calculate from the current table data
                $.ajax({
                    url: "#",
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            $('#totalCount').text(response.data.total || 0);
                            $('#completedCount').text(response.data.completed || 0);
                            $('#pendingCount').text(response.data.pending || 0);
                            $('#failedCount').text(response.data.failed || 0);
                        }
                    }
                });
            }

            // Search button
            $('#filterSearchBtn').on('click', function() {
                table.ajax.reload();
            });

            // Reset button
            $('#resetFilterBtn').on('click', function() {
                $('.filter-input').val('');
                calendar.clear();
                table.ajax.reload();
            });

            // Update export URL with filters
            function updateExportUrl() {
                let params = new URLSearchParams();

                if ($('#filterStatus').val()) {
                    params.append('status', $('#filterStatus').val());
                }

                if ($('#rangeCalendar').val()) {
                    params.append('date_range', $('#rangeCalendar').val());
                }

                if ($('#filterPaymentMethod').val()) {
                    params.append('payment_method', $('#filterPaymentMethod').val());
                }

                let exportUrl = "#";
                if (params.toString()) {
                    exportUrl += '?' + params.toString();
                }

                $('#exportBtn').attr('href', exportUrl);
            }

            // Update export URL on filter changes
            $('.filter-input').on('change', updateExportUrl);
            calendar.config.onChange.push(updateExportUrl);


            // Initialize statistics on page load
            updateStatistics();

        });
    </script>
@endpush
