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
                                    <li class="breadcrumb-item active" aria-current="page">{{ $resource }}</li>
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

                                    <!-- Parent Category Filter -->
                                    <div class="col-md-2">
                                        <label for="filterParent" class="form-label">Parent Category</label>
                                        <select name="parent_id" id="filterParent"
                                            class="form-control select2 filter-input">
                                            <option value="">-- All Categories --</option>
                                            @foreach ($categories ?? [] as $cat)
                                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Sub Category Filter -->
                                    <div class="col-md-2">
                                        <label for="subCategory" class="form-label">Sub Category</label>
                                        <select name="sub_category_id" id="subCategory"
                                            class="form-control select2 sub_category filter-input" required>
                                            <option value="">— Select Sub Category —</option>
                                        </select>
                                    </div>

                                    <!-- Primary Category Filter -->
                                    <div class="col-md-2">
                                        <label for="filterStock" class="form-label"> Stock </label>
                                        <select name="stock_quantity" id="stock_quantity" class="form-control filter-input">
                                            <option value="">ALL</option>
                                            <option value="1">In Stock</option>
                                            <option value="0">Out Of Stock</option>
                                        </select>
                                    </div>

                                    <!-- Status Filter -->
                                    <div class="col-md-2">
                                        <label for="filterStatus" class="form-label">Status</label>
                                        <select name="status" id="filterStatus" class="form-control filter-input">
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
                                    @php
                                        $uid = Str::random(32);
                                    @endphp

                                    <!-- Add Button -->
                                    <div class="col-md-2 text-end">
                                        @can('products.create')
                                            <a href="{{ route($resource . '.create', ['uid' => $uid]) }}"
                                                class="btn btn-primary btn-md px-4 fw-bold">
                                                Add {{ $resourceName ?? 'Product' }}
                                            </a>
                                        @endcan
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Categories Table -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="dataTable" class="table table-bordered table-hover dt-responsive">
                                        <thead class="table-light sticky-top">
                                            <tr>
                                                <th>#</th>
                                                <th>Image</th>
                                                <th>Title</th>
                                                <th>Category</th>
                                                <th>Total Variant</th>
                                                <th>Status</th>
                                                <th>Published</th>
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
    <link rel="stylesheet" href="{{ asset('assets/admin/css/plugins/jquery-confirm.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/plugins/select2.min.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('assets/admin/js/plugins/dataTables.js') }}"></script>
    <script src="{{ asset('assets/admin/js/plugins/jquery-confirm.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/plugins/select2.min.js') }}"></script>

    <script>
        $(document).ready(function() {

            // Initialize Select2
            $('.select2').select2({
                width: "100%",
                placeholder: "Select Option",
                allowClear: true
            });

            // Initialize DataTable
            let table = $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                pagingType: "simple_numbers",
                ajax: {
                    url: "{{ route($resource . '.index') }}",
                    data: function(d) {
                        d.category_id = $('#filterParent').val();
                        d.sub_category_id = $('.sub_category').val();
                        d.stock_quantity = $('#stock_quantity').val();
                        d.status = $('#filterStatus').val();
                    }
                },
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'image'
                    },
                    {
                        data: 'product_name'
                    },
                    {
                        data: 'category'
                    },
                    {
                        data: 'total_variant',
                        className: "text-start"
                    },
                    {
                        data: 'status'
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

            // Reload table on filter change
            $('.filter-input').on('change', function() {
                table.ajax.reload();
            });



            $('#resetFilterBtn').on('click', function() {
                $('.filter-input').each(function() {
                    $(this).val('').trigger('change'); // reset value and trigger select2 if used
                });

                // Redraw the datatable
                if (typeof table !== 'undefined' && table.draw) {
                    table.draw();
                }
            });

            // Load subcategories dynamically
            $('#filterParent').on('change', function() {
                let categoryId = $(this).val();
                let $sub = $('.sub_category');

                if (!categoryId) {
                    $sub.html('<option value="">— Select Sub Category —</option>');
                    return;
                }

                $sub.html('<option>Loading...</option>');
                let url = "{{ route('subcategories.ajax', ':id') }}".replace(':id', categoryId);

                $.get(url, function(data) {
                    $sub.html('<option value="">— Select Sub Category —</option>');
                    $.each(data, function(_, item) {
                        $sub.append(`<option value="${item.id}">${item.name}</option>`);
                    });
                }).fail(function() {
                    $sub.html('<option>Error loading data</option>');
                });
            });

            // Optional: Show child category from button
            $(document).on('click', '.show_child_category', function() {
                let categoryId = $(this).data('id');
                $('#filterParent').val(categoryId).trigger('change');
            });

        });
    </script>
@endpush
