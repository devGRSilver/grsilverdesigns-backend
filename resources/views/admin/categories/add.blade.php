<style>
    .img-preview {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border: 1px solid #ddd;
        border-radius: 8px;
        margin-top: 8px;
    }

    .preview-container {
        display: inline-block;
        position: relative;
    }

    .remove-preview {
        position: absolute;
        top: -5px;
        right: -5px;
        background: red;
        color: white;
        width: 22px;
        height: 22px;
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 50%;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
        line-height: 1;
    }
</style>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">{{ $title ?? 'Create Category' }}</h5>
    </div>

    <div class="card-body pt-15">
        @if (session('debug'))
            <div class="alert alert-info">
                <strong>Debug:</strong> {{ session('debug') }}
            </div>
        @endif

        <form class="validate_form" action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data"
            id="categoryForm">
            @csrf

            <div class="row">



                <!-- Category Name -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Category Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="Enter category name"
                        required>
                </div>

                <!-- Meta Title -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Meta Title</label>
                    <input type="text" name="meta_title" class="form-control" placeholder="Enter meta title for SEO">
                </div>

                <!-- Meta Keywords -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Meta Keywords</label>
                    <select name="meta_keywords[]" class="form-control select2-tags" multiple
                        data-placeholder="Add keywords (press Enter after each)"></select>
                </div>

                <!-- Status -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="1" selected>Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>



                <!-- Meta Description -->
                <div class="col-md-12 mb-15">
                    <label class="form-label">Meta Description</label>
                    <textarea name="meta_description" class="form-control" rows="3" placeholder="Enter meta description for SEO"></textarea>
                </div>

                <!-- Category Image -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Category Image</label>
                    <input type="file" name="image" class="form-control imageInput" data-preview="previewImage"
                        accept="image/*">
                    <small class="form-text text-muted">
                        Recommended size: <strong>800 × 800 px</strong> (Square) | Max: 5MB
                    </small>
                    <div class="preview-container d-none" id="wrap_previewImage">
                        <img id="previewImage" class="img-preview">
                        <span class="remove-preview" data-target="previewImage">×</span>
                    </div>
                </div>

                <!-- Banner Image -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Category Banner Image <span class="text-danger">*</span></label>
                    <input type="file" name="banner_image" class="form-control imageInput"
                        data-preview="previewBanner" accept="image/*" required>
                    <small class="form-text text-muted">
                        Recommended size: <strong>1600 × 500 px</strong> (Wide Banner) | Max: 5MB
                    </small>
                    <div class="preview-container d-none" id="wrap_previewBanner">
                        <img id="previewBanner" class="img-preview">
                        <span class="remove-preview" data-target="previewBanner">×</span>
                    </div>
                </div>






                <div class="col-md-12 mb-15" id="primaryCheckboxContainer">
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="is_primary" id="isPrimaryCategory">
                        <label class="form-check-label" for="isPrimaryCategory">
                            Make this a Primary Category (will be visible on the homepage if no parent is selected)
                        </label>
                    </div>
                </div>



            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Submit</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>

        </form>
    </div>
</div>

<script>
    $(document).ready(function() {

        // Select2 for normal selects
        $('.select2').select2({
            width: '100%',
            dropdownParent: $('#categoryForm').closest('.modal, .card-body'),
        });

        // Select2 for tags
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

        // Remove Preview
        $(document).on('click', '.remove-preview', function() {
            let target = $(this).data('target');
            let input = $("input[data-preview='" + target + "']");
            $('#' + target).attr('src', '');
            $('#wrap_' + target).addClass('d-none');
            input.val('');
        });




        function togglePrimaryCheckbox() {
            if ($('#parentCategorySelect').val() === '') {
                $('#primaryCheckboxContainer').show();
                // $('#isPrimaryCategory').prop('checked', true);
            } else {
                $('#primaryCheckboxContainer').hide();
                $('#isPrimaryCategory').prop('checked', false);
            }
        }

        // Initial check on page load
        togglePrimaryCheckbox();

        // Toggle on parent select change
        $('#parentCategorySelect').on('change', function() {
            togglePrimaryCheckbox();
        });




    });
</script>
