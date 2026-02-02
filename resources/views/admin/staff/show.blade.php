@extends('layouts.admin')
@section('content')
    <div class="app-content-area">
        <div class="app-content-wrap">
            <div class="container-fluid">

                <!-- Page Header -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="page-title-box d-flex-between flex-wrap gap-15 align-items-center">

                            <h1 class="page-title fs-18 lh-1 mb-0">
                                {{ $title ?? 'Users' }}
                            </h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb breadcrumb-example1 mb-0">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('staff.index') }}">User</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">User Account</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-12">
                        <div class="customer-nav mb-25 mobile-nav">
                            <ul class="d-flex-items gap-10">
                                <li class="active"><a class="btn btn-primary"
                                        href="{{ route('staff.show', encrypt($data->id)) }}">User Info</a>
                                </li>
                                <li class=""><a class="btn btn-light modal_open"
                                        href="{{ route('staff.password.edit', encrypt($data->id)) }}"> Change Password
                                    </a></li>
                                <li class=""><a class="btn btn-light modal_open"
                                        href="{{ route('staff.edit', encrypt($data->id)) }}">Edit User </a>
                                </li>

                                {{-- <li class=""><a class="btn btn-light delete_record"
                                        href="{{ route('staff.delete', encrypt($data->id)) }}">Delete User </a>
                                </li> --}}

                                <li class="">
                                    {!! status_dropdown($data->status, [
                                        'id' => $data->id,
                                        'url' => route('staff.status', encrypt($data->id)),
                                        'method' => 'PUT',
                                    ]) !!}
                                </li>




                            </ul>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="card">
                            <div class="card-header justify-between">
                                <h4 class="">Personal Information</h4>
                                <div class="card-dropdown">
                                    <div class="dropdown">
                                        <a class="card-dropdown-icon" href="javascript:void(0);" role="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ri-more-2-fill"></i>
                                        </a>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item modal_open"
                                                href="{{ route('staff.edit', encrypt($data->id)) }}">Edit</a>
                                            <a class="dropdown-item modal_open"
                                                href="{{ route('staff.password.edit', encrypt($data->id)) }}">Change
                                                Password </a>

                                            <a class="dropdown-item delete_record"
                                                href="{{ route('staff.delete', encrypt($data->id)) }}">Delete User </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body pt-15">
                                <div class="text-center mb-10">
                                    <div class="avatar avatar-big radius-100">
                                        <img class="radius-100" src="{{ $data->profile_picture }}" alt="image not found">
                                    </div>
                                </div>
                                <div class="profile-info text-center mb-15">
                                    <h3 class="mb-5">{{ $data->name }}</h3>
                                    <h6 class="text-body mb-10">User ID: # {{ $data->id }}</h6>
                                    <div class="d-flex-center gap-15">
                                        <a href="javascript:void(0);" class="btn-icon btn-warning-light fs-16">
                                            <i class="ri-twitter-x-line"></i>
                                        </a><a href="javascript:void(0);" class="btn-icon btn-success-light fs-16">
                                            <i class="ri-facebook-fill"></i>
                                        </a>
                                        <a href="javascript:void(0);" class="btn-icon btn-info-light fs-16">
                                            <i class="ri-linkedin-fill"></i>
                                        </a>
                                        <a href="javascript:void(0);" class="btn-icon btn-danger-light fs-16">
                                            <i class="ri-whatsapp-line"></i>
                                        </a>
                                        <a href="javascript:void(0);" class="btn-icon btn-primary-light fs-16">
                                            <i class="ri-telegram-2-fill"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table">
                                        <tbody>
                                            <tr>
                                                <td>Name</td>
                                                <td>
                                                    <div class="text-heading">{{ $data->name ?? '' }}</div>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>Phone</td>
                                                <td>
                                                    <div class="text-heading">{{ $data->phone_code }} {{ $data->phone }}
                                                    </div>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>Email</td>
                                                <td>
                                                    <div class="text-heading">{{ $data->email }}</div>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>Timezone</td>
                                                <td>
                                                    <div class="text-heading">{{ $data->timezone }}</div>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>Created Date</td>
                                                <td>
                                                    <div class="text-heading">{{ $data->created_at }}</div>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>Currency</td>
                                                <td>
                                                    <div class="badge bg-label-success">{{ $data->currency ?? '-' }}</div>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>Last Login At</td>
                                                <td>
                                                    <div class="badge bg-label-success">{{ $data->last_login_at ?? '-' }}
                                                    </div>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>IP Address</td>
                                                <td>
                                                    <div class="badge bg-label-success">{{ $data->ip_address ?? '-' }}
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- New: User Active Status -->
                                            <tr>
                                                <td>Status</td>
                                                <td>
                                                    {!! status_dropdown($data->status, [
                                                        'id' => $data->id,
                                                        'url' => route('staff.status', encrypt($data->id)),
                                                        'method' => 'PUT',
                                                    ]) !!}

                                                </td>

                                            </tr>
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-8">
                        <div class="row">
                            <div class="col-xl-6 col-lg-3 col-md-6">
                                <div class="card">
                                    <div class="card-body mini-card-body d-flex align-center gap-16">
                                        <div class="avatar avatar-xl bg-primary-transparent text-primary">
                                            <i class="ri-shopping-bag-3-line fs-42"></i>
                                        </div>
                                        <div class="card-content">
                                            <span class="d-block fs-16 mb-5">Total Orders</span>
                                            <h2 class="mb-5">98.5k</h2>
                                            <span class="text-success">+1.24%<i
                                                    class="ri-arrow-up-line ml-5 d-inline-block"></i></span>
                                            <span class="fs-12 text-muted ml-5">This week</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-3 col-md-6">
                                <div class="card">
                                    <div class="card-body mini-card-body d-flex align-center gap-16">
                                        <div class="avatar avatar-xl bg-warning-transparent text-warning">
                                            <i class="ri-time-line fs-42"></i>
                                        </div>
                                        <div class="card-content">
                                            <span class="d-block fs-16 mb-5">Pending Orders</span>
                                            <h2 class="mb-5">12</h2>
                                            <span class="text-warning">+2 pending<i
                                                    class="ri-arrow-up-line ml-5 d-inline-block"></i></span>
                                            <span class="fs-12 text-muted ml-5">In Dispatch</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-3 col-md-6">
                                <div class="card">
                                    <div class="card-body mini-card-body d-flex align-center gap-16">
                                        <div class="avatar avatar-xl bg-success-transparent text-success">
                                            <i class="ri-checkbox-circle-line fs-42"></i>
                                        </div>
                                        <div class="card-content">
                                            <span class="d-block fs-16 mb-5">Completed Orders</span>
                                            <h2 class="mb-5">86</h2>
                                            <span class="text-success">+8.5%<i
                                                    class="ri-arrow-up-line ml-5 d-inline-block"></i></span>
                                            <span class="fs-12 text-muted ml-5">This month</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-3 col-md-6">
                                <div class="card">
                                    <div class="card-body mini-card-body d-flex align-center gap-16">
                                        <div class="avatar avatar-xl bg-purple-transparent text-purple">
                                            <i class="ri-money-dollar-circle-line fs-42"></i>
                                        </div>
                                        <div class="card-content">
                                            <span class="d-block fs-16 mb-5">Total Spent</span>
                                            <h2 class="mb-5">$12,450</h2>
                                            <span class="text-success">+15%<i
                                                    class="ri-arrow-up-line ml-5 d-inline-block"></i></span>
                                            <span class="fs-12 text-muted ml-5">vs last year</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-12">
                                <div class="card">
                                    <div class="card-header justify-between">
                                        <h4>Order List</h4>
                                    </div>
                                    <div class="card-body pt-15">
                                        <div class="table-responsive">
                                            <div id="dataTableDefault_wrapper"
                                                class="dataTables_wrapper dt-bootstrap5 no-footer">
                                                <div class="row">
                                                    <div class="col-sm-12 col-md-6">
                                                        <div class="dataTables_length" id="dataTableDefault_length">
                                                            <label>Show <select name="dataTableDefault_length"
                                                                    aria-controls="dataTableDefault"
                                                                    class="form-select form-select-sm">
                                                                    <option value="10">10</option>
                                                                    <option value="25">25</option>
                                                                    <option value="50">50</option>
                                                                    <option value="100">100</option>
                                                                </select> entries</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-12 col-md-6">
                                                        <div id="dataTableDefault_filter" class="dataTables_filter">
                                                            <label>Search:<input type="search"
                                                                    class="form-control form-control-sm" placeholder=""
                                                                    aria-controls="dataTableDefault"></label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <table id="dataTableDefault"
                                                            class="table text-nowrap w-100 dataTable no-footer"
                                                            aria-describedby="dataTableDefault_info">
                                                            <thead>
                                                                <tr>
                                                                    <th class="sorting_disabled sorting_asc"
                                                                        rowspan="1" colspan="1"
                                                                        aria-label="Order ID" style="width: 81px;">Order
                                                                        ID</th>
                                                                    <th class="sorting" tabindex="0"
                                                                        aria-controls="dataTableDefault" rowspan="1"
                                                                        colspan="1"
                                                                        aria-label="Order Date: activate to sort column ascending"
                                                                        style="width: 108.203px;">Order Date</th>
                                                                    <th class="sorting" tabindex="0"
                                                                        aria-controls="dataTableDefault" rowspan="1"
                                                                        colspan="1"
                                                                        aria-label="Delivery Date: activate to sort column ascending"
                                                                        style="width: 130.125px;">Delivery Date</th>
                                                                    <th class="sorting" tabindex="0"
                                                                        aria-controls="dataTableDefault" rowspan="1"
                                                                        colspan="1"
                                                                        aria-label="Payment Status: activate to sort column ascending"
                                                                        style="width: 148.281px;">Payment Status</th>
                                                                    <th class="sorting" tabindex="0"
                                                                        aria-controls="dataTableDefault" rowspan="1"
                                                                        colspan="1"
                                                                        aria-label="Order Status: activate to sort column ascending"
                                                                        style="width: 122.625px;">Order Status</th>
                                                                    <th class="sorting" tabindex="0"
                                                                        aria-controls="dataTableDefault" rowspan="1"
                                                                        colspan="1"
                                                                        aria-label="Total Spent: activate to sort column ascending"
                                                                        style="width: 111.406px;">Total Spent</th>
                                                                    <th class="sorting" tabindex="0"
                                                                        aria-controls="dataTableDefault" rowspan="1"
                                                                        colspan="1"
                                                                        aria-label="Action: activate to sort column ascending"
                                                                        style="width: 71.3438px;">Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>






                                                                <tr class="odd">
                                                                    <td class="sorting_1">#10025</td>
                                                                    <td>Apr 25, 2025</td>
                                                                    <td>Apr 29, 2025</td>
                                                                    <td><span class="text-black fw-5">Credit Card</span>
                                                                    </td>
                                                                    <td><span class="badge bg-label-success">Paid</span>
                                                                    </td>
                                                                    <td>$129.99</td>
                                                                    <td>
                                                                        <div class="d-flex-items gap-5">
                                                                            <a class="btn-icon btn-success-light"
                                                                                href="ecommerce-order-details.html">
                                                                                <i class="ri-eye-line"></i>
                                                                            </a>
                                                                            <button
                                                                                class="btn-icon btn-danger-light removeRow"
                                                                                type="button">
                                                                                <i class="ri-delete-bin-line"></i>
                                                                            </button>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr class="even">
                                                                    <td class="sorting_1">#10026</td>
                                                                    <td>Apr 26, 2025</td>
                                                                    <td>May 1, 2025</td>
                                                                    <td><span class="text-black fw-5">PayPal</span></td>
                                                                    <td><span class="badge bg-label-success">Paid</span>
                                                                    </td>
                                                                    <td>$89.50</td>
                                                                    <td>
                                                                        <div class="d-flex-items gap-5">
                                                                            <a class="btn-icon btn-success-light"
                                                                                href="ecommerce-order-details.html">
                                                                                <i class="ri-eye-line"></i>
                                                                            </a>
                                                                            <button
                                                                                class="btn-icon btn-danger-light removeRow"
                                                                                type="button">
                                                                                <i class="ri-delete-bin-line"></i>
                                                                            </button>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr class="odd">
                                                                    <td class="sorting_1">#10027</td>
                                                                    <td>Apr 27, 2025</td>
                                                                    <td>Apr 30, 2025</td>
                                                                    <td><span class="text-black fw-5">Bank Transfer</span>
                                                                    </td>
                                                                    <td><span class="badge bg-label-warning">Pending</span>
                                                                    </td>
                                                                    <td>$245.75</td>
                                                                    <td>
                                                                        <div class="d-flex-items gap-5">
                                                                            <a class="btn-icon btn-success-light"
                                                                                href="ecommerce-order-details.html">
                                                                                <i class="ri-eye-line"></i>
                                                                            </a>
                                                                            <button
                                                                                class="btn-icon btn-danger-light removeRow"
                                                                                type="button">
                                                                                <i class="ri-delete-bin-line"></i>
                                                                            </button>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr class="even">
                                                                    <td class="sorting_1">#10028</td>
                                                                    <td>Apr 28, 2025</td>
                                                                    <td>May 3, 2025</td>
                                                                    <td><span class="text-black fw-5">Credit Card</span>
                                                                    </td>
                                                                    <td><span class="badge bg-label-danger">Failed</span>
                                                                    </td>
                                                                    <td>$179.99</td>
                                                                    <td>
                                                                        <div class="d-flex-items gap-5">
                                                                            <a class="btn-icon btn-success-light"
                                                                                href="ecommerce-order-details.html">
                                                                                <i class="ri-eye-line"></i>
                                                                            </a>
                                                                            <button
                                                                                class="btn-icon btn-danger-light removeRow"
                                                                                type="button">
                                                                                <i class="ri-delete-bin-line"></i>
                                                                            </button>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr class="odd">
                                                                    <td class="sorting_1">#10029</td>
                                                                    <td>Apr 29, 2025</td>
                                                                    <td>May 5, 2025</td>
                                                                    <td><span class="text-black fw-5">Cash on
                                                                            Delivery</span>
                                                                    </td>
                                                                    <td><span class="badge bg-label-info">Processing</span>
                                                                    </td>
                                                                    <td>$65.20</td>
                                                                    <td>
                                                                        <div class="d-flex-items gap-5">
                                                                            <a class="btn-icon btn-success-light"
                                                                                href="ecommerce-order-details.html">
                                                                                <i class="ri-eye-line"></i>
                                                                            </a>
                                                                            <button
                                                                                class="btn-icon btn-danger-light removeRow"
                                                                                type="button">
                                                                                <i class="ri-delete-bin-line"></i>
                                                                            </button>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr class="even">
                                                                    <td class="sorting_1">#10030</td>
                                                                    <td>Apr 30, 2025</td>
                                                                    <td>May 4, 2025</td>
                                                                    <td><span class="text-black fw-5">Stripe</span></td>
                                                                    <td><span class="badge bg-label-success">Paid</span>
                                                                    </td>
                                                                    <td>$320.00</td>
                                                                    <td>
                                                                        <div class="d-flex-items gap-5">
                                                                            <a class="btn-icon btn-success-light"
                                                                                href="ecommerce-order-details.html">
                                                                                <i class="ri-eye-line"></i>
                                                                            </a>
                                                                            <button
                                                                                class="btn-icon btn-danger-light removeRow"
                                                                                type="button">
                                                                                <i class="ri-delete-bin-line"></i>
                                                                            </button>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12 col-md-5">
                                                        <div class="dataTables_info" id="dataTableDefault_info"
                                                            role="status" aria-live="polite">Showing 1 to 6 of 6 entries
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-12 col-md-7">
                                                        <div class="dataTables_paginate paging_simple_numbers"
                                                            id="dataTableDefault_paginate">
                                                            <ul class="pagination">
                                                                <li class="paginate_button page-item previous disabled"
                                                                    id="dataTableDefault_previous"><a href="#"
                                                                        aria-controls="dataTableDefault" data-dt-idx="0"
                                                                        tabindex="0" class="page-link">Previous</a></li>
                                                                <li class="paginate_button page-item active"><a
                                                                        href="#" aria-controls="dataTableDefault"
                                                                        data-dt-idx="1" tabindex="0"
                                                                        class="page-link">1</a></li>
                                                                <li class="paginate_button page-item next disabled"
                                                                    id="dataTableDefault_next"><a href="#"
                                                                        aria-controls="dataTableDefault" data-dt-idx="2"
                                                                        tabindex="0" class="page-link">Next</a></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
    <link rel="stylesheet" href="{{ asset('assets/admin/css/plugins/jquery-confirm.min.css') }}">
