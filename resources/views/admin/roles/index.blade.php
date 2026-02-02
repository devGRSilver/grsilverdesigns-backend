@extends('layouts.admin')

@section('content')
    <div class="app-content-area">
        <div class="app-content-wrap">
            <div class="container-fluid">

                <!-- Page Header -->
                <div class="row mb-3">
                    <div class="col-xl-12">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h1 class="fs-18 mb-0">{{ $title ?? '' }}</h1>

                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb breadcrumb-example1 mb-0">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.dashboard') }}">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active">{{ $resource }}</li>
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
                                    <!-- Add Button -->
                                    <div class="col text-end">
                                        <a href="{{ route($resource . '.create') }}" class="btn btn-primary px-4 fw-bold">
                                            <i class="ri-add-line me-1"></i>
                                            Add {{ $resourceName ?? 'Role' }}
                                        </a>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="dataTable" class="table table-bordered table-hover align-middle w-100">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Role Name</th>
                                                <th class="text-start">Total Permissions</th>
                                                <th class="text-start">Associate User</th>
                                                <th class="text-start">Status</th>
                                                <th>Updated</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                    </table>

                                    <!-- ATTENTION MESSAGE -->
                                    <div class="alert alert-success d-flex align-items-start gap-2 mt-3" role="alert">
                                        <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                                        <div>
                                            <strong>Attention:</strong>
                                            If you deactivate a role, all users with that role will be automatically logged
                                            out.
                                            Users assigned to this role will not be able to log in until the role is
                                            reactivated.
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
    <link rel="stylesheet" href="{{ asset('assets/admin/css/plugins/dataTables.dataTables.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/plugins/jquery-confirm.min.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('assets/admin/js/plugins/dataTables.js') }}"></script>
    <script src="{{ asset('assets/admin/js/plugins/jquery-confirm.min.js') }}"></script>

    <script>
        $(function() {

            let table = $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                pagingType: "simple_numbers",

                ajax: {
                    url: "{{ route($resource . '.index') }}",
                    data: function(d) {
                        d.status = $('#filterStatus').val();
                    }
                },

                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'display_name',
                        name: 'display_name'
                    },

                    {
                        data: 'total_permission',
                        name: 'total_permission',
                        class: 'text-start',
                        searchable: false
                    },


                    {
                        data: 'associated_users',
                        name: 'associated_users',
                        class: 'text-start',
                        searchable: false
                    },

                    {
                        data: 'status',
                        name: 'status',
                    },




                    {
                        data: 'updated_at',
                        name: 'updated_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ],

                order: [
                    [0, 'desc']
                ],

                language: {
                    search: "",
                    searchPlaceholder: "Search...",
                    lengthMenu: "Show _MENU_",
                    zeroRecords: "No records found",
                },

                drawCallback: function() {
                    $('[data-bs-toggle="tooltip"]').tooltip();
                }
            });

            /* Filter change */
            $('#filterStatus').on('change', function() {
                table.ajax.reload();
            });

            /* Reset */
            $('#resetFilterBtn').on('click', function() {
                $('.filter-input').val('');
                table.ajax.reload();
            });

            /* Delete */
            $(document).on('click', '.delete-btn', function(e) {
                e.preventDefault();

                let url = $(this).attr('href');

                $.confirm({
                    title: 'Confirm Delete',
                    content: 'Are you sure you want to delete this role?',
                    type: 'red',
                    buttons: {
                        confirm: {
                            btnClass: 'btn-danger',
                            action: function() {
                                $.ajax({
                                    url: url,
                                    type: 'DELETE',
                                    data: {
                                        _token: "{{ csrf_token() }}"
                                    },
                                    success: function(res) {
                                        if (res.success) {
                                            table.ajax.reload();
                                            toastr.success(res.message);
                                        }
                                    },
                                    error: function() {
                                        toastr.error('Delete failed');
                                    }
                                });
                            }
                        },
                        cancel: function() {}
                    }
                });
            });

        });
    </script>
@endpush
