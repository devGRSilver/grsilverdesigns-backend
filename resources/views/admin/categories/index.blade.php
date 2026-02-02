@extends('layouts.admin')

@section('content')
    <div class="app-content-area">
        <div class="app-content-wrap">
            <div class="container-fluid">

                <!-- Page Header -->
                <div class="row mb-3">
                    <div class="col-xl-12">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h1 class="fs-18 mb-0">{{ $title ?? 'Categories' }}</h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb breadcrumb-example1 mb-0">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.dashboard') }}">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active">{{ $resourceName ?? 'Categories' }}</li>
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
                                    <!-- Status Filter -->
                                    <div class="col-md-2">
                                        <label class="form-label">Status</label>
                                        <select id="filterStatus" class="form-control filter-input">
                                            <option value="">ALL</option>
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>



                                    <!-- Reset Button -->
                                    <div class="col-md-2 d-grid">
                                        <button id="resetFilterBtn" type="button"
                                            class="btn btn-outline-secondary btn-md fw-bold">
                                            <i class="ri-refresh-line me-1 fs-5"></i> Reset
                                        </button>
                                    </div>

                                    <!-- Add Button -->
                                    <div class="col text-end">
                                        @can('categories.create')
                                            <a href="{{ route($resource . '.create') }}"
                                                class="btn btn-primary btn-md px-4 fw-bold modal_open">
                                                Add {{ $resourceName ?? 'Category' }}
                                            </a>
                                        @endcan
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
                                        <thead class="table-light sticky-top">
                                            <tr>
                                                <th>#</th>
                                                <th>Icon</th>
                                                <th>Banner</th>
                                                <th>Name</th>
                                                <th>Slug</th>
                                                <th>Status</th>
                                                <th>Sub Category</th>
                                                <th>Updated</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>

                                <!-- Information Message -->
                                <div class="alert alert-primary d-flex align-items-start gap-2 mt-3" role="alert">
                                    <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                                    <div>
                                        <strong>Attention:</strong>
                                        This page displays only the main categories.
                                        To view subcategories, click on a main category count.
                                        After selecting a main category, all related subcategories will become visible.
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
        $(document).ready(function() {
            /* -------------------------------------
             * Initialize DataTable
             * ------------------------------------- */
            let table = $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                pagingType: "simple_numbers",
                ajax: {
                    url: "{{ route($resource . '.index') }}",
                    data: function(d) {
                        d.status = $('#filterStatus').val();
                        d.parent_id = $('#filterParent').val();
                    }
                },
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'image',
                        orderable: false
                    },
                    {
                        data: 'banner_image',
                        orderable: false
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'slug'
                    },
                    {
                        data: 'status'
                    },
                    {
                        data: 'sub_categories_count'
                    },
                    {
                        data: 'updated_at'
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

            /* -------------------------------------
             * Filter Change Handler
             * ------------------------------------- */
            $('.filter-input').on('change', function() {
                table.ajax.reload();
            });

            /* -------------------------------------
             * Select2 Initialization
             * ------------------------------------- */
            $('.select2').select2({
                width: "100%",
                placeholder: "Select",
                allowClear: true
            });

            /* -------------------------------------
             * Reset Filters
             * ------------------------------------- */
            $('#resetFilterBtn').on('click', function() {
                $('#filterStatus').val('');
                $('#filterParent').val('').trigger('change');
                table.ajax.reload();
            });


        });
    </script>
@endpush
