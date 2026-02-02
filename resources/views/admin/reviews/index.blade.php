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
                                    <li class="breadcrumb-item active">{{ $resourceName }}</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <div class="row g-3 align-items-end">

                                    <!-- Status -->
                                    <div class="col-md-2">
                                        <label class="form-label">Status</label>
                                        <select id="filterStatus" class="form-control filter-input">
                                            <option value="">ALL</option>
                                            <option value="1">Published</option>
                                            <option value="0">Unpublished</option>
                                        </select>
                                    </div>

                                    <!-- Reset -->
                                    <div class="col-md-2 d-grid">
                                        <button id="resetFilterBtn" type="button"
                                            class="btn btn-outline-secondary fw-bold">
                                            <i class="ri-refresh-line me-1"></i> Reset
                                        </button>
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
                                @cannot('reviews.view.any')
                                    <div class="alert alert-warning mb-0">
                                        <i class="ri-alert-line me-2"></i>You don't have permission to view reviews.
                                    </div>
                                @else
                                    <div class="table-responsive">
                                        <table id="dataTable" class="table table-bordered align-middle w-100">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>#</th>
                                                    <th>User</th>
                                                    <th>Rating</th>
                                                    <th>Comment</th>
                                                    <th>Status</th>
                                                    <th>Updated</th>
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
            // Only initialize DataTable if user has permission
            @can('reviews.view.any')
                let table = $('#dataTable').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,

                    ajax: {
                        url: "{{ route($resource . '.index') }}",
                        data: function(d) {
                            d.status = $('#filterStatus').val();
                        }
                    },

                    columns: [{
                            data: 'id'
                        },
                        {
                            data: 'user'
                        },
                        {
                            data: 'rating'
                        },
                        {
                            data: 'comment'
                        },
                        {
                            data: 'status',
                            orderable: false
                        },
                        {
                            data: 'updated_at'
                        },
                        {
                            data: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ],

                    order: [
                        [0, 'desc']
                    ],

                    language: {
                        search: "",
                        searchPlaceholder: "Search review...",
                        lengthMenu: "Show _MENU_",
                        zeroRecords: "No reviews found"
                    }
                });

                $('.filter-input').on('change', function() {
                    table.ajax.reload();
                });

                $('#resetFilterBtn').on('click', function() {
                    $('.filter-input').val('');
                    table.draw();
                });
            @endcan

        });
    </script>
@endpush
