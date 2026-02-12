@extends('layouts.admin')

@section('content')
    <div class="app-content-area">
        <div class="app-content-wrap">
            <div class="container-fluid">
                {{-- Page Header --}}




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

                                    <li class="breadcrumb-item">
                                        <a href="{{ route('users.index') }}">Users </a>
                                    </li>


                                    <li class="breadcrumb-item active">Details </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>




                <div class="row">
                    {{-- Left Sidebar: User Profile Card --}}
                    <div class="col-xl-3 col-lg-4 mb-4">
                        <div class="card border-0 shadow-sm sticky-top">
                            {{-- Profile Header --}}
                            <div class="card-body text-center border-bottom py-4">
                                <div class="position-relative d-inline-block mb-3">
                                    <img src="{{ $data->profile_picture ?? URL::asset('default_images/no_user.png') }}"
                                        class="rounded-circle border border-3 border-white shadow" alt="{{ $data->name }}"
                                        style="width: 120px; height: 120px; object-fit: cover;"
                                        onerror="this.src='{{ URL::asset('default_images/no_user.png') }}'">
                                    @php
                                        $statusColors = [
                                            'active' => 'success',
                                            'inactive' => 'danger',
                                            'pending' => 'warning',
                                            'suspended' => 'secondary',
                                        ];
                                        $statusColor = $statusColors[$data->status] ?? 'secondary';
                                    @endphp
                                    <span
                                        class="position-absolute bottom-0 end-0 p-2 bg-{{ $statusColor }} border border-3 border-white rounded-circle"
                                        title="{{ ucfirst($data->status) }}" style="width: 20px; height: 20px;">
                                    </span>
                                </div>
                                <h4 class="mb-2 fw-bold">{{ $data->name }}</h4>
                                <p class="text-muted mb-3 small">{{ $data->email }}</p>
                                <span class="badge bg-{{ $statusColor }}-subtle text-{{ $statusColor }} px-3 py-2">
                                    {{ ucfirst($data->status) }}
                                </span>
                            </div>

                            {{-- Quick Info --}}
                            <div class="card-body py-4">
                                <h6 class="text-uppercase text-muted fw-bold mb-3"
                                    style="font-size: 11px; letter-spacing: 0.5px;">
                                    Account Information
                                </h6>
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-3 pb-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <div class="avatar avatar-sm bg-primary-subtle text-primary rounded">
                                                    <i class="ri-hashtag"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <p class="text-muted mb-0" style="font-size: 12px;">User ID</p>
                                                <p class="mb-0 fw-semibold">#{{ $data->id }}</p>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="mb-3 pb-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <div class="avatar avatar-sm bg-success-subtle text-success rounded">
                                                    <i class="ri-phone-line"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <p class="text-muted mb-0" style="font-size: 12px;">Phone</p>
                                                <p class="mb-0 fw-semibold">
                                                    {{ $data->phonecode && $data->phone ? $data->phonecode . ' ' . $data->phone : 'N/A' }}
                                                </p>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="mb-3 pb-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <div class="avatar avatar-sm bg-info-subtle text-info rounded">
                                                    <i class="ri-global-line"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <p class="text-muted mb-0" style="font-size: 12px;">Timezone</p>
                                                <p class="mb-0 fw-semibold">{{ $data->timezone ?? 'UTC' }}</p>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="mb-3 pb-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <div class="avatar avatar-sm bg-warning-subtle text-warning rounded">
                                                    <i class="ri-calendar-line"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <p class="text-muted mb-0" style="font-size: 12px;">Joined</p>
                                                <p class="mb-0 fw-semibold">{{ $data->created_at->format('M d, Y') }}</p>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="mb-3 pb-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <div class="avatar avatar-sm bg-danger-subtle text-danger rounded">
                                                    <i class="ri-time-line"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <p class="text-muted mb-0" style="font-size: 12px;">Last Login</p>
                                                <p class="mb-0 fw-semibold">
                                                    {{ $data->last_login_at ? $data->last_login_at->diffForHumans() : 'Never' }}
                                                </p>
                                            </div>
                                        </div>
                                    </li>
                                    @if ($data->ip_address)
                                        <li>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <div
                                                        class="avatar avatar-sm bg-secondary-subtle text-secondary rounded">
                                                        <i class="ri-map-pin-line"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <p class="text-muted mb-0" style="font-size: 12px;">IP Address</p>
                                                    <p class="mb-0 fw-semibold">{{ $data->ip_address }}</p>
                                                </div>
                                            </div>
                                        </li>
                                    @endif
                                </ul>
                            </div>

                            {{-- Quick Actions --}}
                            <div class="card-body border-top py-4">
                                <h6 class="text-uppercase text-muted fw-bold mb-3"
                                    style="font-size: 11px; letter-spacing: 0.5px;">
                                    Quick Actions
                                </h6>
                                <div class="d-grid gap-2">
                                    @can('users.update')
                                        <button href="{{ route('users.edit', encrypt($data->id)) }}"
                                            class="btn btn-outline-primary btn-sm modal_open">
                                            <i class="ri-edit-line me-1"></i> Edit Profile
                                        </button>
                                    @endcan
                                    <a href="mailto:{{ $data->email }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="ri-mail-line me-1"></i> Send Email
                                    </a>
                                    <a href="{{ route('users.password.edit', encrypt($data->id)) }}"
                                        class="btn btn-outline-info btn-sm modal_open">
                                        <i class="ri-shopping-bag-line me-1"></i> Reset Password
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Main Content: Stats & Tabs --}}
                    <div class="col-xl-9 col-lg-8">
                        {{-- Stats Cards --}}
                        <div class="row g-3 mb-4" id="stats-wrapper">
                            <div class="col-xl-3 col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div>
                                                <p class="text-muted mb-2" style="font-size: 13px;">Total Orders</p>
                                                <h3 class="mb-0 fw-bold" id="stat-total-orders">
                                                    <span class="placeholder col-6 placeholder-wave"></span>
                                                </h3>
                                            </div>
                                            <div class="avatar avatar-md bg-primary-subtle text-primary rounded-3">
                                                <i class="ri-shopping-bag-3-line fs-20"></i>
                                            </div>
                                        </div>
                                        <a href="{{ route('orders.index') }}?user_id={{ $data->id }}"
                                            class="text-primary text-decoration-none small d-flex align-items-center">
                                            View Details <i class="ri-arrow-right-line ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div>
                                                <p class="text-muted mb-2" style="font-size: 13px;">Processing</p>
                                                <h3 class="mb-0 fw-bold" id="stat-processing">
                                                    <span class="placeholder col-6 placeholder-wave"></span>
                                                </h3>
                                            </div>
                                            <div class="avatar avatar-md bg-warning-subtle text-warning rounded-3">
                                                <i class="ri-loader-4-line fs-20"></i>
                                            </div>
                                        </div>
                                        <a href="{{ route('orders.index') }}?user_id={{ $data->id }}&status=processing"
                                            class="text-warning text-decoration-none small d-flex align-items-center">
                                            View Orders <i class="ri-arrow-right-line ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div>
                                                <p class="text-muted mb-2" style="font-size: 13px;">Completed</p>
                                                <h3 class="mb-0 fw-bold" id="stat-completed">
                                                    <span class="placeholder col-6 placeholder-wave"></span>
                                                </h3>
                                            </div>
                                            <div class="avatar avatar-md bg-success-subtle text-success rounded-3">
                                                <i class="ri-check-double-line fs-20"></i>
                                            </div>
                                        </div>
                                        <a href="{{ route('orders.index') }}?user_id={{ $data->id }}&status=delivered"
                                            class="text-success text-decoration-none small d-flex align-items-center">
                                            View Orders <i class="ri-arrow-right-line ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div>
                                                <p class="text-muted mb-2" style="font-size: 13px;">Total Spent</p>
                                                <h3 class="mb-0 fw-bold" id="stat-total-spent">
                                                    <span class="placeholder col-8 placeholder-wave"></span>
                                                </h3>
                                            </div>
                                            <div class="avatar avatar-md bg-info-subtle text-info rounded-3">
                                                <i class="ri-money-dollar-circle-line fs-20"></i>
                                            </div>
                                        </div>
                                        <p class="text-muted mb-0 small">Lifetime value</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Navigation Tabs --}}
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body p-0">
                                <ul class="nav nav-pills nav-justified mb-0" id="userDetailTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button
                                            class="nav-link active rounded-0 border-bottom border-3 border-primary py-3"
                                            id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button"
                                            role="tab">
                                            <i class="ri-shopping-bag-line me-2"></i>
                                            <span class="d-none d-sm-inline">Orders</span>
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link rounded-0 border-bottom border-3 border-transparent py-3"
                                            id="transactions-tab" data-bs-toggle="tab" data-bs-target="#transactions"
                                            type="button" role="tab">
                                            <i class="ri-exchange-dollar-line me-2"></i>
                                            <span class="d-none d-sm-inline">Transactions</span>
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link rounded-0 border-bottom border-3 border-transparent py-3"
                                            id="activity-tab" data-bs-toggle="tab" data-bs-target="#activity"
                                            type="button" role="tab">
                                            <i class="ri-history-line me-2"></i>
                                            <span class="d-none d-sm-inline">Activity</span>
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        {{-- Tab Content --}}
                        <div class="tab-content" id="userDetailTabsContent">
                            {{-- Orders Tab --}}
                            <div class="tab-pane fade show active" id="orders" role="tabpanel">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-white border-bottom py-3">
                                        <h5 class="mb-0 fw-bold">All Orders</h5>
                                    </div>
                                    <div class="card-body p-4">
                                        <div class="table-responsive">
                                            <table class="table table-hover" id="ordersTable">
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
                                                <tbody>
                                                    <tr>
                                                        <td colspan="7" class="text-center py-5">
                                                            <div class="spinner-border text-primary" role="status">
                                                                <span class="visually-hidden">Loading...</span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Transactions Tab --}}
                            <div class="tab-pane fade" id="transactions" role="tabpanel">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-white border-bottom py-3">
                                        <h5 class="mb-0 fw-bold">Transaction History</h5>
                                    </div>
                                    <div class="card-body p-4">
                                        <div class="table-responsive">
                                            <table class="table table-hover" id="transactionsTable">
                                                <thead class="table-light">
                                                    <tr>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Transaction ID</th>
                                                        <th>Order ID</th>
                                                        <th>User</th>
                                                        <th>Amount</th>
                                                        <th>Status</th>
                                                        <th>Created</th>
                                                        <th>Action</th>
                                                    </tr>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td colspan="8" class="text-center py-5">
                                                            <div class="spinner-border text-primary" role="status">
                                                                <span class="visually-hidden">Loading...</span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Activity Tab --}}
                            <div class="tab-pane fade" id="activity" role="tabpanel">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-white border-bottom py-3">
                                        <h5 class="mb-0 fw-bold">Activity Log</h5>
                                    </div>
                                    <div class="card-body p-4">
                                        <div id="activityLog">
                                            <div class="text-center py-5">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
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

    {{-- Edit User Modal --}}
    @can('users.update')
        <div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit User Information</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="editUserForm" action="{{ route('users.update', encrypt($data->id)) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body p-4">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Full Name *</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ $data->name }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address *</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="{{ $data->email }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phonecode" class="form-label">Phone Code</label>
                                    <input type="text" class="form-control" id="phonecode" name="phonecode"
                                        value="{{ $data->phonecode }}" placeholder="+1">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone"
                                        value="{{ $data->phone }}" placeholder="1234567890">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="timezone" class="form-label">Timezone</label>
                                    <select class="form-select" id="timezone" name="timezone">
                                        @foreach (timezone_identifiers_list() as $tz)
                                            <option value="{{ $tz }}"
                                                {{ $data->timezone == $tz ? 'selected' : '' }}>{{ $tz }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="currency" class="form-label">Currency</label>
                                    <select class="form-select" id="currency" name="currency">
                                        <option value="USD" {{ $data->currency == 'USD' ? 'selected' : '' }}>USD ($)
                                        </option>
                                        <option value="INR" {{ $data->currency == 'INR' ? 'selected' : '' }}>INR (₹)
                                        </option>
                                        <option value="EUR" {{ $data->currency == 'EUR' ? 'selected' : '' }}>EUR (€)
                                        </option>
                                        <option value="GBP" {{ $data->currency == 'GBP' ? 'selected' : '' }}>GBP (£)
                                        </option>
                                    </select>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="status" class="form-label">Account Status</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="active" {{ $data->status == 'active' ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="inactive" {{ $data->status == 'inactive' ? 'selected' : '' }}>Inactive
                                        </option>
                                        <option value="pending" {{ $data->status == 'pending' ? 'selected' : '' }}>Pending
                                        </option>
                                        <option value="suspended" {{ $data->status == 'suspended' ? 'selected' : '' }}>
                                            Suspended</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line me-1"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    {{-- Password Reset Modal --}}
    @can('users.reset.password')
        <div class="modal fade" id="passwordModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Reset Password</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="changePasswordForm" action="{{ route('users.password.update', encrypt($data->id)) }}"
                        method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label for="password" class="form-label">New Password *</label>
                                <input type="password" class="form-control" id="password" name="password" required
                                    minlength="8">
                            </div>
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password *</label>
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-lock-line me-1"></i> Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
@endsection



@push('style')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/plugins/dataTables.dataTables.css') }}">
@endpush


@push('scripts')
    <script src="{{ asset('assets/admin/js/plugins/dataTables.js') }}"></script>

    <script>
        $(document).ready(function() {
            const userId = '{{ $data->id }}';
            const currency = '{{ $data->currency ?? '$' }}';
            const csrfToken = $('meta[name="csrf-token"]').attr('content');

            // Initialize DataTables
            let ordersTable = null;
            let transactionsTable = null;

            // Load initial data
            loadUserStats();
            initOrdersTable();

            // Tab change handler
            $('#userDetailTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                const target = $(e.target).attr('data-bs-target');

                if (target === '#transactions') {
                    if (transactionsTable) {
                        transactionsTable.draw(); // Refresh existing table
                    } else {
                        initTransactionsTable(); // Initialize if not exists
                    }
                } else if (target === '#orders') {
                    if (ordersTable) {
                        ordersTable.draw();
                    }
                } else if (target === '#activity') {
                    loadActivityLog();
                }
            });
            // Load user statistics
            function loadUserStats() {
                $.ajax({
                    url: `/admin/users/${userId}/stats`,
                    type: 'GET',
                    data: {
                        user_id: userId
                    },
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(data) {
                        if (data.status) {
                            $('#stat-total-orders').html(data.response.total_orders || 0);
                            $('#stat-processing').html(data.response.processing_orders || 0);
                            $('#stat-completed').html(data.response.delivered_orders || 0);
                            $('#stat-total-spent').html(data.response.total_spent || 0);
                        }
                    },
                    error: function() {
                        setDefaultStats();
                    }
                });
            }

            function setDefaultStats() {
                $('#stat-total-orders, #stat-processing, #stat-completed').html('0');
                $('#stat-total-spent').html(formatCurrency(0));
            }

            // Initialize Orders DataTable
            function initOrdersTable() {
                ordersTable = $('#ordersTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: `/admin/users/${userId}/orders`,
                        type: 'GET',
                        data: function(d) {
                            d.user_id = userId;
                        },
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
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
                        [1, 'desc']
                    ],
                    pageLength: 10,
                    language: {
                        emptyTable: "No orders found",
                        zeroRecords: "No matching orders found",
                        search: "Search orders..."
                    }
                });
            }

            // Initialize Transactions DataTable
            function initTransactionsTable() {
                transactionsTable = $('#transactionsTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: `/admin/users/${userId}/transactions`,
                        type: 'GET',
                        data: function(d) {
                            d.user_id = userId;
                        },
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
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
                        [1, 'desc']
                    ],
                    pageLength: 10,
                    language: {
                        emptyTable: "No transactions found",
                        zeroRecords: "No matching transactions found",
                        search: "Search transactions..."
                    }
                });
            }

            // Load activity log
            function loadActivityLog() {
                $.ajax({
                    url: `/admin/users/${userId}/activity`,
                    type: 'GET',
                    data: function(d) {
                        d.user_id = userId;
                    },
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(response) {
                        const container = $('#activityLog');
                        if (response.success && response.data && response.data.length > 0) {
                            let html = '<div class="activity-timeline">';
                            response.data.forEach(function(activity) {
                                html += `
                                    <div class="activity-item mb-3">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <div class="avatar avatar-sm bg-light rounded">
                                                    <i class="ri-user-line"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <p class="mb-1">${activity.description || 'Activity'}</p>
                                                <p class="text-muted small mb-0">${formatDateTime(activity.created_at)}</p>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            });
                            html += '</div>';
                            container.html(html);
                        } else {
                            container.html(`
                                <div class="text-center text-muted py-5">
                                    <i class="ri-inbox-line fs-1 d-block mb-2"></i>
                                    <p class="mb-0">No activity found</p>
                                </div>
                            `);
                        }
                    },
                    error: function() {
                        $('#activityLog').html(`
                            <div class="text-center text-danger py-5">
                                <i class="ri-error-warning-line fs-1 d-block mb-2"></i>
                                <p class="mb-0">Failed to load activity</p>
                            </div>
                        `);
                    }
                });
            }



        });
    </script>
@endpush
