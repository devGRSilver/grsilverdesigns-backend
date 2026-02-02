<div class="card">
    <div class="card-header">
        <h5 class="mb-0">{{ $title ?? 'Add Attribute' }}</h5>
    </div>

    <div class="card-body pt-15">
        <form class="validate_form" action="{{ route('attributes.store') }}" method="POST" enctype="multipart/form-data"
            id="attributeForm">
            @csrf

            <div class="row">
                <!-- Attribute Name -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">
                        Attribute Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="name" class="form-control" placeholder="Enter attribute name"
                        required>
                </div>

                <!-- Attribute Type -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">
                        Attribute Type <span class="text-danger">*</span>
                    </label>
                    <select name="type" class="form-control select2" required>
                        <option value="">Select Attribute Type</option>
                        <option value="select">Select</option>
                        <option value="multiselect">Multi Select</option>
                        <option value="text">Text</option>
                        <option value="color">Color</option>
                    </select>
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">
                    Save
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Close
                </button>
            </div>
        </form>
    </div>
</div>
