<div class="card">
    <div class="card-header">
        <h5 class="mb-0">{{ $title ?? 'Create Banner' }}</h5>
    </div>

    <div class="card-body">
        <form class="validate_form" action="{{ route('banners.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">

                <!-- Title -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" placeholder="Enter banner title"
                        value="{{ old('title') }}" required>
                </div>

                <!-- Type -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Type <span class="text-danger">*</span></label>
                    <select name="type" class="form-select" required>
                        <option value="">Select Type</option>
                        <option value="banner">Banner</option>
                        <option value="slider">Slider</option>
                    </select>
                </div>

                <!-- Group Key -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Group Key</label>
                    <input type="text" name="group_key" class="form-control"
                        placeholder="home-top / sidebar / footer" value="{{ old('group_key') }}">
                </div>

                <!-- Image -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Image <span class="text-danger">*</span></label>
                    <input type="file" name="image_url" class="form-control" accept="image/*" required>
                </div>

                <!-- Link URL -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Link URL</label>
                    <input type="url" name="link_url" class="form-control" placeholder="https://example.com"
                        value="{{ old('link_url') }}">
                </div>

                <!-- Button Text -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Button Text</label>
                    <input type="text" name="button_text" class="form-control" placeholder="Shop Now"
                        value="{{ old('button_text') }}">
                </div>

                <!-- Description -->
                <div class="col-md-12 mb-15">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Short banner description">{{ old('description') }}</textarea>
                </div>

                <!-- Status -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>

            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>

        </form>
    </div>
</div>
