@extends('layouts.admin')

@section('content')
    <div class="app-content-area">
        <div class="app-content-wrap">
            <div class="container-fluid">

                <!-- Page Header -->
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h1 class="fs-18 mb-0">{{ $title ?? 'Staff' }}</h1>

                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.dashboard') }}">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active">Staff</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="row">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row g-3 align-items-end">

                                    {{-- Role Filter --}}
                                    @if ($roles && $roles->count())
                                        <div class="col-md-3">
                                            <label class="form-label">Role</label>
                                            <select id="filterRole" class="form-control select2">
                                                <option value="">All</option>
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->id }}">
                                                        {{ $role->display_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif

                                    {{-- Buttons --}}
                                    <div class="col-md-7">
                                        <div class="d-flex gap-2">
                                            <button type="button" id="filterSearchBtn" class="btn btn-success">
                                                <i class="ri-search-line me-1"></i> Search
                                            </button>

                                            <button type="button" id="resetFilterBtn" class="btn btn-outline-secondary">
                                                <i class="ri-refresh-line me-1"></i> Reset
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Add Staff --}}
                                    <div class="col-md-2 text-end">
                                        <a href="{{ route('staff.create') }}" class="btn btn-primary w-100 modal_open">
                                            Add Staff
                                        </a>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="dataTable" class="table table-bordered table-hover align-middle w-100">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Mobile</th>
                                                <th>Email</th>
                                                <th>Status</th>
                                                <th>Role</th>
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
    <link rel="stylesheet" href="{{ asset('assets/admin/css/plugins/dataTables.dataTables.css') }}">
@endpush
@push('scripts')
    <script src="{{ asset('assets/admin/js/plugins/dataTables.js') }}"></script>

    <script>
        $(function() {



            /* DataTable */
            const table = $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                pagingType: "simple_numbers",

                ajax: {
                    url: "{{ route('staff.index') }}",
                    data: function(d) {
                        d.date_range = $('#rangeCalendar').val();
                        d.role_id = $('#filterRole').val();
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
                        data: 'role'
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

            /* Search */
            $('#filterSearchBtn').on('click', function() {
                table.draw();
            });

            /* Reset */
            $('#resetFilterBtn').on('click', function() {
                $('#filterRole').val('').trigger('change');
                table.draw();
            });

        });
    </script>
@endpush
