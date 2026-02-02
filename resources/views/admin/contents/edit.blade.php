@extends('layouts.admin')

@section('content')
    <div class="app-content-area">
        <div class="app-content-wrap">
            <div class="container-fluid">

                <!-- PAGE HEADER -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="page-title-box d-flex-between flex-wrap gap-15">
                            <h1 class="page-title fs-18 lh-1">Edit Content</h1>
                            <nav>
                                <ol class="breadcrumb breadcrumb-example1 mb-0">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.dashboard') }}">Home</a>
                                    </li>

                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.dashboard') }}">Content</a>
                                    </li>

                                    <li class="breadcrumb-item active" aria-current="page">Edit Content</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- FORM -->
                <form action="{{ route('contents.update', encrypt($content->id)) }}" method="POST"
                    enctype="multipart/form-data" class="validate_form" id="contentForm">
                    @csrf
                    @method('PUT')

                    <div class="row">

                        <!-- LEFT COLUMN -->
                        <div class="col-xl-8">
                            <div class="card">
                                <div class="card-header justify-between">
                                    <h4>Content Information</h4>
                                    <a href="{{ route('contents.index') }}" class="btn btn-light">Back</a>
                                </div>

                                <div class="card-body">
                                    <div class="row">

                                        <!-- TITLE -->
                                        <div class="col-md-12 mb-15">
                                            <label class="form-label">
                                                Title <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="title" class="form-control"
                                                value="{{ old('title', $content->title) }}" required>
                                        </div>

                                        <!-- IMAGE -->
                                        <div class="col-md-6 mb-15">
                                            <label class="form-label">Banner Image</label>
                                            <input type="file" name="image" class="form-control" accept="image/*">

                                            @if ($content->image)
                                                <div class="mt-2">
                                                    {!! image_show($content->image, 100, 100) !!}
                                                </div>
                                            @endif
                                        </div>

                                        <!-- STATUS -->
                                        <div class="col-md-6 mb-15">
                                            <label class="form-label">Status</label>
                                            <select name="status" class="form-control" required>
                                                <option value="1" {{ $content->status == 1 ? 'selected' : '' }}>
                                                    Published
                                                </option>
                                                <option value="0" {{ $content->status == 0 ? 'selected' : '' }}>
                                                    Draft
                                                </option>
                                            </select>
                                        </div>

                                        <!-- CONTENT -->
                                        <div class="col-md-12 mb-15">
                                            <label class="form-label">Content</label>

                                            <div id="editor" style="height:260px;">
                                                {!! $content->description !!}
                                            </div>

                                            <input type="hidden" name="description" id="description">
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- RIGHT COLUMN -->
                        <div class="col-xl-4">
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h4>SEO Information</h4>
                                </div>
                                <div class="card-body">

                                    <label class="form-label">Meta Title</label>
                                    <input type="text" name="meta_title" class="form-control"
                                        value="{{ old('meta_title', $content->meta_title) }}">

                                    <label class="form-label mt-3">Meta Description</label>
                                    <textarea name="meta_description" class="form-control" rows="3">{{ old('meta_description', $content->meta_description) }}</textarea>

                                    <label class="form-label mt-3">Meta Keywords</label>
                                    <select name="meta_keywords[]" class="form-control select2-tags" multiple>
                                        @foreach ((array) $content->meta_keywords as $keyword)
                                            <option value="{{ $keyword }}" selected>
                                                {{ $keyword }}
                                            </option>
                                        @endforeach
                                    </select>

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
                                        <i class="bi bi-check-circle me-2"></i> Update Content
                                    </button>
                                    <a href="{{ route('contents.index') }}" class="btn btn-outline-secondary">
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

            // Sync editor data on load
            $('#description').val(quill.root.innerHTML);

            quill.on('text-change', function() {
                $('#description').val(quill.root.innerHTML);
            });

            // Safety sync before submit
            $('#contentForm').on('submit', function() {
                $('#description').val(quill.root.innerHTML);
            });

        });
    </script>
@endpush
