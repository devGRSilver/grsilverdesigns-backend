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

                                    <li class="breadcrumb-item">
                                        <a href="{{ route('roles.index') }}">Roles </a>
                                    </li>


                                    <li class="breadcrumb-item active"> Details </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>


                <div class="row g-4">
                    <!-- LEFT COLUMN: ROLE INFO & PERMISSIONS -->
                    <div class="col-lg-8">
                        <!-- Role Information Card -->
                        <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                            <div class="card-header bg-gradient-primary text-white">
                                <div class="d-flex align-items-center m-3">
                                    <div>
                                        <h5 class="mb-0 fw-bold text-white">Role Information</h5>
                                        <small class="opacity-75">Basic details about this role</small>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="text-muted small mb-2">Role Name</label>
                                        <div class="fw-bold fs-5">{{ $role->display_name }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small mb-2">System Name</label>
                                        <div class="fw-semibold">
                                            <code class="bg-light px-2 py-1 rounded">{{ $role->name }}</code>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small mb-2">Created At</label>
                                        <div class="fw-semibold">
                                            <i class="bi bi-calendar-event me-2 text-primary"></i>
                                            {{ $role->created_at->format('M d, Y') }}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small mb-2">Last Updated</label>
                                        <div class="fw-semibold">
                                            <i class="bi bi-clock-history me-2 text-primary"></i>
                                            {{ $role->updated_at->format('M d, Y') }}
                                        </div>
                                    </div>
                                    @if ($role->description)
                                        <div class="col-12">
                                            <label class="text-muted small mb-2">Description</label>
                                            <div class="fw-semibold">{{ $role->description }}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Permissions Card -->
                        <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                            <div class="card-header bg-gradient-success text-white py-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center m-3">

                                        <div>
                                            <h5 class="mb-0 fw-bold text-white">Assigned Permissions</h5>
                                            <small class="opacity-75">Permissions granted to this role</small>
                                        </div>
                                    </div>
                                    <span class="badge bg-white text-success fw-bold fs-6 px-3 py-2">
                                        {{ $rolePermissions->count() }} Permissions
                                    </span>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                @if ($rolePermissions->isEmpty())
                                    <div class="text-center py-5">
                                        <div class="mb-3">
                                            <i class="bi bi-shield-x display-1 text-muted opacity-25"></i>
                                        </div>
                                        <h6 class="text-muted mb-2">No Permissions Assigned</h6>
                                        <p class="text-muted small mb-0">
                                            This role doesn't have any permissions yet.
                                        </p>
                                    </div>
                                @else
                                    @foreach ($groups as $group)
                                        @php
                                            $groupPermissions = $group->permissions->whereIn(
                                                'id',
                                                $rolePermissions->pluck('id'),
                                            );
                                        @endphp

                                        @if ($groupPermissions->isNotEmpty())
                                            <div class="permission-group mb-4">
                                                <div class="d-flex align-items-center gap-3 mb-3">

                                                    <h6 class="fw-bold text-dark mb-0">
                                                        {{ $group->display_name ?? $group->name }}
                                                    </h6>
                                                    <span class="badge bg-primary-subtle text-primary">
                                                        {{ $groupPermissions->count() }}
                                                    </span>
                                                </div>

                                                <div class="row g-3">
                                                    @foreach ($groupPermissions as $permission)
                                                        <div class="col-md-6">
                                                            <div class="permission-card border rounded-3 h-100 bg-white">
                                                                <div class="card-body p-3">
                                                                    <div class="d-flex align-items-start gap-3">
                                                                        <div class="mt-1">
                                                                            <i
                                                                                class="bi bi-check-circle-fill text-success fs-5"></i>
                                                                        </div>
                                                                        <div class="flex-grow-1">
                                                                            <div class="fw-semibold mb-1">
                                                                                {{ $permission->display_name ?? $permission->name }}
                                                                            </div>
                                                                            <small class="text-muted">
                                                                                {{ $permission->description ?? 'No description available' }}
                                                                            </small>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT COLUMN: QUICK STATS & ACTIONS -->
                    <div class="col-lg-4">
                        <div class="sticky-top" style="top: 20px;">
                            <!-- Statistics Card -->
                            <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4">
                                <div class="card-header py-4" style="background: #cce1df">
                                    <div class="d-flex align-items-center m-3">

                                        <div>
                                            <h5 class="mb-0 fw-bold">Statistics</h5>
                                            <small class="opacity-75">Role overview</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-4">
                                    <div class="stat-item mb-4 pb-4 border-bottom">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="text-muted small mb-1">Total Permissions</div>
                                                <div class="fs-3 fw-bold text-primary">{{ $rolePermissions->count() }}
                                                </div>
                                            </div>
                                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                                <i class="bi bi-shield-check fs-4 text-primary"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="stat-item mb-4 pb-4 border-bottom">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="text-muted small mb-1">Permission Groups</div>
                                                <div class="fs-3 fw-bold text-success">
                                                    @php
                                                        $groupCount = 0;
                                                        foreach ($groups as $group) {
                                                            if (
                                                                $group->permissions
                                                                    ->whereIn('id', $rolePermissions->pluck('id'))
                                                                    ->isNotEmpty()
                                                            ) {
                                                                $groupCount++;
                                                            }
                                                        }
                                                    @endphp
                                                    {{ $groupCount }}
                                                </div>
                                            </div>
                                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                                <i class="bi bi-folder2-open fs-4 text-success"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="stat-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="text-muted small mb-1">Coverage</div>
                                                <div class="fs-3 fw-bold text-warning">
                                                    {{ $totalPermissions > 0 ? round(($rolePermissions->count() / $totalPermissions) * 100) : 0 }}%
                                                </div>
                                            </div>
                                            <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                                <i class="bi bi-pie-chart fs-4 text-warning"></i>
                                            </div>
                                        </div>
                                        <div class="progress mt-3" style="height: 8px;">
                                            <div class="progress-bar bg-warning" role="progressbar"
                                                style="width: {{ $totalPermissions > 0 ? round(($rolePermissions->count() / $totalPermissions) * 100) : 0 }}%">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions Card -->
                            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                                <div class="card-header" style="background: #cce1df">
                                    <div class="d-flex align-items-center m-3">

                                        <div>
                                            <h5 class="mb-0 fw-bold">Quick Actions</h5>
                                            <small class="opacity-75">Manage this role</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-4">
                                    <div class="d-grid gap-3">
                                        <a href="{{ route('roles.edit', encrypt($role->id)) }}"
                                            class="btn btn-primary btn-lg fw-semibold shadow-sm mb-4">
                                            <i class="bi bi-pencil-square me-2"></i>
                                            Edit Role
                                        </a>

                                        <a href="{{ route('roles.index') }}"
                                            class="btn btn-outline-secondary btn-lg mb-4">
                                            <i class="bi bi-arrow-left me-2"></i>
                                            Back to List
                                        </a>


                                    </div>

                                    <div class="alert alert-info mt-4 mb-0">
                                        <div class="d-flex align-items-start gap-2">
                                            <i class="bi bi-info-circle mt-1"></i>
                                            <small>
                                                This role was last updated {{ $role->updated_at->diffForHumans() }}.
                                            </small>
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

    <!-- Delete Confirmation Form -->
    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('scripts')
    <script>
        function confirmDelete(url) {
            if (confirm('Are you sure you want to delete this role? This action cannot be undone.')) {
                const form = document.getElementById('delete-form');
                form.action = url;
                form.submit();
            }
        }
    </script>

    <style>
        .permission-card {
            transition: all 0.3s ease;
            border: 1px solid #e9ecef;
        }

        .permission-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-color: #0d6efd;
        }

        .permission-group {
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .permission-group:last-child {
            border-bottom: none;
        }

        .stat-item {
            transition: all 0.3s ease;
        }

        .stat-item:hover {
            transform: translateX(5px);
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
    </style>
@endpush
