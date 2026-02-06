<div class="card border-0 shadow">
    <div class="card-header bg-white border-bottom py-3">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="mb-0 fw-semibold p-1">{{ $title ?? 'Coupon Details' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
    </div>

    <div class="card-body p-4">
        <!-- Header -->
        <div class="d-flex flex-column flex-md-row align-items-start justify-content-between mb-5">
            <div class="d-flex align-items-center mb-3 mb-md-0">
                <div class="bg-primary bg-gradient p-3 rounded-3 me-3">
                    <h3 class="mb-0 text-white font-monospace">{{ $coupon->code }}</h3>
                </div>
                <div>
                    <h4 class="fw-semibold mb-1">{{ $coupon->name }}</h4>
                    <p class="text-muted mb-0">{{ $coupon->description ?? 'No description provided' }}</p>
                </div>
            </div>
            <div>
                {!! status_dropdown($coupon->status, [
                    'id' => $coupon->id,
                    'url' => route('coupons.status', encrypt($coupon->id)),
                    'method' => 'PUT',
                ]) !!}
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
                        <dl class="row mb-2 mt-2">
                            <dt class="col-sm-5 text-muted">Type</dt>
                            <dd class="col-sm-7">
                                @if ($coupon->type === 'percentage')
                                    <span class="badge bg-info">Percentage</span>
                                @else
                                    <span class="badge bg-success">Fixed Amount</span>
                                @endif
                            </dd>

                            <dt class="col-sm-5 text-muted">Value</dt>
                            <dd class="col-sm-7">
                                <span class="fw-bold fs-5">
                                    @if ($coupon->type === 'percentage')
                                        {{ $coupon->value }}%
                                    @else
                                        ${{ number_format($coupon->value, 2) }}
                                    @endif
                                </span>
                            </dd>

                            <dt class="col-sm-5 text-muted">Start Date</dt>
                            <dd class="col-sm-7">{{ $coupon->starts_at->format('d M Y, h:i A') }}</dd>

                            <dt class="col-sm-5 text-muted">Expiry Date</dt>
                            <dd class="col-sm-7">{{ $coupon->expires_at->format('d M Y, h:i A') }}</dd>

                            <dt class="col-sm-5 text-muted">Days Remaining</dt>
                            <dd class="col-sm-7">
                                @if ($coupon->expires_at->isFuture())
                                    <span class="badge bg-info">{{ $coupon->expires_at->diffInDays(now()) }} days</span>
                                @else
                                    <span class="badge bg-danger">Expired</span>
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>

                <!-- Restrictions -->
                <div class="card border mt-3">
                    <div class="bg-light p-2">
                        <h6 class="mb-0 fw-semibold p-1">Conditions</h6>
                    </div>
                    <div class="card-body">
                        <dl class="row mt-2">
                            <dt class="col-sm-7 text-muted">Minimum Purchase</dt>
                            <dd class="col-sm-5 text-end">
                                @if ($coupon->min_purchase_amount)
                                    <span
                                        class="fw-semibold">${{ number_format($coupon->min_purchase_amount, 2) }}</span>
                                @else
                                    <span class="text-muted">None</span>
                                @endif
                            </dd>

                            <dt class="col-sm-7 text-muted">Minimum Items</dt>
                            <dd class="col-sm-5 text-end">
                                <span
                                    class="fw-semibold">{{ $coupon->min_items ? $coupon->min_items . ' items' : 'None' }}</span>
                            </dd>

                            <dt class="col-sm-7 text-muted">First Order Only</dt>
                            <dd class="col-sm-5 text-end">
                                @if ($coupon->first_order_only)
                                    <span class="badge bg-info">Yes</span>
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </dd>

                            <dt class="col-sm-7 text-muted">Free Shipping</dt>
                            <dd class="col-sm-5 text-end">
                                @if ($coupon->free_shipping)
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-lg-6">
                <!-- Usage Statistics -->
                <div class="card border">
                    <div class="bg-light p-2">
                        <h6 class="mb-0 fw-semibold p-1">Usage Statistics</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center mt-2 mb-4">
                            <div class="col-6">
                                <div class="p-3 bg-light rounded">
                                    <h2 class="text-primary mb-1">{{ $coupon->usage_count }}</h2>
                                    <small class="text-muted">Times Used</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-light rounded">
                                    <h2 class="text-success mb-1">
                                        @if ($coupon->usage_limit)
                                            {{ $coupon->usage_limit - $coupon->usage_count }}
                                        @else
                                            ∞
                                        @endif
                                    </h2>
                                    <small class="text-muted">Remaining Uses</small>
                                </div>
                            </div>
                        </div>

                        @if ($coupon->usage_limit)
                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted">Usage Progress</small>
                                    <small
                                        class="fw-semibold">{{ $coupon->usage_count }}/{{ $coupon->usage_limit }}</small>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar" role="progressbar"
                                        style="width: {{ ($coupon->usage_count / $coupon->usage_limit) * 100 }}%"
                                        aria-valuenow="{{ $coupon->usage_count }}" aria-valuemin="0"
                                        aria-valuemax="{{ $coupon->usage_limit }}">
                                    </div>
                                </div>
                            </div>
                        @endif

                        <dl class="row mt-2">
                            <dt class="col-sm-7 text-muted">Usage Limit</dt>
                            <dd class="col-sm-5 text-end">
                                <span
                                    class="fw-semibold">{{ $coupon->usage_limit ? $coupon->usage_limit . ' times' : 'Unlimited' }}</span>
                            </dd>

                            <dt class="col-sm-7 text-muted">User Limit</dt>
                            <dd class="col-sm-5 text-end">
                                <span
                                    class="fw-semibold">{{ $coupon->user_limit ? $coupon->user_limit . ' per user' : 'Unlimited' }}</span>
                            </dd>
                        </dl>
                    </div>
                </div>

                <!-- Applicability -->
                <div class="card border mt-3">
                    <div class="bg-light p-2">
                        <h6 class="mb-0 fw-semibold p-1">Applicability</h6>
                    </div>
                    <div class="card-body">
                        <dl class="row mt-2">
                            <dt class="col-sm-7 text-muted">Included Products</dt>
                            <dd class="col-sm-5 text-end">
                                @if ($coupon->included_products && count($coupon->included_products) > 0)
                                    <span class="badge bg-info">{{ count($coupon->included_products) }} products</span>
                                @else
                                    <span class="badge bg-secondary">All Products</span>
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
                    <div class="col-md-3 col-6 mb-3 mb-md-0">
                        <p class="text-muted small mb-1">Created At</p>
                        <p class="fw-semibold mb-0">{{ $coupon->created_at->format('d M Y, h:i A') }}</p>
                    </div>
                    <div class="col-md-3 col-6 mb-3 mb-md-0">
                        <p class="text-muted small mb-1">Updated At</p>
                        <p class="fw-semibold mb-0">{{ $coupon->updated_at->format('d M Y, h:i A') }}</p>
                    </div>
                    <div class="col-md-3 col-6">
                        <p class="text-muted small mb-1">Created By</p>
                        <p class="fw-semibold mb-0">
                            @if ($coupon->created_by)
                                Admin #{{ $coupon->created_by }}
                            @else
                                System
                            @endif
                        </p>
                    </div>
                    <div class="col-md-3 col-6">
                        <p class="text-muted small mb-1">Total Orders</p>
                        <p class="fw-semibold mb-0">{{ $coupon->usage_count }} orders</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
        </div>
    </div>
</div>
