@extends('layouts.admin')

@section('content')
    <div class="app-content-area">
        <div class="container-fluid">
            <!-- Page Header -->
            <div class="row mb-3">
                <div class="col-xl-12">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h1 class="fs-18 mb-0">{{ $title ?? '' }}</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb breadcrumb-example1 mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}">Home</a>
                                </li>
                                <li class="breadcrumb-item active">Price</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- TABS -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <ul class="nav nav-tabs px-3 pt-3">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#goldTab">
                                <i class="ri-medal-fill me-1"></i> Gold
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#silverTab">
                                <i class="ri-vip-crown-fill me-1"></i> Silver
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content p-4">
                        @php
                            $gold = DB::table('metals')->where('id', 1)->first();
                            $silver = DB::table('metals')->where('id', 2)->first();
                        @endphp

                        {{-- ================= GOLD TAB ================= --}}
                        <div class="tab-pane fade show active" id="goldTab">
                            <div class="col-xl-12">
                                <div class="card shadow-sm border-0">
                                    <div class="card-body">
                                        <small class="text-muted mb-3">
                                            Last updated at: {{ $gold->updated_at ?? 'Never' }}
                                        </small>

                                        <div class="row g-3 align-items-end mt-2">
                                            <!-- Price Input (only if can update) -->
                                            @can('metals.update')
                                                <div class="col-md-2">
                                                    <label for="goldPriceInput" class="form-label">
                                                        1 Gram Gold Price (USD) <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="number" id="goldPriceInput" name="price"
                                                        class="form-control" placeholder="{{ $gold->price_per_gram ?? '0' }}"
                                                        min="0.01" max="10000" step="0.01" required
                                                        value="{{ number_format($gold->price_per_gram, 2) ?? '' }}">
                                                </div>
                                            @endcan

                                            <!-- Update Price Button (only if can update) -->
                                            @can('metals.update')
                                                <div class="col-md-2 d-grid">
                                                    <button id="updateGoldPriceBtn" type="button"
                                                        class="btn btn-outline-primary btn-md fw-bold">
                                                        <i class="ri-refresh-line me-1 fs-5"></i> Update Price
                                                    </button>
                                                </div>
                                            @endcan

                                            <!-- Assign Category Button (only if can assign) -->
                                            <div class="col text-end">
                                                @can('metals.assign')
                                                    <a href="{{ route('metals.assign', 'gold') }}"
                                                        class="btn btn-primary btn-md px-4 fw-bold modal_open">
                                                        Assign New Category
                                                    </a>
                                                @else
                                                    @can('metals.view.any')
                                                        <div class="text-muted small">No permission to assign categories</div>
                                                    @endcan
                                                @endcan
                                            </div>

                                            <!-- Attention Message -->
                                            @can('metals.view.any')
                                                <div class="col-12">
                                                    <div class="alert alert-primary d-flex align-items-start gap-2"
                                                        role="alert">
                                                        <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                                                        <div>
                                                            <strong>Attention:</strong>
                                                            The current gold price is
                                                            ${{ number_format($gold->price_per_gram, 2) }}.
                                                            @can('metals.update')
                                                                If you want to update the price, please enter the new value
                                                                carefully and click <strong>Update Price</strong>.
                                                            @endcan
                                                            Once updated, the prices of all products listed in
                                                            the categories below will be recalculated automatically based on
                                                            their weight.
                                                            This process may take some time to complete.
                                                        </div>
                                                    </div>
                                                </div>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Gold Categories List -->
                            @can('metals.view.any')
                                @forelse($goldCategory as $cat)
                                    @php
                                        $mainCategory = DB::table('categories')->find($cat->category_id);
                                        $subCategories = DB::table('categories')
                                            ->whereIn('id', array_filter(explode(',', $cat->sub_category_ids)))
                                            ->get();
                                    @endphp

                                    <div class="card border-0 shadow-sm mb-4" id="gold-main-{{ $cat->category_id }}">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="fw-semibold mb-0">
                                                    <a href="{{ route('categories.index') }}" target="_blank"
                                                        class="text-decoration-none text-dark">
                                                        {{ $mainCategory->name ?? 'Category Deleted' }}
                                                    </a>
                                                </h6>
                                                <small class="text-muted">
                                                    {!! redirect_to_link(
                                                        route('subcategories.index') . '?parent_id=' . $cat->category_id,
                                                        $subCategories->count() . ' Subcategories',
                                                    ) !!}
                                                </small>
                                            </div>

                                            <!-- Delete Main Category Button (only if can delete category) -->
                                            @can('metals.category.delete')
                                                <button class="btn btn-outline-danger btn-sm delete-main"
                                                    data-url="{{ route('metals.category.delete', [1, $cat->category_id]) }}"
                                                    data-wrapper="#gold-main-{{ $cat->category_id }}"
                                                    data-name="{{ $mainCategory->name ?? 'Category' }}">
                                                    <i class="ri-delete-bin-6-line"></i>
                                                </button>
                                            @endcan
                                        </div>

                                        <div class="card-body">
                                            <div class="row g-3">
                                                @foreach ($subCategories as $sub)
                                                    <div class="col-xl-3 col-lg-4 col-md-6"
                                                        id="gold-sub-{{ $cat->category_id }}-{{ $sub->id }}">
                                                        <div class="card border shadow-sm">
                                                            <div class="card-body p-3 position-relative">
                                                                <!-- Delete Sub Category Button (only if can delete subcategory) -->
                                                                @can('metals.subcategory.delete')
                                                                    <button
                                                                        class="btn btn-sm btn-light text-danger position-absolute top-0 end-0 m-2 delete-sub"
                                                                        data-url="{{ route('metals.subcategory.delete', [1, $cat->category_id, $sub->id]) }}"
                                                                        data-wrapper="#gold-sub-{{ $cat->category_id }}-{{ $sub->id }}"
                                                                        data-name="{{ $sub->name }}">
                                                                        <i class="ri-close-line"></i>
                                                                    </button>
                                                                @endcan
                                                                <h6 class="fw-semibold mb-0">{{ $sub->name }}</h6>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-muted text-center py-5">No gold categories found.</p>
                                @endforelse
                            @endcan
                        </div>

                        {{-- ================= SILVER TAB ================= --}}
                        <div class="tab-pane fade" id="silverTab">
                            <div class="col-xl-12">
                                <div class="card shadow-sm border-0">
                                    <div class="card-body">
                                        <small class="text-muted mb-3">
                                            Last updated at: {{ $silver->updated_at ?? 'Never' }}
                                        </small>

                                        <div class="row g-3 mt-1 align-items-end">
                                            <!-- Price Input (only if can update) -->
                                            @can('metals.update')
                                                <div class="col-md-2">
                                                    <label for="silverPriceInput" class="form-label">
                                                        1 Gram Silver Price (USD) <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="number" id="silverPriceInput" name="price"
                                                        class="form-control" placeholder="{{ $silver->price_per_gram ?? '0' }}"
                                                        min="0.01" max="10000" step="0.01" required
                                                        value="{{ number_format($silver->price_per_gram, 2) ?? '' }}">
                                                </div>
                                            @endcan

                                            <!-- Update Price Button (only if can update) -->
                                            @can('metals.update')
                                                <div class="col-md-2 d-grid">
                                                    <button id="updateSilverPriceBtn" type="button"
                                                        class="btn btn-outline-primary btn-md fw-bold">
                                                        <i class="ri-refresh-line me-1 fs-5"></i> Update Price
                                                    </button>
                                                </div>
                                            @endcan

                                            <!-- Assign Category Button (only if can assign) -->
                                            <div class="col text-end">
                                                @can('metals.assign')
                                                    <a href="{{ route('metals.assign', 'silver') }}"
                                                        class="btn btn-primary btn-md px-4 fw-bold">
                                                        Assign New Category
                                                    </a>
                                                @else
                                                    @can('metals.view.any')
                                                        <div class="text-muted small">No permission to assign categories</div>
                                                    @endcan
                                                @endcan
                                            </div>

                                            <!-- Attention Message -->
                                            @can('metals.view.any')
                                                <div class="col-12">
                                                    <div class="alert alert-warning d-flex align-items-start gap-2"
                                                        role="alert">
                                                        <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                                                        <div>
                                                            <strong>Attention:</strong>
                                                            The current silver price is
                                                            ${{ $silver->price_per_gram ?? '0.00' }}.
                                                            @can('metals.update')
                                                                If you want to update the price, please enter the new value
                                                                carefully and click <strong>Update Price</strong>.
                                                            @endcan
                                                            Once updated, the prices of all products listed in
                                                            the categories below will be recalculated automatically based on
                                                            their weight.
                                                            This process may take some time to complete.
                                                        </div>
                                                    </div>
                                                </div>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Silver Categories List -->
                            @can('metals.view.any')
                                @forelse($silverCategory as $cat)
                                    @php
                                        $mainCategory = DB::table('categories')->find($cat->category_id);
                                        $subCategories = DB::table('categories')
                                            ->whereIn('id', array_filter(explode(',', $cat->sub_category_ids)))
                                            ->get();
                                    @endphp

                                    <div class="card border-0 shadow-sm mb-4" id="silver-main-{{ $cat->category_id }}">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="fw-semibold mb-0">
                                                    <a href="{{ route('categories.index') }}" target="_blank"
                                                        class="text-decoration-none text-dark">
                                                        {{ $mainCategory->name ?? 'Category Deleted' }}
                                                    </a>
                                                </h6>
                                                <small class="text-muted">
                                                    {!! redirect_to_link(
                                                        route('subcategories.index') . '?parent_id=' . $cat->category_id,
                                                        $subCategories->count() . ' Subcategories',
                                                    ) !!}
                                                </small>
                                            </div>

                                            <!-- Delete Main Category Button (only if can delete category) -->
                                            @can('metals.category.delete')
                                                <button class="btn btn-outline-danger btn-sm delete-main"
                                                    data-url="{{ route('metals.category.delete', [2, $cat->category_id]) }}"
                                                    data-wrapper="#silver-main-{{ $cat->category_id }}"
                                                    data-name="{{ $mainCategory->name ?? 'Category' }}">
                                                    <i class="ri-delete-bin-6-line"></i>
                                                </button>
                                            @endcan
                                        </div>

                                        <div class="card-body">
                                            <div class="row g-3">
                                                @foreach ($subCategories as $sub)
                                                    <div class="col-xl-3 col-lg-4 col-md-6"
                                                        id="silver-sub-{{ $cat->category_id }}-{{ $sub->id }}">
                                                        <div class="card border shadow-sm">
                                                            <div class="card-body p-3 position-relative">
                                                                <!-- Delete Sub Category Button (only if can delete subcategory) -->
                                                                @can('metals.subcategory.delete')
                                                                    <button
                                                                        class="btn btn-sm btn-light text-danger position-absolute top-0 end-0 m-2 delete-sub"
                                                                        data-url="{{ route('metals.subcategory.delete', [2, $cat->category_id, $sub->id]) }}"
                                                                        data-wrapper="#silver-sub-{{ $cat->category_id }}-{{ $sub->id }}"
                                                                        data-name="{{ $sub->name }}">
                                                                        <i class="ri-close-line"></i>
                                                                    </button>
                                                                @endcan
                                                                <h6 class="fw-semibold mb-0">{{ $sub->name }}</h6>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-muted text-center py-5">No silver categories found.</p>
                                @endforelse
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/plugins/jquery-confirm.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/plugins/select2.min.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('assets/admin/js/plugins/jquery-confirm.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/plugins/select2.min.js') }}"></script>

    <script>
        // Only initialize delete functions if user has permission
        $(document).on('click', '.delete-main, .delete-sub', function() {
            const url = $(this).data('url');
            const wrapper = $(this).data('wrapper');
            const name = $(this).data('name') || 'this item';

            $.confirm({
                title: 'Confirm Deletion',
                content: `Are you sure you want to delete <b>${name}</b>?`,
                type: 'red',
                buttons: {
                    confirm: {
                        text: 'Yes, Delete',
                        btnClass: 'btn-danger',
                        action: function() {
                            $.ajax({
                                url: url,
                                type: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                beforeSend: function() {
                                    $(wrapper).css('opacity', '0.5');
                                },
                                success: function(res) {
                                    successToast(res.message);
                                    $(wrapper).slideUp(300, function() {
                                        $(this).remove();
                                        // Only reload if no more items in tab
                                        if ($(this).closest('.tab-pane').find(
                                                '.card').length === 0) {
                                            location.reload();
                                        }
                                    });
                                },
                                error: function(xhr) {
                                    errorToast(xhr.responseJSON?.message ||
                                        'Delete failed');
                                    $(wrapper).css('opacity', '1');
                                }
                            });
                        }
                    },
                    cancel: {
                        text: 'Cancel'
                    }
                }
            });
        });

        // Update Gold Price (only if button exists)
        @can('metals.update')
            $(document).on('click', '#updateGoldPriceBtn', function() {
                const newPrice = $('#goldPriceInput').val();
                const currentPrice = '{{ $gold->price_per_gram ?? '0' }}';

                if (!newPrice || parseFloat(newPrice) <= 0) {
                    errorToast('Please enter a valid price');
                    return;
                }

                $.confirm({
                    title: 'Confirm Price Update',
                    content: `
                    <div class="text-start">
                        <p>You are about to update the gold price:</p>
                        <ul>
                            <li>Current Price: <b>$${currentPrice}</b></li>
                            <li>New Price: <b class="text-primary">$${newPrice}</b></li>
                        </ul>
                        <div class="alert alert-warning mt-3 mb-0">
                            <small><strong>Warning:</strong> All products in gold categories will be repriced automatically. This may take some time.</small>
                        </div>
                    </div>
                `,
                    type: 'orange',
                    buttons: {
                        confirm: {
                            text: 'Update Price',
                            btnClass: 'btn-primary',
                            action: function() {
                                const btn = $('#updateGoldPriceBtn');
                                const originalText = btn.html();

                                $.ajax({
                                    url: '{{ route('metals.update', encrypt(1)) }}',
                                    type: 'POST',
                                    data: {
                                        _method: 'PUT',
                                        price: newPrice,
                                        _token: $('meta[name="csrf-token"]').attr('content')
                                    },
                                    beforeSend: function() {
                                        btn.prop('disabled', true)
                                            .html(
                                                '<i class="ri-loader-4-line ri-spin me-1"></i> Updating...'
                                            );
                                    },
                                    success: function(res) {
                                        successToast(res.message ||
                                            'Gold price updated successfully');
                                        btn.prop('disabled', false).html(originalText);
                                        // Update the displayed price
                                        $('#goldPriceInput').val(newPrice);
                                        // Show success message
                                        successToast('Gold price updated to $' + newPrice);
                                    },
                                    error: function(xhr) {
                                        errorToast(xhr.responseJSON?.message ||
                                            'Price update failed');
                                        btn.prop('disabled', false).html(originalText);
                                    }
                                });
                            }
                        },
                        cancel: {
                            text: 'Cancel'
                        }
                    }
                });
            });

            // Update Silver Price (only if button exists)
            $(document).on('click', '#updateSilverPriceBtn', function() {
                const newPrice = $('#silverPriceInput').val();
                const currentPrice = '{{ $silver->price_per_gram ?? '0' }}';

                if (!newPrice || parseFloat(newPrice) <= 0) {
                    errorToast('Please enter a valid price');
                    return;
                }

                $.confirm({
                    title: 'Confirm Price Update',
                    content: `
                    <div class="text-start">
                        <p>You are about to update the silver price:</p>
                        <ul>
                            <li>Current Price: <b>$${currentPrice}</b></li>
                            <li>New Price: <b class="text-primary">$${newPrice}</b></li>
                        </ul>
                        <div class="alert alert-warning mt-3 mb-0">
                            <small>
                                <strong>Warning:</strong>
                                All products in silver categories will be repriced automatically.
                                This may take some time.
                            </small>
                        </div>
                    </div>
                `,
                    type: 'orange',
                    buttons: {
                        confirm: {
                            text: 'Update Price',
                            btnClass: 'btn-primary',
                            action: function() {
                                const btn = $('#updateSilverPriceBtn');
                                const originalText = btn.html();

                                $.ajax({
                                    url: '{{ route('metals.update', encrypt(2)) }}',
                                    type: 'POST',
                                    data: {
                                        _method: 'PUT',
                                        price: newPrice,
                                        _token: $('meta[name="csrf-token"]').attr('content')
                                    },
                                    beforeSend: function() {
                                        btn.prop('disabled', true)
                                            .html(
                                                '<i class="ri-loader-4-line ri-spin me-1"></i> Updating...'
                                            );
                                    },
                                    success: function(res) {
                                        successToast(res.message ||
                                            'Silver price updated successfully');
                                        btn.prop('disabled', false).html(originalText);
                                        // Update the displayed price
                                        $('#silverPriceInput').val(newPrice);
                                        // Show success message
                                        successToast('Silver price updated to $' +
                                            newPrice);
                                    },
                                    error: function(xhr) {
                                        errorToast(xhr.responseJSON?.message ||
                                            'Silver update failed');
                                        btn.prop('disabled', false).html(originalText);
                                    }
                                });
                            }
                        },
                        cancel: {
                            text: 'Cancel'
                        }
                    }
                });
            });
        @endcan
    </script>
@endpush
