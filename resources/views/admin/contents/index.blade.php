@extends('layouts.admin')

@section('content')
    <div class="app-content-area">
        <div class="app-content-wrap">
            <div class="container-fluid">

                <!-- Page Header -->
                <div class="row mb-3">
                    <div class="col-xl-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <h1 class="fs-18 mb-0">{{ $title ?? 'CMS Pages' }}</h1>

                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.dashboard') }}">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active">Content</li>
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

                                    <div class="col-md-2">
                                        <label class="form-label">Status</label>
                                        <select id="filterStatus" class="form-control filter-input">
                                            <option value="">ALL</option>
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>

                                    @if (!empty($contentTypes))
                                        <div class="col-md-2">
                                            <label class="form-label">Type</label>
                                            <select id="filterType" class="form-control filter-input">
                                                <option value="">ALL</option>
                                                @foreach ($contentTypes as $value => $label)
                                                    <option value="{{ $value }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif

                                    <div class="col-md-2 d-grid">
                                        <button type="button" id="resetFilterBtn" class="btn btn-outline-secondary">
                                            <i class="ri-refresh-line"></i> Reset
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
                                <div class="table-responsive">
                                    <table id="dataTable" class="table table-bordered align-middle w-100">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="50">#</th>
                                                <th width="90">Image</th>
                                                <th>Title</th>
                                                <th>Slug</th>
                                                <th width="90">Status</th>
                                                <th width="130">Updated At</th>
                                                <th width="120">Action</th>
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

            const table = $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,

                ajax: {
                    url: "{{ route('contents.index') }}",
                    data: function(d) {
                        d.status = $('#filterStatus').val();
                        d.type = $('#filterType').val();
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
                    {
                        data: 'status'
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
                    searchPlaceholder: "Search pages..."
                }
            });

            $('.filter-input').on('change', function() {
                table.ajax.reload();
            });

            $('#resetFilterBtn').on('click', function() {
                $('.filter-input').val('');
                table.ajax.reload();
            });

        });
    </script>
@endpush
