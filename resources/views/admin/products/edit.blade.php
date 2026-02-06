@extends('layouts.admin')
@section('content')
    <div class="app-content-area">
        <div class="app-content-wrap">
            <div class="container-fluid">

                <!-- Page Header -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="page-title-box d-flex-between flex-wrap gap-15">
                            <h1 class="page-title fs-18 lh-1">Edit Product</h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb breadcrumb-example1 mb-0">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Edit Product</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <div class="row">
                    <form action="{{ route('products.update', encrypt($product->id)) }}" method="POST"
                        enctype="multipart/form-data" class="validate_form" id="productForm">
                        @csrf
                        @method('PUT')

                        <div class="row">

                            <!-- ================= LEFT COLUMN : PRODUCT INFO ================= -->
                            <div class="col-xxl-8 col-xl-8">
                                <div class="card">
                                    <div class="card-header justify-between">
                                        <h4>Product Information</h4>
                                        <a class="btn btn-light" href="{{ route('products.index') }}">Back to Products</a>
                                    </div>

                                    <div class="card-body pt-15">
                                        <div class="row">

                                            <!-- CATEGORY -->
                                            <!-- PARENT CATEGORY -->
                                            <div class="col-md-6 mb-15">
                                                <label for="filterParent" class="form-label fw-semibold mb-2">
                                                    <i class="ri-folder-line me-1"></i>Parent Category
                                                </label>

                                                <select id="filterParent" class="form-select select2 parent_category"
                                                    name="category_id" required>
                                                    <option value="">— Select Parent Category —</option>

                                                    @foreach ($categories as $parent)
                                                        <option value="{{ $parent->id }}"
                                                            {{ isset($product) && $product->category_id == $parent->id ? 'selected' : '' }}>
                                                            {{ $parent->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>


                                            <!-- SUB CATEGORY -->
                                            <div class="col-md-6 mb-15">
                                                <label class="form-label mt-3">Status <span
                                                        class="text-danger">*</span></label>
                                                <select name="status" class="form-control" required>
                                                    <option value="1" {{ $product->status == 1 ? 'selected' : '' }}>
                                                        Published
                                                        (Visible)</option>
                                                    <option value="0" {{ $product->status == 0 ? 'selected' : '' }}>
                                                        Draft
                                                        (Hidden)</option>
                                                </select>
                                            </div>

                                            <!-- PRODUCT NAME -->
                                            <div class="col-md-12 mb-15">
                                                <label class="form-label">Product Name <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="product_name" class="form-control"
                                                    placeholder="e.g. 18K Gold Ring for Women – Floral Design"
                                                    id="product_name" value="{{ old('product_name', $product->name) }}"
                                                    required>
                                                <small class="text-muted">
                                                    Clear, searchable title including material & type
                                                </small>
                                            </div>

                                            <!-- SLUG -->
                                            <div class="col-md-6 mb-15">
                                                <label class="form-label">Slug</label>
                                                <input type="text" name="slug" class="form-control"
                                                    placeholder="auto-generated-if-empty" id="slug"
                                                    value="{{ old('slug', $product->slug) }}">
                                                <small class="text-muted">
                                                    SEO-friendly URL (lowercase, hyphens only)
                                                </small>
                                            </div>

                                            <!-- SKU -->
                                            <div class="col-md-6 mb-15">
                                                <label class="form-label">SKU <span class="text-danger">*</span></label>
                                                <input type="text" name="sku" class="form-control"
                                                    placeholder="e.g. GR-WOM-001" value="{{ old('sku', $product->sku) }}"
                                                    required id="parent_product_sku">
                                                <small class="text-muted">Must be unique across all products</small>
                                            </div>

                                            <!-- MAIN IMAGE -->
                                            <div class="col-md-6 mb-15">
                                                <label class="form-label">Main Image <span
                                                        class="text-danger">*</span></label>
                                                <div class="mb-2">
                                                    @if ($product->main_image)
                                                        {!! image_show($product->main_image, 100, 100) !!}
                                                    @endif
                                                </div>
                                                <input type="file" name="main_image" class="form-control"
                                                    accept="image/*">
                                                <small class="text-muted">
                                                    Recommended: 1200×1200px • JPG/PNG/WebP • Max 5MB
                                                </small>
                                            </div>

                                            <!-- SECONDARY IMAGE -->
                                            <div class="col-md-6 mb-15">
                                                <label class="form-label">Secondary Image</label>
                                                <div class="mb-2">
                                                    @if ($product->secondary_image)
                                                        {!! image_show($product->secondary_image, 100, 100) !!}
                                                    @endif
                                                </div>
                                                <input type="file" name="secondary_image" class="form-control"
                                                    accept="image/*">
                                                <small class="text-muted">
                                                    Recommended: 1200×1200px • JPG/PNG/WebP • Max 5MB
                                                </small>
                                            </div>


                                            <!-- COST PRICE -->
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Cost Price</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" name="parent_cost_price"
                                                        class="form-control parent-price-input" placeholder="0.00"
                                                        min="0" max="9999999.99" step="0.01"
                                                        value="{{ $product->cost_price }}">
                                                </div>
                                            </div>

                                            <!-- MRP PRICE -->
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">MRP Price <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" name="parent_mrp_price"
                                                        class="form-control parent-price-input" placeholder="0.00"
                                                        min="1" max="9999999.99" step="0.01" required
                                                        value="{{ $product->mrp_price }}">
                                                </div>
                                            </div>

                                            <!-- SELLING PRICE -->
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Selling Price <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" name="parent_selling_price"
                                                        class="form-control parent-price-input" placeholder="0.00"
                                                        min="1" max="9999999.99" step="0.01" required
                                                        value="{{ $product->selling_price }}">
                                                </div>
                                            </div>



                                            <!-- TAX PERCENTAGE -->
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Tax Percentage</label>
                                                <div class="input-group">
                                                    <input type="number" name="tax_percentage" class="form-control"
                                                        placeholder="0.00" min="0" max="100" step="0.01"
                                                        value="{{ $product->tax_percentage }}">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                                <small class="text-muted">TAX percentage (e.g., 18.00)</small>
                                            </div>




                                            <!-- SHORT DESCRIPTION -->
                                            <div class="col-md-12 mb-15">
                                                <label class="form-label">Short Description <span
                                                        class="text-danger">*</span></label>
                                                <div id="short_editor" style="height:260px;">{!! old('short_description', $product->short_description) !!}</div>
                                                <input type="hidden" id="short_description" name="short_description"
                                                    value="{{ old('short_description', $product->short_description) }}">
                                                <small class="text-muted">
                                                    Shown on listing pages • Recommended 120–250 characters
                                                </small>
                                            </div>

                                            <!-- DESCRIPTION -->
                                            <div class="col-md-12 mb-15">
                                                <label class="form-label">Product Description</label>
                                                <small class="text-muted d-block mb-2">
                                                    Include material, size, weight, care instructions, warranty, etc.
                                                </small>
                                                <div id="editor" style="height:260px;">{!! old('description', $product->description) !!}</div>
                                                <input type="hidden" id="description" name="description"
                                                    value="{{ old('description', $product->description) }}">
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ================= RIGHT COLUMN : PUBLISH & SEO ================= -->
                            <div class="col-xxl-4 col-xl-4">

                                <!-- PUBLISH -->
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Publish</h4>
                                    </div>
                                    <div class="card-body">



                                        <label class="form-label mt-3">Personalize Product? <span
                                                class="text-danger">*</span></label>
                                        <select name="personalize" class="form-control" required>
                                            <option value="1" {{ $product->personalize == 1 ? 'selected' : '' }}>YES
                                                (Visible Frontend Option)</option>
                                            <option value="0" {{ $product->personalize == 0 ? 'selected' : '' }}>NO
                                            </option>
                                        </select>




                                        <label class="form-label mt-3">Status <span class="text-danger">*</span></label>
                                        <select name="status" class="form-control" required>
                                            <option value="1" {{ $product->status == 1 ? 'selected' : '' }}>Published
                                                (Visible)</option>
                                            <option value="0" {{ $product->status == 0 ? 'selected' : '' }}>Draft
                                                (Hidden)</option>
                                        </select>

                                        <label class="form-label mt-3">Marketing Label</label>
                                        <select name="marketing_label" class="form-control">
                                            <option value="">None</option>
                                            <option value="new" {{ $product->label == 'new' ? 'selected' : '' }}>New
                                                Arrival</option>
                                            <option value="trending"
                                                {{ $product->label == 'trending' ? 'selected' : '' }}>Trending</option>
                                            <option value="hot" {{ $product->label == 'hot' ? 'selected' : '' }}>Hot
                                                Deal</option>
                                            <option value="sale" {{ $product->label == 'sale' ? 'selected' : '' }}>On
                                                Sale</option>
                                            <option value="limited" {{ $product->label == 'limited' ? 'selected' : '' }}>
                                                Limited Edition</option>
                                            <option value="exclusive"
                                                {{ $product->label == 'exclusive' ? 'selected' : '' }}>Exclusive</option>
                                            <option value="popular" {{ $product->label == 'popular' ? 'selected' : '' }}>
                                                Popular</option>
                                            <option value="top_rated"
                                                {{ $product->label == 'top_rated' ? 'selected' : '' }}>Top Rated</option>
                                        </select>
                                        <small class="text-muted">Badge shown on product cards</small></br>

                                        <label class="form-label mt-3">Is Featured</label>
                                        <select name="is_featured" class="form-control">
                                            <option value="1" {{ $product->is_featured == 1 ? 'selected' : '' }}>Yes
                                                (Homepage)</option>
                                            <option value="0" {{ $product->is_featured == 0 ? 'selected' : '' }}>No
                                            </option>
                                        </select>

                                    </div>
                                </div>

                                <!-- SEO -->
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h4>SEO Information</h4>
                                    </div>
                                    <div class="card-body">

                                        <label class="form-label mt-3">Meta Title</label>
                                        <input type="text" name="seo_title" class="form-control"
                                            placeholder="e.g. Buy 18K Gold Ring for Women Online"
                                            value="{{ old('seo_title', $product->seo_title) }}">
                                        <small class="text-muted">Max 60 characters</small>

                                        <label class="form-label mt-3">SEO Image</label>
                                        <div class="mb-2">
                                            @if ($product->seo_image)
                                                <img src="{{ asset($product->seo_image) }}" alt="SEO Image"
                                                    style="max-width: 150px; max-height: 150px;" class="img-thumbnail">
                                            @endif
                                        </div>
                                        <input type="file" name="seo_image" class="form-control" accept="image/*">
                                        <small class="text-muted">Recommended: 1200×630px (Social sharing)</small> </br>
                                        <label class="form-label mt-3">Meta Description</label>
                                        <textarea name="seo_description" class="form-control" rows="3"
                                            placeholder="Short SEO description (max 160 characters)">{{ old('seo_description', $product->seo_description) }}</textarea>

                                        <label class="form-label mt-3">Meta Keywords</label>
                                        <select name="seo_keywords[]" class="form-control select2-tags" multiple
                                            data-placeholder="Type keyword & press Enter">


                                            @if ($product['seo_keywords'])
                                                @foreach (json_decode($product['seo_keywords']) as $keyword)
                                                    <option value="{{ $keyword }}" selected>{{ $keyword }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <small class="text-muted">Press Enter or comma after each keyword</small>

                                    </div>
                                </div>
                            </div>

                            <!-- ================= VARIANT OPTIONS ================= -->
                            <div class="col-xxl-12 col-xl-12">
                                <div class="card shadow-sm border-0 h-100">
                                    <div class="card-header border-bottom mt-4 py-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h5 class="mb-0 fw-semibold">Variant Options</h5>
                                                <p class="text-muted mb-0 mt-1">
                                                    Add attributes like Color, Size, Weight, Purity, etc.
                                                </p>
                                            </div>
                                            <button type="button" class="btn btn-primary add-variant-btn">
                                                <i class="bi bi-plus-circle me-1"></i>Add Variant Option
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="variant-options-container">
                                            <div class="variant-options">
                                                @if ($existingOptions && $existingOptions->count() > 0)
                                                    @foreach ($existingOptions as $attributeId => $combinations)
                                                        <div class="variant-option card mb-3 shadow-sm"
                                                            data-option-id="option-{{ $loop->index }}">
                                                            <div class="card-body">
                                                                <div class="row align-items-end">
                                                                    <div class="col-md-3">
                                                                        <label class="form-label">Attribute <span
                                                                                class="text-danger">*</span></label>
                                                                        <select
                                                                            class="form-control select2option option-name"
                                                                            required>
                                                                            <option value="">— Select Attribute —
                                                                            </option>
                                                                            @foreach ($attributes as $attribute)
                                                                                <option value="{{ $attribute->id }}"
                                                                                    {{ $attributeId == $attribute->id ? 'selected' : '' }}>
                                                                                    {{ $attribute->name }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-7">
                                                                        <label class="form-label">Values <span
                                                                                class="text-danger">*</span></label>



                                                                        <select class="form-control option-values" multiple
                                                                            required>
                                                                            @php
                                                                                // Filter attribute values by the current attribute ID
                                                                                $attributeValues = DB::table(
                                                                                    'attribute_values',
                                                                                )
                                                                                    ->select('id', 'value')
                                                                                    ->where(
                                                                                        'attribute_id',
                                                                                        $attributeId,
                                                                                    ) // Add this filter
                                                                                    ->get();
                                                                                $selectedValues = $combinations
                                                                                    ->pluck('attribute_value_id')
                                                                                    ->toArray();
                                                                            @endphp

                                                                            @foreach ($attributeValues as $attributeValue)
                                                                                <option value="{{ $attributeValue->id }}"
                                                                                    {{ in_array($attributeValue->id, $selectedValues) ? 'selected' : '' }}>
                                                                                    {{ $attributeValue->value }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>

                                                                    </div>
                                                                    <div class="col-md-2 text-end">
                                                                        <button type="button"
                                                                            class="btn btn-outline-danger btn-sm remove-option w-100">
                                                                            <i class="bi bi-trash me-1"></i>Remove
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="text-center py-4">
                                                        <p class="text-muted">No variant options added yet.</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ================= VARIANT COMBINATIONS ================= -->
                            <div class="col-xxl-12 col-xl-12">
                                <div class="card mt-4">
                                    <div class="card-header">
                                        <h5 class="mb-1">Variant Combinations</h5>
                                    </div>

                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered align-middle">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Variant</th>
                                                        <!-- SKU -->
                                                        <th class="th-filter">
                                                            <span>SKU *</span>
                                                            <div style="display: flex; align-items: center;">
                                                                <input type="text" id="main_product_sku"
                                                                    class="form-control form-control-sm parent-input"
                                                                    data-field="sku" placeholder="SKU" autocomplete="off"
                                                                    maxlength="32" style="flex: 1;">
                                                                <button type="button"
                                                                    class="btn btn-outline-secondary btn-sm generate-sku-btn"
                                                                    title="Generate SKU">
                                                                    ⚡
                                                                </button>
                                                            </div>
                                                        </th>

                                                        <!-- Weight -->
                                                        <th class="th-filter">
                                                            <span>Weight (g) *</span>
                                                            <div style="display: flex; align-items: center;">
                                                                <input type="number" id="main_product_weight"
                                                                    class="form-control form-control-sm parent-input"
                                                                    data-field="weight" placeholder="Weight"
                                                                    min="0" max="50000" step="1"
                                                                    inputmode="numeric" style="flex: 1;">
                                                                <button type="button"
                                                                    class="btn btn-outline-secondary btn-sm apply-to-all-btn"
                                                                    data-field="weight" title="Apply to All">
                                                                    ⚡
                                                                </button>
                                                            </div>
                                                        </th>

                                                        <!-- Cost Price -->
                                                        <th class="th-filter">
                                                            <span>Cost Price</span>
                                                            <div style="display: flex; align-items: center;">
                                                                <input type="number" id="main_cost_price"
                                                                    class="form-control form-control-sm parent-input"
                                                                    data-field="cost_price" placeholder="Cost Price"
                                                                    min="0" max="1000000" step="0.01"
                                                                    inputmode="decimal" style="flex: 1;">
                                                                <button type="button"
                                                                    class="btn btn-outline-secondary btn-sm apply-to-all-btn"
                                                                    data-field="cost_price" title="Apply to All">
                                                                    ⚡
                                                                </button>
                                                            </div>
                                                        </th>

                                                        <!-- MRP -->
                                                        <th class="th-filter">
                                                            <span>MRP *</span>
                                                            <div style="display: flex; align-items: center;">
                                                                <input type="number" id="main_mrp_price"
                                                                    class="form-control form-control-sm parent-input"
                                                                    data-field="mrp_price" placeholder="MRP Price"
                                                                    min="1" max="1000000" step="0.01"
                                                                    inputmode="decimal" style="flex: 1;">
                                                                <button type="button"
                                                                    class="btn btn-outline-secondary btn-sm apply-to-all-btn"
                                                                    data-field="mrp_price" title="Apply to All">
                                                                    ⚡
                                                                </button>
                                                            </div>
                                                        </th>

                                                        <!-- Selling Price -->
                                                        <th class="th-filter">
                                                            <span>Selling Price *</span>
                                                            <div style="display: flex; align-items: center;">
                                                                <input type="number" id="main_selling_price"
                                                                    class="form-control form-control-sm parent-input"
                                                                    data-field="selling_price" placeholder="Selling Price"
                                                                    min="0" max="1000000" step="0.01"
                                                                    inputmode="decimal" style="flex: 1;">
                                                                <button type="button"
                                                                    class="btn btn-outline-secondary btn-sm apply-to-all-btn"
                                                                    data-field="selling_price" title="Apply to All">
                                                                    ⚡
                                                                </button>
                                                            </div>
                                                        </th>

                                                        <!-- Quantity -->
                                                        <th class="th-filter">
                                                            <span>Quantity *</span>
                                                            <div style="display: flex; align-items: center;">
                                                                <input type="number"
                                                                    class="form-control form-control-sm parent-input"
                                                                    data-field="quantity" placeholder="Qty"
                                                                    min="0" max="99999" step="1"
                                                                    inputmode="numeric" style="flex: 1;">
                                                                <button type="button"
                                                                    class="btn btn-outline-secondary btn-sm apply-to-all-btn"
                                                                    data-field="quantity" title="Apply to All">
                                                                    ⚡
                                                                </button>
                                                            </div>
                                                        </th>

                                                        <th>Images</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="variant-combinations">
                                                    @foreach ($product->variants as $variant)
                                                        <tr data-variant-id="{{ $variant->id }}"
                                                            data-combination="{{ $variant->variant_name }}">
                                                            <td>
                                                                <strong
                                                                    class="variant-name">{{ $variant->variant_name }}</strong>
                                                                <input type="hidden"
                                                                    name="variants[{{ $loop->index }}][id]"
                                                                    value="{{ $variant->id }}">
                                                                <input type="hidden"
                                                                    name="variants[{{ $loop->index }}][name]"
                                                                    value="{{ $variant->variant_name }}">
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control form-control-sm"
                                                                    name="variants[{{ $loop->index }}][sku]"
                                                                    value="{{ $variant->sku }}" required>
                                                            </td>
                                                            <td>
                                                                <input type="number" step="0.01" min="0"
                                                                    class="form-control form-control-sm"
                                                                    name="variants[{{ $loop->index }}][weight]"
                                                                    value="{{ $variant->weight }}" placeholder="0"
                                                                    required>
                                                            </td>
                                                            <td>
                                                                <input type="number" step="0.01" min="0"
                                                                    class="form-control form-control-sm variant-cost-price"
                                                                    name="variants[{{ $loop->index }}][cost_price]"
                                                                    value="{{ $variant->cost_price }}">
                                                            </td>

                                                            <td>
                                                                <input type="number" step="0.01" min="0"
                                                                    class="form-control form-control-sm variant-mrp-price"
                                                                    name="variants[{{ $loop->index }}][mrp_price]"
                                                                    value="{{ $variant->mrp_price }}" required>
                                                            </td>


                                                            <td>
                                                                <input type="number" step="0.01" min="0"
                                                                    class="form-control form-control-sm variant-selling-price"
                                                                    name="variants[{{ $loop->index }}][selling_price]"
                                                                    value="{{ $variant->selling_price }}" required>
                                                            </td>
                                                            <td>
                                                                <input type="number" min="0"
                                                                    class="form-control form-control-sm"
                                                                    name="variants[{{ $loop->index }}][quantity]"
                                                                    value="{{ $variant->stock_quantity }}" required>
                                                            </td>
                                                            <td>
                                                                <div class="mb-2">
                                                                    @foreach ($variant->images as $image)
                                                                        {!! image_show_with_delete($image->image_url, 50, 50, route('products.image.delete', encrypt($image->id))) !!}
                                                                    @endforeach
                                                                </div>

                                                                <input type="file" class="form-control form-control-sm"
                                                                    name="variants[{{ $loop->index }}][images][]"
                                                                    accept="image/jpeg,image/jpg,image/png,image/webp"
                                                                    multiple>
                                                            </td>
                                                            <td class="text-center">
                                                                <button type="button"
                                                                    class="btn btn-sm btn-danger remove-variant-combination"
                                                                    title="Remove this variant">
                                                                    Remove
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- ================= SUBMIT ================= -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card mt-3">
                                    <div class="card-footer bg-light">
                                        <button class="btn btn-primary btn-lg submit_form" type="submit">
                                            <i class="bi bi-check-circle me-2"></i>Update Product
                                        </button>
                                        <a href="{{ route('products.index') }}"
                                            class="btn btn-outline-secondary">Cancel</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>

                <!-- VARIANT OPTION TEMPLATE (Hidden) -->
                <div class="d-none">
                    <div class="variant-option-template">
                        <div class="variant-option card mb-3 shadow-sm">
                            <div class="card-body">
                                <div class="row align-items-end">
                                    <!-- OPTION NAME -->
                                    <div class="col-md-3">
                                        <label class="form-label">Attribute <span class="text-danger">*</span></label>
                                        <select class="form-control select2option option-name" required>
                                            <option value="">— Select Attribute —</option>
                                            @foreach ($attributes as $attribute)
                                                <option value="{{ $attribute->id }}">{{ $attribute->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- OPTION VALUES -->
                                    <div class="col-md-7">
                                        <label class="form-label">Values <span class="text-danger">*</span></label>
                                        <select class="form-control option-values" multiple required>
                                            <!-- Values loaded dynamically via AJAX -->
                                        </select>
                                    </div>

                                    <!-- REMOVE BUTTON -->
                                    <div class="col-md-2 text-end">
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-option w-100">
                                            <i class="bi bi-trash me-1"></i>Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/plugins/select2.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/plugins/jquery-confirm.min.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('assets/admin/js/plugins/select2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script src="{{ asset('assets/admin/js/plugins/jquery-confirm.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            'use strict';

            // ========================================
            // GLOBAL VARIABLES
            // ========================================
            let variantOptionCount = {{ $existingOptions->count() ?? 0 }};
            let attributeValuesCache = {};
            let existingVariantOptions = @json($existingOptions ?? []);
            let quill, short_quill;

            // ========================================
            // INITIALIZATION
            // ========================================

            /**
             * Initialize Select2 for standard dropdowns
             */
            function initSelect2(context = document) {
                $(context).find('.select2').not('.select2-hidden-accessible').each(function() {
                    $(this).select2({
                        width: 'resolve',
                        placeholder: $(this).data('placeholder') || 'Select option',
                        allowClear: true,
                        dropdownParent: $(this).parent(),
                    });
                });

                $(context).find('.select2-tags').not('.select2-hidden-accessible').each(function() {
                    $(this).select2({
                        tags: true,
                        tokenSeparators: [','],
                        width: 'resolve',
                        placeholder: 'Type & press Enter',
                        dropdownParent: $(this).parent(),
                    });
                });
            }

            /**
             * Initialize Select2 for variant options
             */
            function initVariantSelect2($element) {
                $element.find('.option-name').not('.select2-hidden-accessible').select2({
                    width: '100%',
                    placeholder: 'Select attribute',
                    dropdownParent: $element.find('.option-name').parent()
                });

                $element.find('.option-values').not('.select2-hidden-accessible').select2({
                    width: '100%',
                    placeholder: 'Select values',
                    closeOnSelect: false,
                    dropdownParent: $element.find('.option-values').parent()
                });
            }

            /**
             * Initialize Quill Editors
             */
            function initQuillEditors() {
                const toolbarOptions = [
                    [{
                        font: []
                    }, {
                        size: ['small', false, 'large', 'huge']
                    }],
                    [{
                        header: [1, 2, 3, 4, 5, 6, false]
                    }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{
                        color: []
                    }, {
                        background: []
                    }],
                    [{
                        script: 'sub'
                    }, {
                        script: 'super'
                    }],
                    [{
                        list: 'ordered'
                    }, {
                        list: 'bullet'
                    }],
                    [{
                        indent: '-1'
                    }, {
                        indent: '+1'
                    }],
                    [{
                        align: []
                    }],
                    ['blockquote', 'code-block'],
                    ['link', 'image', 'video'],
                    ['clean']
                ];

                quill = new Quill('#editor', {
                    theme: 'snow',
                    modules: {
                        toolbar: toolbarOptions
                    }
                });

                short_quill = new Quill('#short_editor', {
                    theme: 'snow',
                    modules: {
                        toolbar: [
                            ['bold', 'italic', 'underline'],
                            [{
                                list: 'ordered'
                            }, {
                                list: 'bullet'
                            }],
                            ['link', 'clean']
                        ]
                    }
                });

                quill.on('text-change', function() {
                    $('#description').val(quill.root.innerHTML);
                });

                short_quill.on('text-change', function() {
                    $('#short_description').val(short_quill.root.innerHTML);
                });
            }

            // Initialize all components
            initSelect2();
            initQuillEditors();

            // Initialize select2 for existing variant options
            $('.variant-options .variant-option').each(function() {
                initVariantSelect2($(this));
            });

            // ========================================
            // AUTO SLUG GENERATION
            // ========================================
            $('#product_name').on('keyup change', function() {
                let slug = $(this).val()
                    .toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .trim();
                $('#slug').val(slug);
            });

            // ========================================
            // CATEGORY → SUBCATEGORY AJAX
            // ========================================
            $('.main_category').on('change', function() {
                let categoryId = $(this).val();
                let $sub = $('.sub_category');

                if (!categoryId) {
                    $sub.html('<option value="">Select Sub Category</option>');
                    $sub.trigger('change');
                    return;
                }

                $sub.html('<option value="">Loading...</option>');
                $sub.prop('disabled', true);

                let url = "{{ route('subcategories.ajax', ':id') }}".replace(':id', categoryId);

                $.get(url, function(data) {
                    $sub.html('<option value="">Select Sub Category</option>');
                    $.each(data, function(_, item) {
                        $sub.append(`<option value="${item.id}">${item.name}</option>`);
                    });
                    $sub.prop('disabled', false);

                    let currentSub = "{{ $product->sub_category_id ?? '' }}";
                    if (currentSub) {
                        $sub.val(currentSub).trigger('change');
                    }
                }).fail(function() {
                    $sub.html('<option value="">Error loading data</option>');
                    $sub.prop('disabled', false);
                });
            });

            // ========================================
            // VARIANT OPTIONS - DYNAMIC ATTRIBUTE VALUES
            // ========================================

            /**
             * Populate values dropdown from cache or server
             */
            function populateValues($select, values) {
                let currentValues = $select.val() || [];

                if ($select.hasClass('select2-hidden-accessible')) {
                    $select.select2('destroy');
                }

                $select.empty();

                if (values.response) {
                    values.response.forEach(v => {
                        $select.append(`<option value="${v.id}">${v.value}</option>`);
                    });
                } else {
                    values.forEach(v => {
                        $select.append(`<option value="${v.id}">${v.value}</option>`);
                    });
                }

                if (currentValues.length > 0) {
                    $select.val(currentValues);
                }

                $select.select2({
                    width: '100%',
                    placeholder: 'Select values',
                    closeOnSelect: false,
                    dropdownParent: $select.parent()
                });

                $select.prop('disabled', false);
                generateCombinations();
            }

            /**
             * Load attribute values when attribute changes
             */
            $(document).on('change', '.option-name', function() {
                let attributeId = $(this).val();
                let $valueSelect = $(this).closest('.variant-option').find('.option-values');

                if (!attributeId) {
                    if ($valueSelect.hasClass('select2-hidden-accessible')) {
                        $valueSelect.select2('destroy');
                    }
                    $valueSelect.empty().select2({
                        width: '100%',
                        placeholder: 'Select values',
                        closeOnSelect: false,
                        dropdownParent: $valueSelect.parent()
                    });
                    generateCombinations();
                    return;
                }

                // Check cache first
                if (attributeValuesCache[attributeId]) {
                    populateValues($valueSelect, attributeValuesCache[attributeId]);
                    return;
                }

                // Show loading
                $valueSelect.prop('disabled', true);

                let url = "{{ route('attributes.ajax', ':id') }}".replace(':id', attributeId);

                $.get(url, function(values) {
                    attributeValuesCache[attributeId] = values;
                    populateValues($valueSelect, values);
                }).fail(function() {
                    alert('Error loading attribute values');
                    $valueSelect.prop('disabled', false);
                });
            });

            // ========================================
            // VARIANT OPTIONS - ADD/REMOVE
            // ========================================

            /**
             * Add new variant option
             */
            $('.add-variant-btn').on('click', function() {
                let $template = $('.variant-option-template .variant-option').first().clone();
                let optionId = 'option-' + variantOptionCount++;

                $template.attr('data-option-id', optionId);
                $template.find('.option-name').val('');
                $template.find('.option-values').empty();

                $('.variant-options').append($template);
                initVariantSelect2($template);
            });

            /**
             * Remove variant option
             */
            $(document).on('click', '.remove-option', function() {
                let $option = $(this).closest('.variant-option');

                $.confirm({
                    title: 'Delete Variant Option?',
                    content: 'Deleting this option will also remove all related variant combinations. This action cannot be undone.',
                    type: 'orange',
                    buttons: {
                        confirm: {
                            text: 'Yes, Remove',
                            btnClass: 'btn-orange',
                            action: function() {
                                $option.remove();
                                generateCombinations();
                            }
                        },
                        cancel: {
                            text: 'Cancel'
                        }
                    }
                });
            });

            /**
             * Regenerate combinations when values change
             */
            $(document).on('change', '.option-values', function() {
                generateCombinations();
            });

            // ========================================
            // VARIANT COMBINATIONS - GENERATION
            // ========================================

            /**
             * Cartesian product helper function
             */
            function cartesian(arrays) {
                if (!arrays || arrays.length === 0) return [];

                return arrays.reduce((acc, curr) => {
                    if (acc.length === 0) return curr.map(item => [item]);

                    const result = [];
                    acc.forEach(a => {
                        curr.forEach(b => {
                            result.push([...a, b]);
                        });
                    });
                    return result;
                }, []);
            }

            /**
             * Generate variant combinations from selected options
             */
            function generateCombinations() {
                let options = [];
                let optionData = [];
                let hasValidOptions = false;

                // Collect all variant options
                $('.variant-options .variant-option').each(function() {
                    let $option = $(this);
                    let attributeId = $option.find('.option-name').val();
                    let selectedValues = $option.find('.option-values').val();
                    let values = [];

                    if (attributeId && selectedValues && selectedValues.length > 0) {
                        hasValidOptions = true;
                        $option.find('.option-values option:selected').each(function() {
                            values.push({
                                id: $(this).val(),
                                text: $(this).text().trim()
                            });
                        });
                        options.push(values.map(v => v.text));
                        optionData.push({
                            attribute_id: attributeId,
                            values: values
                        });
                    }
                });

                let $comboBody = $('.variant-combinations');

                // Remove existing hidden input
                $('input[name="variant_options"]').remove();

                // If no valid options, clear generated variants
                if (!hasValidOptions || options.length === 0) {
                    $comboBody.find('tr').each(function() {
                        let hasId = $(this).find('input[name*="[id]"]').length > 0 &&
                            $(this).find('input[name*="[id]"]').val();
                        if (!hasId) {
                            $(this).remove();
                        }
                    });
                    return;
                }

                // Create hidden input for option data
                $comboBody.before(
                    `<input type="hidden" name="variant_options" value='${JSON.stringify(optionData)}'>`
                );

                // Generate combinations
                let combinations = cartesian(options);

                // Track existing variants
                let existingVariants = new Map();
                $comboBody.find('tr').each(function() {
                    let variantName = $(this).find('input[name*="[name]"]').val();
                    let hasId = $(this).find('input[name*="[id]"]').val();
                    if (variantName) {
                        existingVariants.set(variantName, {
                            row: $(this),
                            hasId: hasId
                        });
                    }
                });

                // Track valid combinations
                let validCombinations = new Set();
                combinations.forEach((combo) => {
                    validCombinations.add(combo.join(' / '));
                });

                // Remove invalid variants
                existingVariants.forEach((data, variantName) => {
                    if (!validCombinations.has(variantName) && !data.hasId) {
                        data.row.remove();
                    }
                });

                // Add new combinations
                combinations.forEach((combo) => {
                    let variantName = combo.join(' / ');

                    if (existingVariants.has(variantName)) {
                        return;
                    }

                    let variantId = combo.join('-').toLowerCase().replace(/[^a-z0-9]/g, '-');
                    let index = $comboBody.find('tr').length;

                    let newRow = `
                    <tr data-variant-id="${variantId}" data-combination="${variantName}">
                        <td>
                            <strong class="variant-name">${variantName}</strong>
                            <input type="hidden" name="variants[${index}][name]" value="${variantName}">
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm" 
                                   name="variants[${index}][sku]" placeholder="SKU-${index + 1}" required>
                        </td>
                        <td>
                            <input type="number" step="0.01" min="0" class="form-control form-control-sm" 
                                   name="variants[${index}][weight]" placeholder="0" required>
                        </td>
                        <td>
                            <input type="number" step="0.01" min="0" 
                                   class="form-control form-control-sm variant-cost-price" 
                                   name="variants[${index}][cost_price]" placeholder="0">
                        </td>
                        <td>
                            <input type="number" step="0.01" min="0" 
                                   class="form-control form-control-sm variant-mrp-price" 
                                   name="variants[${index}][mrp_price]" placeholder="0" required>
                        </td>
                        <td>
                            <input type="number" step="0.01" min="0" 
                                   class="form-control form-control-sm variant-selling-price" 
                                   name="variants[${index}][selling_price]" placeholder="0" required>
                        </td>
                        <td>
                            <input type="number" min="0" class="form-control form-control-sm" 
                                   name="variants[${index}][quantity]" placeholder="0" required>
                        </td>
                        <td>
                            <input type="file" class="form-control form-control-sm" 
                                   name="variants[${index}][images][]" 
                                   accept="image/jpeg,image/jpg,image/png,image/webp" multiple>
                            <small class="text-muted d-block mt-1">Max 5 images, 1MB each</small>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-danger remove-variant-combination" title="Remove this variant">
                               Remove
                            </button>
                        </td>
                    </tr>
                `;
                    $comboBody.append(newRow);
                });

                reindexVariants();
            }

            /**
             * Reindex variant input names
             */
            function reindexVariants() {
                let $comboBody = $('.variant-combinations');
                let index = 0;

                $comboBody.find('tr:visible').each(function() {
                    let $row = $(this);

                    $row.find('input, select').each(function() {
                        let $input = $(this);
                        let name = $input.attr('name');

                        if (name && name.includes('variants[')) {
                            name = name.replace(/variants\[\d+\]/, `variants[${index}]`);
                            $input.attr('name', name);
                        }
                    });

                    index++;
                });
            }

            // ========================================
            // VARIANT COMBINATIONS - REMOVE
            // ========================================

            $(document).on('click', '.remove-variant-combination', function() {
                let $row = $(this).closest('tr');
                let variantName = $row.find('input[name*="[name]"]').val() || 'this variant';
                let hasId = $row.find('input[name*="[id]"]').val();

                if (hasId) {
                    // Existing variant → mark for deletion
                    $row.append(`<input type="hidden" name="deleted_variants[]" value="${hasId}">`);
                    $row.hide();
                } else {
                    // New variant → remove directly
                    $row.remove();
                    reindexVariants();
                }
            });

            // ========================================
            // BULK APPLY FUNCTIONALITY
            // ========================================

            /**
             * Apply SKU to all variants
             */
            function applySKUToAllVariants(parentSKU) {
                if (!parentSKU) return;

                $('.variant-combinations tr:visible').each(function() {
                    let $row = $(this);
                    let combination = $row.data('combination') ||
                        $row.find('input[name$="[name]"]').val() ||
                        $row.find('.variant-name').text() || '';

                    if (!combination) return;

                    let cleanCombination = combination
                        .toUpperCase()
                        .replace(/\s+/g, '-')
                        .replace(/[^A-Z0-9-]/g, '')
                        .replace(/-+/g, '-')
                        .replace(/^-|-$/g, '');

                    let finalSku = parentSKU + '-' + cleanCombination;
                    $row.find('input[name*="[sku]"]').val(finalSku);
                });
            }

            /**
             * Apply value to all variants
             */
            function applyToAllVariants(field, value, showAlert = false) {
                if (!value && value !== '0' && value !== 0) return;

                let appliedCount = 0;

                $('.variant-combinations tr:visible').each(function() {
                    let $childInput = $(this).find('input[name*="[' + field + ']"]');

                    if ($childInput.length) {
                        $childInput.val(value);
                        $childInput.removeClass('is-invalid');
                        appliedCount++;
                    }
                });

                if (showAlert && appliedCount > 0) {
                    alert(`${field.replace('_', ' ')} applied to all ${appliedCount} variants successfully!`);
                }
            }

            /**
             * Real-time input sync from parent to variants
             */
            $(document).on('input', '.parent-input', function() {
                let $input = $(this);
                let field = $input.data('field');
                let val = $input.val();

                if (!field) return;

                // Format validation
                if (field === 'weight') {
                    val = val.replace(/\D/g, '').substring(0, 6);
                    if (Number(val) > 50000) val = '50000';
                    $input.val(val);
                } else if (['cost_price', 'mrp_price', 'selling_price'].includes(field)) {
                    val = val.replace(/[^0-9.]/g, '');
                    if ((val.match(/\./g) || []).length > 1) {
                        val = val.slice(0, -1);
                    }
                    if (val.includes('.')) {
                        let [i, d] = val.split('.');
                        val = i.substring(0, 7) + '.' + d.substring(0, 2);
                    } else {
                        val = val.substring(0, 7);
                    }
                    if (Number(val) > 1000000) val = '1000000';
                    $input.val(val);
                } else if (field === 'quantity') {
                    val = val.replace(/\D/g, '').substring(0, 5);
                    if (Number(val) > 99999) val = '99999';
                    $input.val(val);
                } else if (field === 'sku') {
                    val = val
                        .toUpperCase()
                        .replace(/\s+/g, '-')
                        .replace(/[^A-Z0-9-]/g, '')
                        .replace(/-+/g, '-')
                        .replace(/^-|-$/g, '');
                    $input.val(val);
                }

                // Auto-apply to variants
                if (field === 'sku') {
                    applySKUToAllVariants(val);
                } else {
                    applyToAllVariants(field, val, false);
                }
            });

            /**
             * Apply to All button click
             */
            $(document).on('click', '.apply-to-all-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();

                let field = $(this).data('field');
                let $input = $(this).siblings('.parent-input[data-field="' + field + '"]');

                if ($input.length === 0) {
                    $input = $(this).closest('.th-filter').find('.parent-input[data-field="' + field +
                        '"]');
                }

                let val = $input.val();

                if (!val && val !== '0') {
                    alert('Please enter a value first!');
                    $input.focus();
                    return;
                }

                applyToAllVariants(field, val, true);
            });

            /**
             * Generate SKU button
             */
            $(document).on('click', '.generate-sku-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();

                let parentProductSku = $('#parent_product_sku').val()?.trim();
                if (!parentProductSku) {
                    alert('Please enter Parent Product SKU first!');
                    $('#parent_product_sku').focus();
                    return;
                }

                $('#main_product_sku').val(parentProductSku);
                $('#main_product_sku').trigger('input');

                let variantCount = $('.variant-combinations tr:visible').length;
                if (variantCount > 0) {
                    alert(`SKU generated and applied to ${variantCount} variants!`);
                }
            });

            // ========================================
            // PRICE VALIDATION
            // ========================================

            /**
             * Validate price logic for a row
             */
            function validatePriceLogic($row) {
                let costPrice = parseFloat($row.find('input[name*="[cost_price]"]').val()) || 0;
                let mrpPrice = parseFloat($row.find('input[name*="[mrp_price]"]').val()) || 0;
                let sellingPrice = parseFloat($row.find('input[name*="[selling_price]"]').val()) || 0;

                let isValid = true;
                let errorMsg = '';

                if (costPrice > 0 && mrpPrice > 0 && costPrice > mrpPrice) {
                    errorMsg = 'Cost Price cannot be greater than MRP Price!';
                    isValid = false;
                }

                if (mrpPrice > 0 && sellingPrice > mrpPrice) {
                    errorMsg = 'Selling Price cannot be greater than MRP Price!';
                    isValid = false;
                }

                if (costPrice > 0 && sellingPrice > 0 && sellingPrice < costPrice) {
                    console.warn('Warning: Selling Price is less than Cost Price');
                }

                return {
                    isValid,
                    errorMsg
                };
            }

            /**
             * Real-time price validation
             */
            $(document).on('input', '.variant-selling-price', function() {
                let $row = $(this).closest('tr');
                let mrpPrice = parseFloat($row.find('.variant-mrp-price').val()) || 0;
                let sellingPrice = parseFloat($(this).val()) || 0;

                if (mrpPrice > 0 && sellingPrice > mrpPrice) {
                    alert('Selling price cannot be greater than MRP price!');
                    $(this).val(mrpPrice.toFixed(2));
                }
            });

            $(document).on('input', '.variant-mrp-price', function() {
                let $row = $(this).closest('tr');
                let $sellingInput = $row.find('.variant-selling-price');
                let mrpPrice = parseFloat($(this).val()) || 0;
                let sellingPrice = parseFloat($sellingInput.val()) || 0;

                if (sellingPrice > mrpPrice && mrpPrice > 0) {
                    $sellingInput.val(mrpPrice.toFixed(2));
                }
            });

            // ========================================
            // FORM VALIDATION & SUBMISSION
            // ========================================

            $('#productForm').on('submit', function(e) {
                e.preventDefault();

                let isValid = true;
                let errorMessages = [];

                // Update Quill editors
                $('#description').val(quill.root.innerHTML.trim());
                $('#short_description').val(short_quill.root.innerHTML.trim());

                // Validate required fields
                $('[required]:visible').not('.select2-hidden-accessible').each(function() {
                    let $field = $(this);
                    let val = $field.val();

                    if (val === '' || val === null || (Array.isArray(val) && val.length === 0)) {
                        isValid = false;
                        let fieldName = $field.closest('.mb-15, .mb-3, .col-md-6, .col-md-12')
                            .find('.form-label').first().text().trim() || 'This field';
                        errorMessages.push(`${fieldName} is required`);
                        $field.addClass('is-invalid');
                    } else {
                        $field.removeClass('is-invalid');
                    }
                });

                // Validate variant combinations
                $('.variant-combinations tr:visible').each(function() {
                    let variantName = $(this).find('.variant-name').text() || 'Variant';
                    let sku = $(this).find('input[name*="[sku]"]').val();
                    let weight = $(this).find('input[name*="[weight]"]').val();
                    let mrpPrice = parseFloat($(this).find('input[name*="[mrp_price]"]').val());
                    let sellingPrice = parseFloat($(this).find('input[name*="[selling_price]"]')
                        .val());
                    let quantity = $(this).find('input[name*="[quantity]"]').val();

                    if (!sku || sku.trim() === '') {
                        isValid = false;
                        errorMessages.push(`${variantName}: SKU is required`);
                    }

                    if (!weight || weight.trim() === '') {
                        isValid = false;
                        errorMessages.push(`${variantName}: Weight is required`);
                    }

                    if (isNaN(mrpPrice) || mrpPrice <= 0) {
                        isValid = false;
                        errorMessages.push(`${variantName}: MRP must be greater than 0`);
                    }

                    if (isNaN(sellingPrice) || sellingPrice <= 0) {
                        isValid = false;
                        errorMessages.push(`${variantName}: Selling price must be greater than 0`);
                    }

                    if (quantity === '' || isNaN(quantity) || parseInt(quantity) < 0) {
                        isValid = false;
                        errorMessages.push(`${variantName}: Quantity must be 0 or more`);
                    }

                    if (!isNaN(mrpPrice) && !isNaN(sellingPrice) && sellingPrice > mrpPrice) {
                        isValid = false;
                        errorMessages.push(`${variantName}: Selling price cannot exceed MRP`);
                    }
                });

                // Show errors or submit
                if (!isValid) {
                    $.alert({
                        title: 'Form Validation Error',
                        type: 'red',
                        content: `
                            <ul style="padding-left:20px; text-align:left;">
                                ${[...new Set(errorMessages)].map(msg => `<li>${msg}</li>`).join('')}
                            </ul>
                        `,
                        buttons: {
                            ok: {
                                text: 'Fix Errors',
                                btnClass: 'btn-red'
                            }
                        }
                    });
                    return false;
                }

            });

            // ========================================
            // LOAD EXISTING VARIANT OPTIONS
            // ========================================

            if (existingVariantOptions && Object.keys(existingVariantOptions).length > 0) {
                let loadedCount = 0;
                let totalOptions = Object.keys(existingVariantOptions).length;

                Object.entries(existingVariantOptions).forEach(([attributeId, option], index) => {
                    let $existingOption = $(`.variant-option[data-option-id="option-${index}"]`);

                    if ($existingOption.length > 0) {
                        let $optionName = $existingOption.find('.option-name');
                        $optionName.val(attributeId).trigger('change.select2');

                        if (option.values && option.values.length > 0) {
                            let $valueSelect = $existingOption.find('.option-values');
                            let url = "{{ route('attributes.ajax', ':id') }}".replace(':id', attributeId);

                            $.get(url, function(values) {
                                attributeValuesCache[attributeId] = values;

                                if ($valueSelect.hasClass('select2-hidden-accessible')) {
                                    $valueSelect.select2('destroy');
                                }

                                $valueSelect.empty();

                                if (values.response) {
                                    values.response.forEach(v => {
                                        $valueSelect.append(
                                            `<option value="${v.id}">${v.value}</option>`
                                        );
                                    });
                                }

                                let selectedIds = option.values.map(v => v.id.toString());
                                $valueSelect.val(selectedIds);

                                $valueSelect.select2({
                                    width: '100%',
                                    placeholder: 'Select values',
                                    closeOnSelect: false,
                                    dropdownParent: $valueSelect.parent()
                                });

                                loadedCount++;
                                if (loadedCount === totalOptions) {
                                    setTimeout(generateCombinations, 300);
                                }
                            }).fail(function() {
                                loadedCount++;
                                if (loadedCount === totalOptions) {
                                    generateCombinations();
                                }
                            });
                        } else {
                            loadedCount++;
                            if (loadedCount === totalOptions) {
                                generateCombinations();
                            }
                        }
                    }
                });
            }

            console.log('Product edit script initialized successfully');
        });
    </script>
@endpush
