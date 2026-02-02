<div class="card">
    <div class="card-header">
        <h5 class="mb-0">{{ $title ?? 'Edit Category' }}</h5>
    </div>

    <div class="card-body pt-15">

        <form class="validate_form" action="{{ route('categories.update', encrypt($category->id)) }}" method="POST"
            enctype="multipart/form-data" id="categoryForm">
            @csrf
            @method('PUT')

            <div class="row">



                <!-- Category Name -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Category Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="Enter category name"
                        value="{{ old('name', $category->name) }}" required>
                </div>

                <!-- Meta Title -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Meta Title</label>
                    <input type="text" name="meta_title" class="form-control" placeholder="Enter meta title for SEO"
                        value="{{ old('meta_title', $category->meta_title) }}">
                </div>


                <div class="col-md-6 mb-15">
                    <label class="form-label">Meta Keywords</label>
                    <select name="meta_keywords[]" class="form-control select2-tags" multiple
                        data-placeholder="Add keywords (press Enter after each)">

                        @if (!empty($category->meta_keywords))
                            @foreach (json_decode($category->meta_keywords, true) as $keyword)
                                <option value="{{ $keyword }}" selected>{{ $keyword }}</option>
                            @endforeach
                        @endif

                    </select>
                </div>

                <!-- Status -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="1" {{ $category->status == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ $category->status == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>




                <!-- Meta Description -->
                <div class="col-md-12 mb-15">
                    <label class="form-label">Meta Description</label>
                    <textarea name="meta_description" class="form-control" rows="3" placeholder="Enter meta description for SEO">{{ old('meta_description', $category->meta_description) }}</textarea>
                </div>

                <!-- Category Image -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Category Image</label>
                    <input type="file" name="image" class="form-control imageInput" data-preview="previewImage"
                        accept="image/*">
                    <small class="form-text text-muted">
                        Recommended size: <strong>800 × 800 px</strong> (Square) | Max: 5MB
                    </small>
                    <div class="preview-container {{ $category->image ? '' : 'd-none' }}" id="wrap_previewImage">
                        <img id="previewImage" class="img-preview" src="{{ $category->image ?? '' }}"
                            style="max-height:150px; width:auto; object-fit:contain; border:1px solid #ddd; border-radius:8px; margin-top:8px; background:#f8f8f8; padding:3px;">
                        <span class="remove-preview" data-target="previewImage">×</span>
                    </div>
                </div>

                <!-- Banner Image -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Category Banner Image <span class="text-danger">*</span></label>
                    <input type="file" name="banner_image" class="form-control imageInput"
                        data-preview="previewBanner" accept="image/*">
                    <small class="form-text text-muted">
                        Recommended size: <strong>1600 × 500 px</strong> (Wide Banner) | Max: 5MB
                    </small>
                    <div class="preview-container {{ $category->banner_image ? '' : 'd-none' }}"
                        id="wrap_previewBanner">
                        <img id="previewBanner" class="img-preview" src="{{ $category->banner_image ?? '' }}"
                            style="max-height:150px; width:auto; object-fit:contain; border:1px solid #ddd; border-radius:8px; margin-top:8px; background:#f8f8f8; padding:3px;">
                        <span class="remove-preview" data-target="previewBanner">×</span>
                    </div>
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

        // Select2
        $('.select2').select2({
            width: '100%',
            dropdownParent: $('#categoryForm').closest('.modal, .card-body')
        });
        $('.select2-tags').select2({
            tags: true,
            tokenSeparators: [',', ' '],
            width: '100%',
            placeholder: function() {
                return $(this).data('placeholder');
            },
            createTag: function(params) {
                let term = $.trim(params.term);
                if (term === '') return null;
                return {
                    id: term,
                    text: term,
                    newTag: true
                };
            }
        });

        // Image Preview
        $(document).on('change', '.imageInput', function() {
            let previewId = $(this).data('preview');
            let previewImg = $('#' + previewId);
            let previewWrap = $('#wrap_' + previewId);
            let file = this.files[0];
            if (file) {
                const maxSize = 5 * 1024 * 1024;
                if (file.size > maxSize) {
                    alert('File size exceeds 5MB limit.');
                    $(this).val('');
                    return;
                }
                let reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.attr('src', e.target.result);
                    previewWrap.removeClass('d-none');
                };
                reader.readAsDataURL(file);
            }
        });

        $(document).on('click', '.remove-preview', function() {
            let target = $(this).data('target');
            let input = $("input[data-preview='" + target + "']");
            $('#' + target).attr('src', '');
            $('#wrap_' + target).addClass('d-none');
            input.val('');
        });

        // Toggle Primary Checkbox Visibility
        function togglePrimaryCheckbox() {
            if ($('#parentCategorySelect').val() === '') {
                $('#primaryCheckboxContainer').show();
            } else {
                $('#primaryCheckboxContainer').hide();
                $('#isPrimaryCategory').prop('checked', false);
            }
        }

        togglePrimaryCheckbox();
        $('#parentCategorySelect').on('change', togglePrimaryCheckbox);

    });
</script>
