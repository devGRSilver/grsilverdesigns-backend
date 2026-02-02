@extends('layouts.admin')

@section('content')
    <div class="app-content-area">
        <div class="app-content-wrap">
            <div class="container-fluid">

                {{-- Page Header --}}
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <h1 class="fs-18 mb-0">{{ $title ?? 'Orders' }}</h1>

                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}">Home</a>
                                </li>
                                <li class="breadcrumb-item active">Orders</li>
                            </ol>
                        </div>
                    </div>
                </div>

                {{-- Filters --}}
                <div class="row">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">

                                <div class="row g-2 align-items-end">

                                    {{-- Order Status --}}
                                    <div class="col-md-3">
                                        <label class="form-label">Order Status</label>
                                        <select id="filterStatus" class="form-control">
                                            <option value="">All</option>
                                            <option value="pending_payment">Pending Payment</option>
                                            <option value="payment_received">Payment Received</option>
                                            <option value="confirmed">Confirmed</option>
                                            <option value="processing">Processing</option>
                                            <option value="packed">Packed</option>
                                            <option value="shipped">Shipped</option>
                                            <option value="in_transit">In Transit</option>
                                            <option value="out_for_delivery">Out for Delivery</option>
                                            <option value="delivered">Delivered</option>
                                            <option value="cancel_requested">Cancel Requested</option>
                                            <option value="cancelled">Cancelled</option>
                                            <option value="return_requested">Return Requested</option>
                                            <option value="return_approved">Return Approved</option>
                                            <option value="returned">Returned</option>
                                            <option value="refunded">Refunded</option>
                                            <option value="failed">Failed</option>
                                        </select>
                                    </div>

                                    {{-- Date Range --}}
                                    <div class="col-md-3">
                                        <label class="form-label">Date Range</label>
                                        <input type="text" id="rangeCalendar" class="form-control"
                                            placeholder="Select date range" readonly>
                                    </div>

                                    {{-- Search --}}
                                    <div class="col-md-2">
                                        <button id="filterSearchBtn" class="btn btn-success w-100">
                                            <i class="ri-search-line me-1"></i> Search
                                        </button>
                                    </div>

                                    {{-- Reset --}}
                                    <div class="col-md-2">
                                        <button id="resetFilterBtn" class="btn btn-outline-secondary w-100">
                                            <i class="ri-refresh-line me-1"></i> Reset
                                        </button>
                                    </div>

                                    {{-- Export --}}
                                    @can('orders.export')
                                        <div class="col-md-2">
                                            <button id="exportOrdersBtn" class="btn btn-outline-primary w-100">
                                                <i class="ri-download-line me-1"></i> Export
                                            </button>
                                        </div>
                                    @endcan

                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                {{-- Orders Table --}}
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">

                                @cannot('orders.view.any')
                                    <div class="alert alert-warning mb-0">
                                        You don’t have permission to view orders.
                                    </div>
                                @else
                                    <div class="table-responsive">
                                        <table id="dataTable" class="table table-bordered table-hover align-middle w-100">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Order ID</th>
                                                    <th>Customer</th>
                                                    <th>Items</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                    <th>Rating</th>
                                                    <th>Created</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                @endcannot

                            </div>
                        </div>
                    </div>
                </div>



                {{-- Order Status Reference --}}
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header">
                                <h6 class="mb-0">Order Status Reference</h6>
                            </div>

                            <div class="card-body table-responsive">
                                <table class="table table-bordered align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%">#</th>
                                            <th width="25%">Status</th>
                                            <th>Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <tr>
                                            <td>1</td>
                                            <td><span class="badge bg-warning text-dark">Pending Payment</span></td>
                                            <td>Order placed but payment is not completed yet.</td>
                                        </tr>

                                        <tr>
                                            <td>2</td>
                                            <td><span class="badge bg-success">Payment Received</span></td>
                                            <td>Payment has been received successfully.</td>
                                        </tr>

                                        <tr>
                                            <td>3</td>
                                            <td><span class="badge bg-primary">Confirmed</span></td>
                                            <td>Order is confirmed and ready for processing.</td>
                                        </tr>

                                        <tr>
                                            <td>4</td>
                                            <td><span class="badge bg-info">Processing</span></td>
                                            <td>Order is currently being prepared.</td>
                                        </tr>

                                        <tr>
                                            <td>5</td>
                                            <td><span class="badge bg-primary">Packed</span></td>
                                            <td>Items are packed and ready for shipment.</td>
                                        </tr>

                                        <tr>
                                            <td>6</td>
                                            <td><span class="badge bg-secondary">Shipped</span></td>
                                            <td>Order has been shipped from warehouse.</td>
                                        </tr>

                                        <tr>
                                            <td>7</td>
                                            <td><span class="badge bg-info">In Transit</span></td>
                                            <td>Order is on the way to the delivery address.</td>
                                        </tr>

                                        <tr>
                                            <td>8</td>
                                            <td><span class="badge bg-warning text-dark">Out for Delivery</span></td>
                                            <td>Order will be delivered today.</td>
                                        </tr>

                                        <tr>
                                            <td>9</td>
                                            <td><span class="badge bg-success">Delivered</span></td>
                                            <td>Order has been delivered successfully.</td>
                                        </tr>

                                        <tr>
                                            <td>10</td>
                                            <td><span class="badge bg-warning text-dark">Cancel Requested</span></td>
                                            <td>Customer has requested cancellation.</td>
                                        </tr>

                                        <tr>
                                            <td>11</td>
                                            <td><span class="badge bg-danger">Cancelled</span></td>
                                            <td>Order has been cancelled.</td>
                                        </tr>

                                        <tr>
                                            <td>12</td>
                                            <td><span class="badge bg-warning text-dark">Return Requested</span></td>
                                            <td>Customer requested a return.</td>
                                        </tr>

                                        <tr>
                                            <td>13</td>
                                            <td><span class="badge bg-info text-dark">Return Approved</span></td>
                                            <td>Return request approved by admin.</td>
                                        </tr>

                                        <tr>
                                            <td>14</td>
                                            <td><span class="badge bg-primary">Returned</span></td>
                                            <td>Returned item received successfully.</td>
                                        </tr>

                                        <tr>
                                            <td>15</td>
                                            <td><span class="badge bg-success">Refunded</span></td>
                                            <td>Refund processed and completed.</td>
                                        </tr>

                                        <tr>
                                            <td>16</td>
                                            <td><span class="badge bg-danger">Failed</span></td>
                                            <td>Order failed due to payment or system error.</td>
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
@endsection
@push('style')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/plugins/dataTables.dataTables.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/plugins/flatpickr.min.css') }}">

    <style>
        .btn {
            height: 38px;
        }
    </style>
