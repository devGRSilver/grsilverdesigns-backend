<div class="card">
    <div class="card-header d-flex-between">
        <h5 class="mb-0">{{ $title ?? 'Product Variants' }}</h5>

    </div>

    <div class="card-body pt-15">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle">
                <thead class="table-light text-uppercase">
                    <tr>
                        <th>#</th>
                        <th>Variant Name</th>
                        <th>SKU</th>
                        <th>Price</th>
                        <th>Selling Price</th>
                        <th>Stock Quantity</th>
                        <th>Stock Status</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($variants as $variant)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $variant->variant_name }}</td>
                            <td>{{ $variant->sku }}</td>
                            <td>{{ $variant->price }}</td>
                            <td>{{ $variant->selling_price }}</td>
                            <td>{{ $variant->stock_quantity }}</td>
                            <td>
                                <span
                                    class="badge {{ $variant->stock_status == 'in_stock' ? 'bg-success' : 'bg-danger' }}">
                                    {{ ucfirst(str_replace('_', ' ', $variant->stock_status)) }}
                                </span>
                            </td>
                            <td>{{ $variant->created_at->format('d M Y') }}</td>
                            <td>{{ $variant->updated_at->format('d M Y') }}</td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted">No variants found for this product.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3 d-flex-between">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <div>
            </div>
        </div>
    </div>
</div>
