<div class="card">
    <div class="card-header">
        <h5 class="mb-0">{{ $title ?? 'Create Banner' }}</h5>
    </div>

    <div class="card-body">
        <!-- FORM -->
        <form action="{{ route('banners.update', encrypt($banner->id)) }}" method="POST" enctype="multipart/form-data"
            class="validate_form">




            @csrf
            @method('PUT')

            <div class="row">

                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">

                                <!-- TITLE -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Title</label>
                                    <input type="text" name="title" class="form-control"
                                        value="{{ old('title', $banner->title) }}">
                                </div>

                                <!-- TYPE -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Type <span class="text-danger">*</span></label>
                                    <select name="type" class="form-control" required>
                                        <option value="banner" {{ $banner->type == 'banner' ? 'selected' : '' }}>
                                            Banner
                                        </option>
                                        <option value="slider" {{ $banner->type == 'slider' ? 'selected' : '' }}>
                                            Slider
                                        </option>
                                    </select>
                                </div>

                                <!-- GROUP KEY -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Group Key <span class="text-danger">*</span></label>
                                    <input type="text" name="group_key" class="form-control"
                                        value="{{ old('group_key', $banner->group_key) }}"
                                        placeholder="home-top, sidebar, footer" required>
                                </div>

                                <!-- LINK URL -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Link URL</label>
                                    <input type="url" name="link_url" class="form-control"
                                        value="{{ old('link_url', $banner->link_url) }}">
                                </div>

                                <!-- BUTTON TEXT -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Button Text</label>
                                    <input type="text" name="button_text" class="form-control"
                                        value="{{ old('button_text', $banner->button_text) }}">
                                </div>

                                <!-- IMAGE -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Banner Image</label>
                                    <input type="file" name="image_url" class="form-control" accept="image/*">

                                    @if ($banner->image_url)
                                        <div class="mt-2">
                                            {!! image_show($banner->image_url, 150, 80) !!}
                                        </div>
                                    @endif
                                </div>

                                <!-- STATUS -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-control" required>
                                        <option value="1" {{ $banner->status == 1 ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="0" {{ $banner->status == 0 ? 'selected' : '' }}>
                                            Inactive</option>
                                    </select>
                                </div>

                                <!-- DESCRIPTION -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control" rows="4">{{ old('description', $banner->description) }}</textarea>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SUBMIT -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-footer bg-light">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i> Update Banner
                            </button>

                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>




                        </div>
                    </div>
                </div>
            </div>

        </form>

    </div>
</div>
