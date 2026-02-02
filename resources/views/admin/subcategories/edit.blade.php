<div class="card">
    <div class="card-header">
        <h5 class="mb-0">{{ $title ?? 'Edit Sub Category' }}</h5>
    </div>

    <div class="card-body pt-15">

        <form class="validate_form" action="{{ route('subcategories.update', encrypt($category->id)) }}" method="POST"
            enctype="multipart/form-data" id="categoryForm">
            @csrf
            @method('PUT')

            <div class="row">

                <!-- Parent Category (Required) -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">
                        Parent Category <span class="text-danger">*</span>
                    </label>
                    <select name="category_id" class="form-control select2" required>
                        <option value="">-- Select Parent Category --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}"
                                {{ $category->parent_id == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Sub Category Name -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">
                        Sub Category Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $category->name) }}"
                        required>
                </div>

                <!-- Meta Title -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Meta Title</label>
                    <input type="text" name="meta_title" class="form-control"
                        value="{{ old('meta_title', $category->meta_title) }}">
                </div>

                <!-- Meta Keywords -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Meta Keywords</label>
                    <select name="meta_keywords[]" class="form-control select2-tags" multiple
                        data-placeholder="Add keywords">
                        @if ($category->meta_keywords)
                            @foreach (json_decode($category->meta_keywords, true) as $keyword)
                                <option value="{{ $keyword }}" selected>{{ $keyword }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <!-- Meta Description -->
                <div class="col-md-12 mb-15">
                    <label class="form-label">Meta Description</label>
                    <textarea name="meta_description" class="form-control" rows="3">{{ old('meta_description', $category->meta_description) }}</textarea>
                </div>

                <!-- Image -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Category Image</label>
                    <input type="file" name="image" class="form-control imageInput" data-preview="previewImage"
                        accept="image/*">

                    <div class="preview-container mt-2 {{ $category->image ? '' : 'd-none' }}" id="wrap_previewImage"
                        style="max-width:100px">
                        <img id="previewImage" class="img-preview"
                            src="{{ $category->image ? asset($category->image) : '' }}">
                        <span class="remove-preview" data-target="previewImage">×</span>
                    </div>
                </div>

                <!-- Banner -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Banner Image</label>
                    <input type="file" name="banner_image" class="form-control imageInput"
                        data-preview="previewBanner" accept="image/*">

                    <div class="preview-container mt-2 {{ $category->banner_image ? '' : 'd-none' }}"
                        id="wrap_previewBanner" style="max-width:100px">
                        <img id="previewBanner" class="img-preview"
                            src="{{ $category->banner_image ? asset($category->banner_image) : '' }}">
                        <span class="remove-preview" data-target="previewBanner">×</span>
                    </div>
                </div>

                <!-- Status -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="1" {{ $category->status ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ !$category->status ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Update</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>

        </form>
    </div>
</div>

<script>
    $(document).ready(function() {

        $('.select2').select2({
            width: '100%',
            dropdownParent: $('#categoryForm')
        });

        $('.select2-tags').select2({
            tags: true,
            tokenSeparators: [',', ' '],
            width: '100%'
        });

        // Image preview
        $(document).on('change', '.imageInput', function() {
            const preview = $('#' + $(this).data('preview'));
            const wrap = $('#wrap_' + $(this).data('preview'));
            const file = this.files[0];

            if (!file) return;

            if (file.size > 5 * 1024 * 1024) {
                alert('Image must be under 5MB');
                this.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = e => {
                preview.attr('src', e.target.result);
                wrap.removeClass('d-none');
            };
            reader.readAsDataURL(file);
        });

        $(document).on('click', '.remove-preview', function() {
            const target = $(this).data('target');
            $('#' + target).attr('src', '');
            $('#wrap_' + target).addClass('d-none');
            $("input[data-preview='" + target + "']").val('');
        });

    });
</script>
