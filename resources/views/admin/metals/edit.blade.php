<div class="card">

    <div class="card-header">
        <h5 class="mb-0">{{ $title ?? '' }}</h5>
    </div>

    <div class="card-body pt-15">
        <form class="validate_form" action="{{ route('metals.update', encrypt($data->id)) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">

                <!-- Metal Name -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Metal Name</label>
                    <input type="text" name="name" class="form-control" value="{{ $data->name }}" readonly>
                </div>

                <!-- Price Per Gram -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Price Per Gram (USD)</label>
                    <input type="number" step="0.01" name="price_per_gram" class="form-control"
                        placeholder="Enter price per gram" value="{{ $data->price_per_gram }}">
                </div>

            </div>



            <!-- ATTENTION MESSAGE -->
            <div class="alert alert-warning d-flex align-items-start gap-2 mt-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                <div>
                    <strong>Attention:</strong> Please note — updating the price of this metal will automatically
                    update the prices of all related categories and all products under those categories
                    according to the new price. Proceed carefully.
                </div>
            </div>




            <button type="submit" class="btn btn-primary">Update Price</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

        </form>
    </div>
</div>
