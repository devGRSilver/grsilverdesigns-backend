@extends('layouts.admin')

@section('content')
    <div class="app-content-area">
        <div class="app-content-wrap">
            <div class="container-fluid">

                <!-- PAGE HEADER -->
                <div class="row mb-3">
                    <div class="col-xl-12">
                        <div class="page-title-box d-flex justify-content-between align-items-center">
                            <h1 class="fs-18 mb-0">Add Blog</h1>

                            <nav>
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.dashboard') }}">Home</a>
                                    </li>
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('blogs.index') }}">Blogs</a>
                                    </li>
                                    <li class="breadcrumb-item active">Add</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- FORM -->
                <form action="{{ route('blogs.store') }}" method="POST" enctype="multipart/form-data" id="blogForm"
                    class="validate_form">

                    @csrf

                    <div class="row">

                        <!-- LEFT COLUMN -->
                        <div class="col-xl-8">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between">
                                    <h4 class="mb-0">Blog Information</h4>
                                    <a href="{{ route('blogs.index') }}" class="btn btn-light">Back</a>
                                </div>

                                <div class="card-body">
                                    <div class="row">

                                        <!-- TITLE -->
                                        <div class="col-md-12 mb-15">
                                            <label class="form-label">
                                                Title <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="title" class="form-control"
                                                value="{{ old('title') }}" required>
                                        </div>

                                        <!-- SHORT DESCRIPTION -->
                                        <div class="col-md-12 mb-15">
                                            <label class="form-label">Short Description</label>
                                            <textarea name="short_description" class="form-control" rows="3">{{ old('short_description') }}</textarea>
                                        </div>

                                        <!-- FEATURED IMAGE -->
                                        <div class="col-md-6 mb-15">
                                            <label class="form-label">Featured Image</label>
                                            <input type="file" name="featured_image" class="form-control"
                                                accept="image/*">
                                        </div>

                                        <!-- STATUS -->
                                        <div class="col-md-6 mb-15">
                                            <label class="form-label">Status</label>
                                            <select name="status" class="form-control" required>
                                                <option value="1">Active</option>
                                                <option value="0">Inactive</option>
                                            </select>
                                        </div>

                                        <!-- CONTENT -->
                                        <div class="col-md-12 mb-15">
                                            <label class="form-label">
                                                Content <span class="text-danger">*</span>
                                            </label>

                                            <div id="editor" style="height:260px;"></div>

                                            <input type="hidden" name="content" id="content"
                                                value="{{ old('content') }}">
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- RIGHT COLUMN -->
                        <div class="col-xl-4">



                            <!-- SEO -->
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="mb-0">SEO Information</h4>
                                </div>
                                <div class="card-body">

                                    <label class="form-label">Meta Title</label>
                                    <input type="text" name="meta_title" class="form-control"
                                        value="{{ old('meta_title') }}">

                                    <label class="form-label mt-3">Meta Description</label>
                                    <textarea name="meta_description" class="form-control" rows="3">{{ old('meta_description') }}</textarea>

                                    <label class="form-label mt-3">Meta Keywords</label>
                                    <select name="meta_keywords[]" class="form-control select2-tags" multiple></select>

                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- SUBMIT -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-footer bg-light">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="bi bi-check-circle me-2"></i> Create Blog
                                    </button>
                                    <a href="{{ route('blogs.index') }}" class="btn btn-outline-secondary">
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>
@endsection
@push('style')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/plugins/select2.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
@endpush
@push('scripts')
    <script src="{{ asset('assets/admin/js/plugins/select2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>

    <script>
        $(document).ready(function() {

            $('.select2-tags').select2({
                tags: true,
                tokenSeparators: [','],
                width: '100%',
                placeholder: 'Type & press Enter'
            });

            const quill = new Quill('#editor', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{
                            header: [1, 2, 3, false]
                        }],
                        ['bold', 'italic', 'underline'],
                        [{
                            list: 'ordered'
                        }, {
                            list: 'bullet'
                        }],
                        ['link', 'image'],
                        ['clean']
                    ]
                }
            });

            $('#blogForm').on('submit', function() {
                $('#content').val(quill.root.innerHTML);
            });

        });
    </script>
@endpush
