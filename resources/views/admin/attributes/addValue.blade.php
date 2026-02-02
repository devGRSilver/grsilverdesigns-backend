<div class="card">
    <div class="card-header">
        <h5 class="mb-0">{{ $title ?? 'Edit Category' }}</h5>
    </div>

    <div class="card-body pt-15">

        <form class="validate_form" action="{{ route('attributes.values.store', $attribute_id) }}" method="POST"
            id="categoryForm">
            @csrf
            <div class="row">
                <!-- Category Name -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Attribute Value Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="Enter value name" required>
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Add</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>

        </form>
    </div>
</div>
