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

                <!-- Filters + Add Button -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <div class="row g-3 align-items-end">

                                    <!-- Status Filter (only show if user can view blogs) -->
                                    @can('blogs.view.any')
                                        <div class="col-md-2">
                                            <label class="form-label">Status</label>
                                            <select id="filterStatus" class="form-control filter-input">
                                                <option value="">ALL</option>
                                                <option value="1">Active</option>
                                                <option value="0">Inactive</option>
                                            </select>
                                        </div>
                                    @endcan

                                    <!-- Reset Button (only show if user can view blogs) -->
                                    @can('blogs.view.any')
                                        <div class="col-md-2 d-grid">
                                            <button id="resetFilterBtn" type="button"
                                                class="btn btn-outline-secondary fw-bold">
                                                <i class="ri-refresh-line me-1"></i> Reset
                                            </button>
                                        </div>
                                    @endcan

                                    <!-- Add Button (only show if user can create blogs) -->
                                    <div class="col text-end">
                                        @can('blogs.create')
                                            <a href="{{ route($resource . '.create') }}" class="btn btn-primary fw-bold">
                                                <i class="bx bx-plus me-1"></i> Add {{ $resourceName }}
                                            </a>
                                        @else
                                            @can('blogs.view.any')
                                                <div class="text-muted small mt-2">No permission to add blogs</div>
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
                                    <table id="dataTable" class="table table-bordered align-middle w-100">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Image</th>
                                                <th>Title</th>
                                                <th>Slug</th>
                                                @can('blogs.view.any')
                                                    <th>Status</th>
                                                    <th>Updated</th>
                                                @endcan
                                                @canany(['blogs.view', 'blogs.update', 'blogs.delete'])
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
                        data: 'image',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'title'
                    },
                    {
                        data: 'slug'
                    },
                    @can('blogs.view.any')
                        {
                            data: 'status',
                            orderable: false
                        }, {
                            data: 'updated_at'
                        },
                    @endcan
                    @canany(['blogs.view', 'blogs.update', 'blogs.delete'])
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
                    searchPlaceholder: "Search blog...",
                    lengthMenu: "Show _MENU_",
                    zeroRecords: "No blogs found"
                }
            });

            $('.filter-input').on('change', function() {
                table.ajax.reload();
            });

            $('#resetFilterBtn').on('click', function() {
                $('.filter-input').val('');
                table.draw();
            });

        });
    </script>
@endpush
