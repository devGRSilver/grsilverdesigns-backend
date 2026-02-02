@extends('layouts.admin')

@section('content')
    <div class="app-content-area">
        <div class="app-content-wrap">
            <div class="container-fluid">

                <!-- Page Header -->

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

                                <div class="row align-items-end">
                                    <div class="col-md-2">
                                        <label class="form-label">Status </label>
                                        <select name="status" class="form-control filter-input" id="filterStatus">
                                            <option value="">ALL</option>
                                            <option value="1">Active</option>
                                            <option value="0">In Active</option>
                                        </select>
                                    </div>
                                    @can($resource . '.create')
                                        <div class="col-md-3 ms-auto text-end">
                                            <a href="{{ route($resource . '.create') }}"
                                                class="btn btn-primary btn-sm px-4 modal_open">
                                                Add {{ $resourceName ?? 'Attribute' }}
                                            </a>
                                        </div>
                                    @endcan


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
                                    <table id="dataTable" class="table table-bordered table-hover align-middle w-100">
                                        <thead class="table-light sticky-top">
                                            <tr>
                                                <th>#</th>
                                                <th> Name </th>
                                                <th>Slug</th>
                                                <th>Status</th>
                                                <th>Values</th>
                                                <th>Updated at</th>
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
                        d.metal_id = $('#filterMetal').val();
                        d.parent_id = $('#filterParent').val();
                        d.is_primary = $('#filterPrimary').val();
                        d.status = $('#filterStatus').val();
                    }
                },
                columns: [{
                        data: 'id'
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
                        data: 'attribute_value'
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
                placeholder: "Select Option",
                allowClear: true
            });


            // On click
            $(document).on('click', '.show_child_category', function() {
                let categoryId = $(this).data('id');
                $('#filterParent').val(categoryId).trigger('change');
            });



        });
    </script>
@endpush
