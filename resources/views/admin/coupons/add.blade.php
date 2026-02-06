<div class="card">
    <div class="card-header">
        <h5 class="mb-0">{{ $title ?? 'Create Coupon' }}</h5>
    </div>

    <div class="card-body pt-15">
        @if (session('debug'))
            <div class="alert alert-info">
                <strong>Debug:</strong> {{ session('debug') }}
            </div>
        @endif

        <form class="validate_form"
            action="{{ isset($coupon) ? route('coupons.update', encrypt($coupon->id)) : route('coupons.store') }}"
            method="POST" enctype="multipart/form-data" id="couponForm">
            @csrf
            @if (isset($coupon))
                @method('PUT')
            @endif

            <div class="row">

                <!-- Coupon Code -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Coupon Code <span class="text-danger">*</span></label>
                    <input type="text" name="code" class="form-control"
                        placeholder="Enter coupon code (e.g., WELCOME20)" required
                        value="{{ old('code', $coupon->code ?? '') }}">
                    <small class="form-text text-muted">Only uppercase letters, numbers, hyphens, and underscores
                        allowed</small>
                </div>

                <!-- Coupon Name -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Coupon Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="Enter coupon name" required
                        value="{{ old('name', $coupon->name ?? '') }}">
                </div>

                <!-- Description -->
                <div class="col-md-12 mb-15">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Enter coupon description">{{ old('description', $coupon->description ?? '') }}</textarea>
                </div>

                <!-- Type -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Discount Type <span class="text-danger">*</span></label>
                    <select name="type" class="form-control" required id="discountType">
                        <option value="">Select Type</option>
                        @foreach ($couponTypes as $value => $label)
                            <option value="{{ $value }}"
                                {{ old('type', $coupon->type ?? '') == $value ? 'selected' : '' }}>{{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Value -->
                <div class="col-md-6 mb-15">
                    <label class="form-label" id="valueLabel">Discount Value <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="number" name="value" class="form-control" placeholder="Enter value"
                            step="0.01" required value="{{ old('value', $coupon->value ?? '') }}" id="discountValue">
                        <span class="input-group-text" id="valueSuffix">%</span>
                    </div>
                </div>

                <!-- Usage Limit -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Usage Limit</label>
                    <input type="number" name="usage_limit" class="form-control"
                        placeholder="Leave empty for unlimited" min="1"
                        value="{{ old('usage_limit', $coupon->usage_limit ?? '') }}">
                    <small class="form-text text-muted">Maximum number of times this coupon can be used</small>
                </div>

                <!-- User Limit -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">User Limit</label>
                    <input type="number" name="user_limit" class="form-control" placeholder="Max uses per user"
                        min="1" value="{{ old('user_limit', $coupon->user_limit ?? '') }}">
                    <small class="form-text text-muted">Maximum uses per individual user</small>
                </div>

                <!-- Minimum Purchase Amount -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Minimum Purchase Amount</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" name="min_purchase_amount" class="form-control" placeholder="0.00"
                            step="0.01" min="0"
                            value="{{ old('min_purchase_amount', $coupon->min_purchase_amount ?? '') }}">
                    </div>
                    <small class="form-text text-muted">Minimum cart total required to apply coupon</small>
                </div>

                <!-- Status -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control" required>
                        <option value="1" {{ old('status', $coupon->status ?? 1) == '1' ? 'selected' : '' }}>
                            Active
                        </option>
                        <option value="0" {{ old('status', $coupon->status ?? 1) == '0' ? 'selected' : '' }}>
                            Inactive
                        </option>
                    </select>
                </div>

                <!-- Date Range -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Date Range <span class="text-danger">*</span></label>
                    <input type="text" id="dateRange" class="form-control" name="date_range"
                        placeholder="Select start and expiry date" required
                        value="{{ isset($coupon) ? $coupon->starts_at->format('Y-m-d') . ' to ' . $coupon->expires_at->format('Y-m-d') : '' }}">
                    <input type="hidden" name="starts_at" id="starts_at"
                        value="{{ old('starts_at', isset($coupon) ? $coupon->starts_at->format('Y-m-d') : '') }}">
                    <input type="hidden" name="expires_at" id="expires_at"
                        value="{{ old('expires_at', isset($coupon) ? $coupon->expires_at->format('Y-m-d') : '') }}">
                </div>



                {{-- <!-- Included Categories -->
                <div class="col-md-6 mb-15">
                    <label class="form-label">Included Categories</label>
                    <select name="included_categories[]" class="form-control select2" multiple
                        data-placeholder="Select specific categories">
                        @if (isset($categories) && $categories)
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ isset($coupon) && $coupon->included_categories && in_array($category->id, $coupon->included_categories) ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    <small class="form-text text-muted">Leave empty for all categories</small>
                </div> --}}



                <!-- Checkboxes -->
                <div class="col-md-12 mb-15">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="first_order_only"
                                    id="firstOrderCheckbox" value="1"
                                    {{ old('first_order_only', $coupon->first_order_only ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="firstOrderCheckbox">
                                    First Order Only
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="free_shipping"
                                    id="freeShippingCheckbox" value="1"
                                    {{ old('free_shipping', $coupon->free_shipping ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="freeShippingCheckbox">
                                    Free Shipping
                                </label>
                            </div>
                        </div>
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
            dropdownParent: $('#couponForm').closest('.modal, .card-body'),
        });

        // Date Range Picker
        flatpickr("#dateRange", {
            mode: "range",
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "F j, Y",
            minDate: "today",
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates.length === 2) {
                    // Set hidden inputs
                    $('#starts_at').val(selectedDates[0].toISOString().split('T')[0]);
                    $('#expires_at').val(selectedDates[1].toISOString().split('T')[0]);
                } else if (selectedDates.length === 1) {
                    $('#starts_at').val(selectedDates[0].toISOString().split('T')[0]);
                    $('#expires_at').val('');
                }
            }
        });

        // Toggle value suffix based on discount type
        function updateValueLabel() {
            const type = $('#discountType').val();
            const suffix = $('#valueSuffix');
            const label = $('#valueLabel');
            const valueInput = $('#discountValue');

            if (type === 'percentage') {
                suffix.text('%');
                label.text('Discount Percentage *');
                valueInput.attr('max', '100');
                valueInput.attr('placeholder', 'Enter percentage (max 100)');
            } else if (type === 'fixed_amount') {
                suffix.text('$');
                label.text('Discount Amount *');
                valueInput.removeAttr('max');
                valueInput.attr('placeholder', 'Enter amount');
            }
        }

        // Initial update
        updateValueLabel();

        // Update on type change
        $('#discountType').on('change', function() {
            updateValueLabel();
        });

        // Capitalize coupon code
        $('input[name="code"]').on('input', function() {
            $(this).val($(this).val().toUpperCase());
        });

        // Validation for percentage discount (max 100)
        $('#discountValue').on('change', function() {
            const type = $('#discountType').val();
            const value = parseFloat($(this).val());

            if (type === 'percentage' && value > 100) {
                alert('Percentage discount cannot exceed 100%.');
                $(this).val('100');
            }
        });



    });
</script>