@endpush


@push('scripts')
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.5/css/dataTables.dataTables.css">
    <script src="https://cdn.datatables.net/2.3.5/js/dataTables.js"></script>

    <script src="{{ asset('assets/admin/js/plugins/jquery-confirm.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            let table = $('#userTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,

                ajax: {
                    url: "{{ route('staff.index') }}",
                    data: function(d) {
                        d.date_range = $('#rangeCalendar').val(); // date filter
                    }
                },

                columns: [{
                        data: 'id',
                        className: 'text-center fw-bold'
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
                        data: 'created_at'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    } // buttons
                ],

                order: [
                    [0, 'desc']
                ],

                language: {
                    search: "",
                    searchPlaceholder: "Search...",
                    lengthMenu: "Show _MENU_",
                    zeroRecords: "No matching records found",
                }
            });

            // Search button
            $('#filterSearchBtn').on('click', function() {
                table.draw();
            });

            // Reset button
            $('#resetFilterBtn').on('click', function() {
                $('#rangeCalendar').flatpickr().clear(); // clear date
                table.draw();
            });

            // Filter button
            $('#filterSearchBtn').on('click', function() {
                table.draw();
            });

            $('#resetFilterBtn').on('click', function() {
                // Clear Flatpickr input
                $('#rangeCalendar').flatpickr().clear();
                // Redraw table
                table.draw();
            });



        });
    </script>
@endpush
