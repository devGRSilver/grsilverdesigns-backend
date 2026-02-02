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
                                    <li class="breadcrumb-item active">Sub Category</li>
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

                                    <!-- Parent Category -->
                                    @canany(['categories.view.any', 'categories.view', 'categories.create'])
                                        <div class="col-md-2">
                                            <label class="form-label">Parent Category</label>
                                            <select id="filterParent" class="form-control select2 filter-input">
                                                <option value="">All</option>
                                                @foreach ($categories ?? [] as $cat)
                                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endcanany

                                    <!-- Primary -->
                                    @can('categories.view.any')
                                        <div class="col-md-2">
                                            <label class="form-label">Primary</label>
                                            <select id="filterPrimary" class="form-control filter-input">
                                                <option value="">ALL</option>
                                                <option value="1">YES</option>
                                                <option value="0">NO</option>
                                            </select>
                                        </div>
                                    @endcan

                                    <!-- Status -->
                                    @can('categories.view.any')
                                        <div class="col-md-2">
                                            <label class="form-label">Status</label>
                                            <select id="filterStatus" class="form-control filter-input">
                                                <option value="">ALL</option>
                                                <option value="1">Active</option>
                                                <option value="0">Inactive</option>
                                            </select>
                                        </div>
                                    @endcan

                                    <!-- Reset Button -->
                                    @can('categories.view.any')
                                        <div class="col-md-2 d-grid">
                                            <button id="resetFilterBtn" type="button"
                                                class="btn btn-outline-secondary btn-md fw-bold">
                                                <i class="ri-refresh-line me-1 fs-5"></i> Reset
                                            </button>
                                        </div>
                                    @endcan

                                    <!-- Add Button -->
                                    <div class="col text-end">
                                        @can('subcategories.create')
                                            <a href="{{ route('subcategories.create') }}"
                                                class="btn btn-primary btn-md px-4 fw-bold modal_open">
                                                Add Sub Category
                                            </a>
                                        @else
                                            @can('categories.view.any')
                                                <div class="text-muted small mt-2">No permission to add sub categories</div>
                                            @endcan
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
                                                @can('categories.view.any')
                                                    <th>Icon</th>
                                                    <th>Banner</th>
                                                @endcan
                                                <th>Name</th>
                                                <th>Main Category</th>
                                                <th>Slug</th>
                                                @can('categories.view.any')
                                                    <th class="text-start">Product Count</th>
                                                    <th>Status</th>
                                                    <th>Updated</th>
                                                @endcan
                                                @canany(['subcategories.view', 'subcategories.update',
                                                    'subcategories.delete'])
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
             * Initialize DataTable with Filters
             * ------------------------------------- */
            let table = $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                pagingType: "simple_numbers",

                ajax: {
                    url: "{{ route($resource . '.index') }}",
                    data: function(d) {
                        d.parent_id = $('#filterParent').val();
                        d.is_primary = $('#filterPrimary').val();
                        d.status = $('#filterStatus').val();
                    }
                },
                columns: [{
                        data: 'id'
                    },
                    @can('categories.view.any')
                        {
                            data: 'image',
                            orderable: false
                        }, {
                            data: 'banner_image',
                            orderable: false
                        },
                    @endcan {
                        data: 'name'
                    },
                    {
                        data: 'parent_name'
                    },
                    {
                        data: 'slug'
                    },
                    @can('categories.view.any')
                        {
                            data: 'product_count',
                            class: 'text-start'
                        }, {
                            data: 'status'
                        }, {
                            data: 'updated_at'
                        },
                    @endcan
                    @canany(['subcategories.view', 'subcategories.update', 'subcategories.delete'])
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
                    searchPlaceholder: "Search...",
                    lengthMenu: "Show _MENU_",
                    zeroRecords: "No matching records found"
                }
            });

            /* -------------------------------------
             * Auto Refresh on Filter Change
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

            $('#resetFilterBtn').on('click', function() {
                $('.filter-input').each(function() {
                    $(this).val('').trigger('change');
                });

                if (typeof table !== 'undefined' && table.draw) {
                    table.draw();
                }
            });

            function getUrlParam(name) {
                let params = new URLSearchParams(window.location.search);
                return params.get(name);
            }

            let parentId = getUrlParam('parent_id');
            if (parentId) {
                $('#filterParent')
                    .val(parentId)
                    .trigger('change');
            }

            $(document).on('click', '.show_child_category', function() {
                let categoryId = $(this).data('id');
                $('#filterParent').val(categoryId).trigger('change');
            });

        });
    </script>
@endpush