@endpush
@push('scripts')
    <script src="{{ asset('assets/admin/js/plugins/dataTables.js') }}"></script>
    <script src="{{ asset('assets/admin/js/plugins/flatpickr.min.js') }}"></script>

    <script>
        $(function() {

            /* Date Picker */
            const calendar = flatpickr('#rangeCalendar', {
                mode: 'range',
                altInput: true,
                altFormat: 'j M Y',
                dateFormat: 'Y-m-d'
            });

            /* Export */
            @can('orders.export')
                $('#exportOrdersBtn').on('click', function() {
                    const params = new URLSearchParams({
                        status: $('#filterStatus').val(),
                        date_range: $('#rangeCalendar').val()
                    });

                    window.location.href = "#" + params.toString();
                });
            @endcan

            /* DataTable */
            @can('orders.view.any')
                const table = $('#dataTable').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,

                    ajax: {
                        url: "{{ route('orders.index') }}",
                        data: function(d) {
                            d.status = $('#filterStatus').val();
                            d.date_range = $('#rangeCalendar').val();
                        }
                    },

                    columns: [{
                            data: 'order_number'
                        },
                        {
                            data: 'customer'
                        },
                        {
                            data: 'items_count'
                        },
                        {
                            data: 'amount',
                            orderable: false
                        },
                        {
                            data: 'status',
                            orderable: false
                        },
                        {
                            data: 'rating',
                            orderable: false
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
                    ]
                });

                $('#filterSearchBtn').on('click', () => table.draw());

                $('#resetFilterBtn').on('click', function() {
                    $('#filterStatus').val('');
                    calendar.clear();
                    table.draw();
                });
            @endcan

        });
    </script>
@endpush
