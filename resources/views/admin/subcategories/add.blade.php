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
    }
</style>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">{{ $title ?? 'Create Sub Category' }}</h5>
    </div>

    <div class="card-body pt-15">

        <form class="validate_form" action="{{ route('subcategories.store') }}" method="POST" enctype="multipart/form-data"
            id="categoryForm">
            @csrf

            <div class="row">

                <!-- Parent Category (Required) -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">
                        Parent Category <span class="text-danger">*</span>
                    </label>
                    <select id="filterParent" class="form-control select2 filter-input">
                        <option value="">All</option>
                        @foreach ($categories ?? [] as $cat)
                            <option value="{{ encrypt($cat->id) }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>

                </div>

                <!-- Sub Category Name -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">
                        Sub Category Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="name" class="form-control" placeholder="Enter sub category name"
                        required>
                </div>

                <!-- Meta Title -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Meta Title</label>
                    <input type="text" name="meta_title" class="form-control">
                </div>

                <!-- Meta Keywords -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Meta Keywords</label>
                    <select name="meta_keywords[]" class="form-control select2-tags" multiple
                        data-placeholder="Add keywords"></select>
                </div>

                <!-- Meta Description -->
                <div class="col-md-12 mb-15">
                    <label class="form-label">Meta Description</label>
                    <textarea name="meta_description" class="form-control" rows="3"></textarea>
                </div>

                <!-- Image -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Category Image</label>
                    <input type="file" name="image" class="form-control imageInput" data-preview="previewImage"
                        accept="image/*">

                    <div class="preview-container d-none" id="wrap_previewImage">
                        <img id="previewImage" class="img-preview">
                        <span class="remove-preview" data-target="previewImage">×</span>
                    </div>
                </div>

                <!-- Banner Image (Required on Create) -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">
                        Banner Image <span class="text-danger">*</span>
                    </label>
                    <input type="file" name="banner_image" class="form-control imageInput"
                        data-preview="previewBanner" accept="image/*" required>

                    <div class="preview-container d-none" id="wrap_previewBanner">
                        <img id="previewBanner" class="img-preview">
                        <span class="remove-preview" data-target="previewBanner">×</span>
                    </div>
                </div>

                <!-- Status -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="1" selected>Active</option>
                        <option value="0">Inactive</option>
                    </select>
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

        $('.select2').select2({
            width: '100%',
            dropdownParent: $('#categoryForm')
        });

        $('.select2-tags').select2({
            tags: true,
            tokenSeparators: [',', ' '],
            width: '100%'
        });

        // Image Preview
        $(document).on('change', '.imageInput', function() {
            const previewId = $(this).data('preview');
            const previewImg = $('#' + previewId);
            const previewWrap = $('#wrap_' + previewId);
            const file = this.files[0];

            if (!file) return;

            if (file.size > 5 * 1024 * 1024) {
                alert('Image must be under 5MB');
                this.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = e => {
                previewImg.attr('src', e.target.result);
                previewWrap.removeClass('d-none');
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
