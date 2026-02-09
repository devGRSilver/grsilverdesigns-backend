<div class="card border-0 shadow">
    <div class="card-header bg-white border-bottom py-3">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="mb-0 fw-semibold p-1">{{ $title ?? 'Banner Details' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
    </div>

    <div class="card-body p-4">
        <!-- Header -->
        <div class="d-flex flex-column flex-md-row align-items-start justify-content-between mb-5">
            <div class="d-flex align-items-center mb-3 mb-md-0">
                <div class="bg-primary bg-gradient p-3 rounded-3 me-3">
                    <i class="bi bi-image text-white fs-3"></i>
                </div>
                <div>
                    <h4 class="fw-semibold mb-1">{{ $banner?->title ?? 'Untitled Banner' }}</h4>
                    <p class="text-muted mb-0">{{ $banner?->description ?? 'No description provided' }}</p>
                </div>
            </div>


        </div>

        <div class="row g-4">
            <!-- Left Column -->
            <div class="col-lg-6">
                <!-- Basic Information -->
                <div class="card border">
                    <div class="bg-light p-2">
                        <h6 class="mb-0 fw-semibold p-1">Basic Information</h6>
                    </div>
                    <div class="card-body">
                        <dl class="row mt-2 mb-0">
                            <dt class="col-sm-5 text-muted">Type</dt>
                            <dd class="col-sm-7">
                                <span class="badge bg-info text-uppercase">
                                    {{ $banner?->type }}
                                </span>
                            </dd>

                            <dt class="col-sm-5 text-muted">Group</dt>
                            <dd class="col-sm-7">
                                <span class="badge bg-secondary">
                                    {{ $banner?->group_key }}
                                </span>
                            </dd>

                            <dt class="col-sm-5 text-muted">Button Text</dt>
                            <dd class="col-sm-7">
                                {{ $banner?->button_text ?? '—' }}
                            </dd>
                        </dl>
                    </div>
                </div>

                <!-- Links -->
                <div class="card border mt-3">
                    <div class="bg-light p-2">
                        <h6 class="mb-0 fw-semibold p-1">Links</h6>
                    </div>
                    <div class="card-body">
                        <dl class="row mt-2 mb-0">
                            <dt class="col-sm-5 text-muted">Redirect URL</dt>
                            <dd class="col-sm-7">
                                @if ($banner?->link_url)
                                    <a href="{{ $banner?->link_url }}" target="_blank" class="text-decoration-none">
                                        {{ $banner?->link_url }}
                                    </a>
                                @else
                                    <span class="text-muted">Not set</span>
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-lg-6">
                <!-- Banner Image -->
                <div class="card border">
                    <div class="bg-light p-2">
                        <h6 class="mb-0 fw-semibold p-1">Banner Image</h6>
                    </div>
                    <div class="card-body text-center">
                        @if ($banner?->image_url)
                            <img src="{{ $banner?->image_url }}" alt="Banner Image" class="img-fluid rounded shadow-sm"
                                style="max-height: 200px;">
                        @else
                            <div class="text-muted py-5">
                                No image uploaded
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Status Info -->
                <div class="card border mt-3">
                    <div class="bg-light p-2">
                        <h6 class="mb-0 fw-semibold p-1">Status</h6>
                    </div>
                    <div class="card-body">
                        <dl class="row mt-2 mb-0">
                            <dt class="col-sm-7 text-muted">Current Status</dt>
                            <dd class="col-sm-5 text-end">
                                @if ($banner?->status)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metadata -->
        <div class="card border mt-4">
            <div class="bg-light p-2">
                <h6 class="mb-0 fw-semibold p-1">Metadata</h6>
            </div>
            <div class="card-body">
                <div class="row mt-3">
                    <div class="col-md-4 col-6 mb-3">
                        <p class="text-muted small mb-1">Created At</p>
                        <p class="fw-semibold mb-0">
                            {{ $banner?->created_at->format('d M Y, h:i A') }}
                        </p>
                    </div>

                    <div class="col-md-4 col-6 mb-3">
                        <p class="text-muted small mb-1">Updated At</p>
                        <p class="fw-semibold mb-0">
                            {{ $banner?->updated_at->format('d M Y, h:i A') }}
                        </p>
                    </div>

                    <div class="col-md-4 col-6">
                        <p class="text-muted small mb-1">Banner ID</p>
                        <p class="fw-semibold mb-0">
                            #{{ $banner?->id }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                Close
            </button>
        </div>
    </div>
</div>
