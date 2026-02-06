@extends('layouts.admin')
@section('content')
    <style>
        .sku-generate-wrap {
            display: flex;
            gap: 6px;
            align-items: center;
        }

        .sku-generate-wrap input {
            flex: 1;
        }

        .generate-sku-btn {
            white-space: nowrap;
        }


        .th-filter {
            padding: 6px;
            vertical-align: middle;
        }

        .th-filter span {
            display: block;
            font-weight: 600;
            margin-bottom: 4px;
            white-space: nowrap;
        }

        .table-input {
            width: 100%;
            padding: 6px 8px;
            font-size: 13px;
            box-sizing: border-box;
        }

        .input-group-append-btn {
            display: flex;
            gap: 4px;
            margin-top: 4px;
        }

        .apply-to-all-btn {
            padding: 4px 8px;
            font-size: 11px;
            white-space: nowrap;
        }
    </style>


    <style>
        .upload-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }



        .dropzone {
            border: 3px dashed var(--border-color);
            border-radius: 12px;
            background: white;
            padding: 60px 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            min-height: 200px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .dropzone:hover {
            border-color: var(--primary-color);
            background: var(--light-color);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .dropzone.dragover {
            border-color: var(--primary-color);
            background: #edf2f7;
            transform: scale(1.02);
        }

        .dropzone i {
            font-size: 64px;
            color: var(--primary-color);
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }

        .dropzone:hover i {
            transform: scale(1.1);
        }

        .file-item {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            animation: slideIn 0.3s ease;
            border-left: 4px solid var(--primary-color);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .file-item:hover {
            transform: translateX(5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        .file-item.from-storage {
            border-left-color: var(--success-color);
        }

        .file-item.saved-to-storage {
            border-left-color: var(--warning-color);
            animation: pulseSaved 2s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulseSaved {
            0% {
                border-left-color: var(--warning-color);
            }

            50% {
                border-left-color: var(--success-color);
            }

            100% {
                border-left-color: var(--warning-color);
            }
        }

        .file-info {
            display: flex;
            align-items: center;
            flex: 1;
            min-width: 0;
        }

        .file-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .file-icon i {
            color: white;
            font-size: 22px;
        }

        .file-details {
            flex: 1;
            min-width: 0;
        }

        .file-name {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 4px;
            word-break: break-word;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .file-source {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 500;
            padding: 2px 8px;
            background: var(--border-color);
            border-radius: 10px;
            display: inline-block;
            margin-right: 8px;
        }

        .file-size {
            font-size: 13px;
            color: var(--text-muted);
            font-weight: 500;
            display: inline;
        }

        .file-actions {
            display: flex;
            align-items: center;

            flex-shrink: 0;
        }

        .btn-save {
            background: var(--success-color);
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 500;
        }

        .btn-save:hover {
            background: #38a169;
            transform: scale(1.05);
        }

        .btn-save:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }

        .btn-remove {
            background: var(--danger-color);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 500;
            flex-shrink: 0;
        }

        .btn-remove:hover {
            background: #f56565;
            transform: scale(1.05);
        }

        .upload-btn {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
            padding: 14px 36px;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .upload-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }

        .upload-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
            box-shadow: none !important;
        }

        .btn-secondary {
            background: var(--border-color);
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-secondary:hover {
            background: #cbd5e0;
            transform: translateY(-1px);
        }

        .modal-content {
            border-radius: 16px;
            border: none;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border-radius: 16px 16px 0 0;
            padding: 20px 30px;
            border: none;
        }

        .modal-title {
            font-weight: 600;
            font-size: 1.25rem;
        }

        .modal-body {
            padding: 25px;
            max-height: 500px;
            overflow-y: auto;
        }

        .image-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .storage-image-item {
            border: 2px solid var(--border-color);
            border-radius: 12px;
            padding: 12px;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
            position: relative;
            background: white;
            overflow: hidden;
        }

        .storage-image-item:hover {
            border-color: var(--primary-color);
            background: var(--light-color);
            transform: translateY(-3px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .storage-image-item.selected {
            border-color: var(--primary-color);
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            transform: translateY(-3px);
        }

        .storage-image-preview {
            width: 100%;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 10px;
            background: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            font-size: 12px;
        }

        .storage-image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 8px;
        }

        .storage-image-name {
            font-size: 13px;
            font-weight: 500;
            color: var(--dark-color);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            padding: 0 5px;
        }

        .delete-stored-image {
            position: absolute;
            top: 8px;
            right: 8px;
            background: var(--danger-color);
            color: white;
            border: none;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            opacity: 0;
            transition: all 0.2s;
            z-index: 2;
        }

        .storage-image-item:hover .delete-stored-image {
            opacity: 1;
        }

        .delete-stored-image:hover {
            background: #f56565;
            transform: scale(1.1);
        }

        .selected-files-container {
            margin-top: 25px;
            padding: 20px;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
            border-radius: 12px;
            border: 1px solid rgba(102, 126, 234, 0.2);
        }

        .selected-files-container h6 {
            color: var(--dark-color);
            margin-bottom: 15px;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .selected-files-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }

        .selected-file-tag {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 13px;
            display: inline-flex;
            align-items: center;

            font-weight: 500;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .selected-file-tag.from-storage {
            background: linear-gradient(135deg, var(--success-color) 0%, #38a169 100%);
        }

        .selected-file-tag.saved-to-storage {
            background: linear-gradient(135deg, var(--warning-color) 0%, #dd6b20 100%);
        }

        .remove-selected {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            cursor: pointer;
            padding: 2px;
            font-size: 14px;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .remove-selected:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        .storage-info {
            position: fixed;
            bottom: 25px;
            right: 25px;
            background: white;
            padding: 12px 20px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            font-size: 14px;
            color: var(--dark-color);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 1000;
            transition: all 0.3s;
        }

        .storage-info:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.2);
        }

        .alert {
            border-radius: 10px;
            border: none;
            padding: 15px 20px;
            font-weight: 500;
        }

        .progress {
            height: 8px;
            border-radius: 4px;
            margin-top: 20px;
            overflow: hidden;
        }

        .progress-bar {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            transition: width 0.3s ease;
        }

        .no-files-message {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-muted);
        }

        .no-files-message i {
            font-size: 48px;
            margin-bottom: 15px;
            color: var(--border-color);
        }

        @media (max-width: 768px) {
            body {
                padding: 20px 0;
            }

            .upload-container {
                padding: 0 10px;
            }

            .dropzone {
                padding: 40px 15px;
            }

            .dropzone i {
                font-size: 48px;
            }

            .file-icon {
                width: 40px;
                height: 40px;
            }

            .image-grid {
                grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            }

            .storage-info {
                bottom: 15px;
                right: 15px;
                left: 15px;
                text-align: center;
                justify-content: center;
            }
        }

        .loading-spinner {
            display: inline-block;
            width: 1em;
            height: 1em;
            border: 0.2em solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            animation: spinner 0.75s linear infinite;
        }

        @keyframes spinner {
            to {
                transform: rotate(360deg);
            }
        }

        .error-message {
            background: linear-gradient(135deg, rgba(252, 129, 129, 0.1) 0%, rgba(245, 101, 101, 0.1) 100%);
            border: 1px solid var(--danger-color);
            color: #c53030;
            padding: 10px 15px;
            border-radius: 8px;
            margin-top: 10px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .source-badge {
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 600;
            margin-left: 6px;
        }

        .source-local {
            background: var(--primary-color);
            color: white;
        }

        .source-storage {
            background: var(--success-color);
            color: white;
        }

        .saved-badge {
            background: var(--warning-color);
            color: white;
        }

        .auto-save-toggle {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
            padding: 10px 15px;
            background: rgba(102, 126, 234, 0.05);
            border-radius: 10px;
            border: 1px solid rgba(102, 126, 234, 0.1);
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.toggle-slider {
            background-color: var(--success-color);
        }

        input:checked+.toggle-slider:before {
            transform: translateX(26px);
        }
    </style>



    <div class="app-content-area">
        <div class="app-content-wrap">
            <div class="container-fluid">

                <!-- Page Header -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="page-title-box d-flex-between flex-wrap gap-15">
                            <h1 class="page-title fs-18 lh-1">Add Product</h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb breadcrumb-example1 mb-0">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Add Product</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <div class="row">
                    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data"
                        class="validate_form" id="productForm">
                        @csrf

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
                                            <div class="col-md-6 mb-15">
                                                <label for="filterParent" class="form-label fw-semibold mb-2">
                                                    <i class="ri-folder-line me-1"></i> Category
                                                </label>
                                                <select id="filterParent" class="form-select select2" name="category_id">
                                                    <option value="">All Categories</option>

                                                    @php
                                                        $grouped = $categories->groupBy('parent_name');
                                                    @endphp

                                                    @foreach ($grouped as $parent => $items)
                                                        <optgroup label="{{ $parent ?? 'Main Categories' }}">
                                                            @foreach ($items as $cat)
                                                                <option value="{{ $cat->id }}">
                                                                    {{ $cat->name }}
                                                                </option>
                                                            @endforeach
                                                        </optgroup>
                                                    @endforeach
                                                </select>

                                            </div>

                                            <!-- SUB CATEGORY -->
                                            <div class="col-md-6 mb-15">
                                                <label class="form-label mt-3">Status <span
                                                        class="text-danger">*</span></label>
                                                <select name="status" class="form-control" required>
                                                    <option value="1">Published (Visible)</option>
                                                    <option value="0">Draft (Hidden)</option>
                                                </select>
                                            </div>

                                            <!-- PRODUCT NAME -->
                                            <div class="col-md-12 mb-15">
                                                <label class="form-label">Product Name <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="product_name" class="form-control"
                                                    placeholder="e.g. 18K Gold Ring for Women – Floral Design"
                                                    id="product_name" required>
                                                <small class="text-muted">
                                                    Clear, searchable title including material & type
                                                </small>
                                            </div>

                                            <!-- SLUG -->
                                            <div class="col-md-6 mb-15">
                                                <label class="form-label">Slug</label>
                                                <input type="text" name="slug" class="form-control"
                                                    placeholder="auto-generated-if-empty" id="slug">
                                                <small class="text-muted">
                                                    SEO-friendly URL (lowercase, hyphens only)
                                                </small>
                                            </div>

                                            <!-- SKU -->
                                            <div class="col-md-6 mb-15">
                                                <label class="form-label">SKU <span class="text-danger">*</span></label>
                                                <input type="text" name="sku" class="form-control"
                                                    placeholder="e.g. GR-WOM-001" required id="parent_product_sku">
                                                <small class="text-muted">Must be unique across all products</small>
                                            </div>

                                            <!-- MAIN IMAGE -->
                                            <div class="col-md-6 mb-15">
                                                <label class="form-label">Main Image <span
                                                        class="text-danger">*</span></label>
                                                <input type="file" name="main_image" class="form-control"
                                                    accept="image/*" required>
                                                <small class="text-muted">
                                                    Recommended: 1200×1200px • JPG/PNG/WebP • Max 5MB
                                                </small>
                                            </div>

                                            <!-- SECONDARY IMAGE -->
                                            <div class="col-md-6 mb-15">
                                                <label class="form-label">Secondary Image <span
                                                        class="text-danger">*</span></label>
                                                <input type="file" name="secondary_image" class="form-control"
                                                    accept="image/*" required>
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
                                                        min="0" max="9999999.99" step="0.01" value="0">
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
                                                        value="0">
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
                                                        value="0">
                                                </div>
                                            </div>



                                            <!-- TAX PERCENTAGE -->
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Tax Percentage</label>
                                                <div class="input-group">
                                                    <input type="number" name="tax_percentage" class="form-control"
                                                        placeholder="0.00" min="0" max="100" step="0.01"
                                                        value="0">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                                <small class="text-muted">TAX percentage (e.g., 18.00)</small>
                                            </div>

                                            <!-- SHORT DESCRIPTION -->
                                            <div class="col-md-12 mb-15">
                                                <label class="form-label">Short Description <span
                                                        class="text-danger">*</span></label>
                                                <div id="short_editor" style="height:260px;"></div>
                                                <input type="hidden" id="short_description" name="short_description">
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
                                                <div id="editor" style="height:260px;"></div>
                                                <input type="hidden" id="description" name="description">
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ================= RIGHT COLUMN : PUBLISH & SEO ================= -->
                            <div class="col-xxl-4 col-xl-4">



                                <div class="card">
                                    <div class="card-body md-5 mt-2">
                                        <h2 class="text-center mb-4">
                                            <i class="fas fa-cloud-upload-alt text-primary me-2"></i>
                                            Product Images
                                        </h2>


                                        <div class="dropzone" id="dropzone">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                            <h4>Drag & Drop Files Here</h4>
                                            <p class="text-muted mb-0">or click to browse</p>
                                            <p class="text-muted small mt-2">Supports images JPG,PNG,SVG,
                                                and more</p>
                                            <input type="file" id="fileInput" multiple hidden>
                                        </div>



                                        <div id="fileList" class="mt-4"></div>

                                        <div id="noFilesMessage" class="no-files-message" style="display: none;">
                                            <i class="fas fa-folder-open"></i>
                                            <h5>No files selected</h5>
                                            <p class="text-muted">Drag and drop files or click the area above
                                                to select files</p>
                                        </div>

                                        <div class="text-center mt-4" id="uploadSection" style="display: none;">
                                            <div class="progress" style="display: none;" id="uploadProgress">
                                                <div class="progress-bar" id="progressBar" style="width: 0%">
                                                </div>
                                            </div>
                                        </div>

                                        <div id="successMessage" class="alert alert-success mt-4" style="display: none;">
                                            <i class="fas fa-check-circle me-2"></i>
                                            <span id="successText"></span>
                                        </div>

                                        <div id="errorMessage" class="error-message mt-4" style="display: none;">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <span id="errorText"></span>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="storageModal" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">
                                                        <i class="fas fa-images me-2"></i>Select Images from Storage
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="mb-3">Select images from your local storage to attach
                                                        to upload:</p>
                                                    <div id="storageImagesGrid" class="image-grid"></div>
                                                    <div id="noStorageMessage" class="text-center py-5"
                                                        style="display: none;">
                                                        <i class="fas fa-images fa-3x mb-3 text-muted"></i>
                                                        <h5 class="text-muted">No images in storage</h5>
                                                        <p class="text-muted mb-0">Save some images first using "Save
                                                            to Storage" button</p>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Cancel</button>
                                                    <button type="button" class="btn btn-primary" id="attachStorageBtn">
                                                        <i class="fas fa-paperclip me-2"></i>Attach Selected
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                </div>



                                <!-- PUBLISH -->
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h4>Publish</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">

                                            <div class="col-md-6 mb-15">
                                                <label class="form-label mt-3">Personalize Product? <span
                                                        class="text-danger">*</span></label>
                                                <select name="personalize" class="form-control" required>
                                                    <option value="1">YES (Visible)</option>
                                                    <option value="0" selected>NO </option>
                                                </select>
                                            </div>


                                            <div class="col-md-6 mb-15">

                                                <label class="form-label mt-3">Marketing Label</label>
                                                <select name="marketing_label" class="form-control">
                                                    <option value="">None</option>
                                                    <option value="new">New Arrival</option>
                                                    <option value="trending">Trending</option>
                                                    <option value="hot">Hot Deal</option>
                                                    <option value="sale">On Sale</option>
                                                    <option value="limited">Limited Edition</option>
                                                    <option value="exclusive">Exclusive</option>
                                                    <option value="popular">Popular</option>
                                                    <option value="top_rated">Top Rated</option>
                                                </select>
                                            </div>

                                            <div class="col-md-6 mb-15">
                                                <label class="form-label mt-3">Is Featured</label>
                                                <select name="is_featured" class="form-control">
                                                    <option value="1">Yes (Homepage)</option>
                                                    <option value="0" selected>No</option>
                                                </select>
                                            </div>
                                        </div>



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
                                            placeholder="e.g. Buy 18K Gold Ring for Women Online">
                                        <small class="text-muted">Max 60 characters</small></br>

                                        <label class="form-label mt-3">SEO Image</label>
                                        <input type="file" name="seo_image" class="form-control" accept="image/*">
                                        <small class="text-muted">Recommended: 1200×630px (Social sharing)</small> </br>

                                        <label class="form-label mt-3">Meta Description</label>
                                        <textarea name="seo_description" class="form-control" rows="3"
                                            placeholder="Short SEO description (max 160 characters)"></textarea>

                                        <label class="form-label mt-3">Meta Keywords</label>
                                        <select name="seo_keywords[]" class="form-control select2-tags" multiple
                                            data-placeholder="Type keyword & press Enter">
                                        </select>
                                        <small class="text-muted">Press Enter or comma after each keyword</small>
                                    </div>
                                </div>
                            </div>

                            <!-- ================= VARIANT OPTIONS ================= -->
                            <div class="col-xxl-12 col-xl-12 mt-3">
                                <div class="card shadow-sm border-0 h-100">
                                    <div class="card-header border-bottom py-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h5 class="mb-0 fw-semibold">Variant Options</h5>
                                                <div class="alert alert-info">
                                                    Variants are generated automatically after you add a variant option and
                                                    select at least one value
                                                    (e.g., Color → Red, Green). Once created, please enter SKU, pricing,
                                                    stock, and upload images for each variant.
                                                </div>

                                            </div>
                                            <button type="button" class="btn btn-primary add-variant-btn">
                                                <i class="bi bi-plus-circle me-1"></i>Add Variant Option
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="variant-options-container">
                                            <div class="variant-options"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ================= VARIANT COMBINATIONS ================= -->
                            <div class="col-xxl-12 col-xl-12 d-none" id="varinat_box">
                                <div class="card mt-4">
                                    <div class="card-header">
                                        <h5 class="mb-1">Variant Combinations</h5>
                                    </div>

                                    <div class="card-body">

                                        <div class="table-responsive">
                                            <table class="table table-bordered align-middle">
                                                <thead>
                                                    <tr>
                                                        <th class="th-filter">
                                                            <span>Variant</span>
                                                        </th>

                                                        <!-- SKU -->
                                                        <th class="th-filter">
                                                            <span>SKU *</span>
                                                            <div style="display: flex; align-items: center;">
                                                                <input type="text" name="sku"
                                                                    id="main_product_sku"
                                                                    class="form-control form-control-sm parent-input"
                                                                    data-field="sku" placeholder="SKU" autocomplete="off"
                                                                    maxlength="32" required style="flex: 1;">
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
                                                                <input type="number" name="weight"
                                                                    id="main_product_weight"
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
                                                                <input type="number" name="cost_price"
                                                                    id="main_cost_price"
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
                                                                <input type="number" name="mrp_price"
                                                                    id="main_mrp_price"
                                                                    class="form-control form-control-sm parent-input"
                                                                    data-field="mrp_price" placeholder="MRP Price"
                                                                    min="1" max="1000000" step="0.01"
                                                                    inputmode="decimal" required style="flex: 1;">
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
                                                                <input type="number" name="selling_price"
                                                                    id="main_selling_price"
                                                                    class="form-control form-control-sm parent-input"
                                                                    data-field="selling_price" placeholder="Selling Price"
                                                                    min="0" max="1000000" step="0.01"
                                                                    inputmode="decimal" required style="flex: 1;">
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
                                                                <input type="number" name="quantity"
                                                                    class="form-control form-control-sm parent-input"
                                                                    data-field="quantity" placeholder="Qty"
                                                                    min="0" max="99999" step="1"
                                                                    inputmode="numeric" required style="flex: 1;">
                                                                <button type="button"
                                                                    class="btn btn-outline-secondary btn-sm apply-to-all-btn"
                                                                    data-field="quantity" title="Apply to All">
                                                                    ⚡
                                                                </button>
                                                            </div>
                                                        </th>

                                                        <!-- Images -->
                                                        <th class="th-filter">
                                                            <span>Images</span>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody class="variant-combinations">
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
                                            <i class="bi bi-check-circle me-2"></i>Save Product & Variants
                                        </button>
                                        <a href="{{ route('products.index') }}"
                                            class="btn btn-outline-secondary">Cancel</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </form>

                </div>

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

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('assets/admin/js/plugins/select2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script src="{{ asset('assets/admin/js/plugins/jquery-confirm.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>


    <script src="{{ asset('assets/admin/js/file_uploader.js') }}"></script>


    <script>
        $(document).ready(function() {
            let variantOptionCount = 0;
            let attributeValuesCache = {};
            let quill, short_quill;

            /* =====================
                VALIDATION HELPER
            ===================== */
            function validatePriceLogic($row) {
                let costPrice = parseFloat($row.find('input[name*="[cost_price]"]').val()) || 0;
                let mrpPrice = parseFloat($row.find('input[name*="[price]"]').val()) || 0;
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
                    console.warn('Warning: Selling Price is less than Cost Price - potential loss!');
                }

                return {
                    isValid,
                    errorMsg
                };
            }

            /* =====================
                AUTO-APPLY ON INPUT (Realtime) - CORRECTED
            ===================== */
            $(document).on('input', '.parent-input', function(e) {
                let $input = $(this);
                let field = $input.data('field');
                let val = $input.val();

                if (!field) return;

                // Format validation for each field
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

                // Apply to all variants based on field type
                if (field === 'sku') {
                    applySKUToAllVariants(val);
                } else {
                    applyToAllVariants(field, val, false);
                }
            });

            /* =====================
                APPLY SKU TO ALL VARIANTS FUNCTION
            ===================== */
            function applySKUToAllVariants(parentSKU) {
                if (!parentSKU) return;

                $('.variant-combinations tr').each(function() {
                    let $row = $(this);
                    let combination = $row.data('combination') ||
                        $row.find('input[name$="[name]"]').val() ||
                        $row.find('.variant-name').text() || '';

                    if (!combination) return;

                    // Clean combination for SKU
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

            /* =====================
                GENERATE SKU BUTTON CLICK - CORRECTED
            ===================== */
            $(document).on('click', '.generate-sku-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();

                let $button = $(this);
                let $input = $button.closest('.th-filter').find('.parent-input');
                let field = $input.data('field');

                if (field === 'sku') {
                    // For SKU field, generate from parent product SKU
                    let parentProductSku = $('#parent_product_sku').val()?.trim();
                    if (!parentProductSku) {
                        alert('Please enter Parent Product SKU first!');
                        $('#parent_product_sku').focus();
                        return;
                    }

                    // Set the main SKU field value
                    $('#main_product_sku').val(parentProductSku);

                    // Trigger the input event to auto-apply to variants
                    $('#main_product_sku').trigger('input');

                    // Show success message
                    let variantCount = $('.variant-combinations tr').length;
                    if (variantCount > 0) {
                        alert(`SKU generated and applied to ${variantCount} variants!`);
                    }
                }
            });

            /* =====================
                APPLY TO ALL BUTTON CLICK - CORRECTED
            ===================== */
            $(document).on('click', '.apply-to-all-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();

                let $button = $(this);
                let field = $button.data('field');

                // Find the parent input in the same th-filter div
                let $input = $button.closest('.th-filter').find('.parent-input[data-field="' + field +
                    '"]');

                if ($input.length === 0) {
                    // Alternative: look for any parent-input with the data-field
                    $input = $button.closest('.th-filter').find('.parent-input').filter(function() {
                        return $(this).data('field') === field;
                    });
                }

                if ($input.length === 0) {
                    console.error('Input field not found for field:', field);
                    return;
                }

                let val = $input.val();

                if (field === 'sku') {
                    if (!val) {
                        alert('Please enter a SKU value first!');
                        $input.focus();
                        return;
                    }
                    applySKUToAllVariants(val);
                    alert('SKU applied to all variants!');
                } else {
                    if (!val && val !== '0') {
                        if (field === 'cost_price') {
                            val = $('input[name="parent_cost_price"]').val();
                        } else if (field === 'parent_selling_price') {
                            val = $('input[name="parent_selling_price"]').val();
                        } else if (field === 'mrp_price') {
                            val = $('input[name="parent_mrp_price"]').val();
                        } else {
                            $input.focus();
                            return false;
                        }
                    }
                    applyToAllVariants(field, val, true);
                }
            });

            /* =====================
                APPLY TO ALL VARIANTS FUNCTION (Non-SKU fields)
            ===================== */
            function applyToAllVariants(field, value, showAlert = false) {
                let appliedCount = 0;
                let errorCount = 0;

                $('.variant-combinations tr').each(function() {
                    let $row = $(this);
                    let $childInput = $row.find('input[name*="[' + field + ']"]');

                    if ($childInput.length) {
                        $childInput.val(value);

                        if (['cost_price', 'mrp_price', 'selling_price'].includes(field)) {
                            let validation = validatePriceLogic($row);
                            if (!validation.isValid) {
                                $childInput.addClass('is-invalid');
                                errorCount++;
                            } else {
                                $childInput.removeClass('is-invalid');
                                appliedCount++;
                            }
                        } else {
                            appliedCount++;
                        }
                    }
                });

                if (errorCount > 0) {
                    alert(
                        `Applied to ${appliedCount} variants, but ${errorCount} have validation errors. Please check highlighted fields.`
                    );
                } else if (showAlert && appliedCount > 0) {
                    alert(`${field.replace('_', ' ')} applied to all ${appliedCount} variants successfully!`);
                }
            }

            /* =====================
                VARIANT PRICE INPUT VALIDATION
            ===================== */
            $(document).on('input blur',
                '.variant-combinations input[name*="[cost_price]"], .variant-combinations input[name*="[price]"], .variant-combinations input[name*="[selling_price]"]',
                function() {
                    let $row = $(this).closest('tr');
                    let validation = validatePriceLogic($row);

                    if (!validation.isValid) {
                        $(this).addClass('is-invalid');
                        alert(validation.errorMsg);
                    } else {
                        $row.find(
                            'input[name*="[cost_price]"], input[name*="[price]"], input[name*="[selling_price]"]'
                        ).removeClass('is-invalid');
                    }
                });

            /* =====================
                FORM SUBMISSION VALIDATION
            ===================== */
            $('#productForm').on('submit', function(e) {
                let hasErrors = false;
                let errorMessages = [];

                $('.variant-combinations tr').each(function(index) {
                    let $row = $(this);
                    let variantName = $row.find('.variant-name').text();
                    let validation = validatePriceLogic($row);

                    if (!validation.isValid) {
                        hasErrors = true;
                        errorMessages.push(`Variant "${variantName}": ${validation.errorMsg}`);
                        $row.find(
                            'input[name*="[cost_price]"], input[name*="[price]"], input[name*="[selling_price]"]'
                        ).addClass('is-invalid');
                    }
                });

                if (hasErrors) {
                    e.preventDefault();

                    let errorList = errorMessages.map(msg => `• ${msg}`).join('\n');
                    alert('Please fix the following price validation errors:\n\n' + errorList);

                    $('html, body').animate({
                        scrollTop: $('.is-invalid:first').offset().top - 100
                    }, 500);

                    return false;
                }
            });

            /* =====================
                SELECT2 INITIALIZATION
            ===================== */
            function initSelect2(context = document) {
                $(context).find('.select2').each(function() {
                    if ($(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2('destroy');
                    }

                    $(this).select2({
                        width: '100%',
                        placeholder: $(this).data('placeholder') || 'Select option',
                        allowClear: true
                    });
                });

                $(context).find('.select2-tags').each(function() {
                    if ($(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2('destroy');
                    }

                    $(this).select2({
                        tags: true,
                        tokenSeparators: [','],
                        width: '100%',
                        placeholder: 'Type & press Enter'
                    });
                });
            }

            initSelect2();

            /* =====================
                QUILL EDITOR INITIALIZATION
            ===================== */
            const toolbarOptions = [
                [{
                    'font': []
                }, {
                    'size': ['small', false, 'large', 'huge']
                }],
                [{
                    'header': [1, 2, 3, 4, 5, 6, false]
                }],
                ['bold', 'italic', 'underline', 'strike'],
                [{
                    'color': []
                }, {
                    'background': []
                }],
                [{
                    'script': 'sub'
                }, {
                    'script': 'super'
                }],
                [{
                    'list': 'ordered'
                }, {
                    'list': 'bullet'
                }],
                [{
                    'indent': '-1'
                }, {
                    'indent': '+1'
                }],
                [{
                    'align': []
                }],
                ['blockquote', 'code-block'],
                ['link', 'image', 'video'],
                ['clean']
            ];

            if ($('#editor').length) {
                quill = new Quill('#editor', {
                    theme: 'snow',
                    modules: {
                        toolbar: toolbarOptions
                    }
                });

                quill.on('text-change', function() {
                    $('#description').val(quill.root.innerHTML);
                });
            }

            if ($('#short_editor').length) {
                short_quill = new Quill('#short_editor', {
                    theme: 'snow',
                    modules: {
                        toolbar: [
                            ['bold', 'italic', 'underline'],
                            [{
                                'list': 'ordered'
                            }, {
                                'list': 'bullet'
                            }],
                            ['link', 'clean']
                        ]
                    }
                });

                short_quill.on('text-change', function() {
                    $('#short_description').val(short_quill.root.innerHTML);
                });
            }

            /* =====================
                AUTO SLUG GENERATION
            ===================== */
            $('#product_name').on('keyup change', function() {
                let slug = $(this).val()
                    .toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .trim();
                $('#slug').val(slug);
            });

            /* =====================
                CATEGORY → SUBCATEGORY
            ===================== */
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

                let url = `/admin/subcategories/ajax/${categoryId}`;

                $.get(url, function(data) {
                    $sub.html('<option value="">Select Sub Category</option>');
                    $.each(data, function(_, item) {
                        $sub.append(`<option value="${item.id}">${item.name}</option>`);
                    });
                    $sub.prop('disabled', false);
                    $sub.trigger('change');
                }).fail(function() {
                    $sub.html('<option value="">Error loading data</option>');
                    $sub.prop('disabled', false);
                });
            });

            /* =====================
                DYNAMIC ATTRIBUTE VALUES LOADING
            ===================== */
            $(document).on('change', '.option-name', function() {
                let attributeId = $(this).val();
                let $valueSelect = $(this).closest('.variant-option').find('.option-values');
                let $valueSelectContainer = $valueSelect.parent();

                $valueSelect.empty();

                if (!attributeId) {
                    generateCombinations();
                    return;
                }

                if (attributeValuesCache[attributeId]) {
                    populateValues($valueSelect, attributeValuesCache[attributeId]);
                    return;
                }

                $valueSelectContainer.addClass('loading');
                $valueSelect.prop('disabled', true);

                let url = "{{ route('attributes.ajax', ':id') }}".replace(':id', attributeId);

                $.get(url, function(values) {
                    attributeValuesCache[attributeId] = values;
                    populateValues($valueSelect, values);
                }).fail(function() {
                    alert('Error loading attribute values');
                }).always(function() {
                    $valueSelectContainer.removeClass('loading');
                    $valueSelect.prop('disabled', false);
                });
            });

            function populateValues($select, values) {
                $select.empty();

                if (Array.isArray(values)) {
                    values.forEach(v => {
                        $select.append(`<option value="${v.id}">${v.value}</option>`);
                    });
                } else if (values.response && Array.isArray(values.response)) {
                    values.response.forEach(v => {
                        $select.append(`<option value="${v.id}">${v.value}</option>`);
                    });
                }

                if ($select.hasClass('select2-hidden-accessible')) {
                    $select.select2('destroy');
                }

                $select.select2({
                    width: '100%',
                    placeholder: 'Select values',
                    closeOnSelect: false
                });

                generateCombinations();
            }

            /* =====================
                ADD VARIANT OPTION
            ===================== */
            $('.add-variant-btn').on('click', function() {
                let $template = $('.variant-option-template .variant-option').clone();
                let optionId = 'option-' + variantOptionCount++;

                $template.attr('data-option-id', optionId);
                $('.variant-options').append($template);

                $template.find('.option-name').select2({
                    width: '100%',
                    placeholder: 'Select attribute'
                });

                $template.find('.option-values').select2({
                    width: '100%',
                    placeholder: 'Select values',
                    closeOnSelect: false
                });

                $template.find('.option-name').on('change', generateCombinations);
                $template.find('.option-values').on('change', generateCombinations);

                $template.find('.remove-option').on('click', function() {
                    $template.remove();
                    generateCombinations();
                });
            });

            /* =====================
                GENERATE VARIANT COMBINATIONS
            ===================== */
            function generateCombinations() {
                let options = [];
                let optionData = [];
                let hasValidOptions = false;

                $('.variant-options .variant-option').each(function() {
                    let $option = $(this);
                    let attributeId = $option.find('.option-name').val();
                    let attributeName = $option.find('.option-name option:selected').text();
                    let selectedValues = $option.find('.option-values').val();
                    let values = [];

                    if (selectedValues && selectedValues.length > 0) {
                        hasValidOptions = true;
                        $option.find('.option-values option:selected').each(function() {
                            values.push({
                                id: $(this).val(),
                                text: $(this).text().trim(),
                                value: $(this).text().trim()
                            });
                        });
                        options.push(values.map(v => v.text));
                        optionData.push({
                            attribute_id: attributeId,
                            attribute_name: attributeName,
                            values: values
                        });
                    }
                });

                let $comboBody = $('.variant-combinations');
                $comboBody.empty();

                $('input[name="variant_options"]').remove();

                if (!hasValidOptions || options.length === 0) {
                    $('#varinat_box').addClass('d-none');
                    return;
                }

                $('#productForm').append(
                    `<input type="hidden" name="variant_options" id="variant_options_input" value='${JSON.stringify(optionData)}'>`
                );

                let combinations = cartesian(options);

                combinations.forEach((combo, index) => {
                    let variantName = combo.join(' / ');
                    let variantId = combo.join('-').toLowerCase().replace(/[^a-z0-9]/g, '-');

                    $comboBody.append(`
                <tr data-variant-id="${variantId}" data-combination="${variantName}">
                    <td>
                        <strong class="variant-name">${variantName}</strong>
                        <input type="hidden" name="variants[${index}][name]" value="${variantName}">
                    </td>
                    <td>
                        <input type="text" 
                            class="form-control form-control-sm" 
                            name="variants[${index}][sku]" 
                            placeholder="SKU-${index + 1}"
                            required>
                    </td>
                    <td>
                        <input type="number" 
                            step="1" 
                            min="0"
                            class="form-control form-control-sm" 
                            name="variants[${index}][weight]" 
                            placeholder="0"
                            value="0">
                    </td>
                    <td>
                        <input type="number" 
                            step="0.01" 
                            min="0"
                            class="form-control form-control-sm" 
                            name="variants[${index}][cost_price]" 
                            placeholder="0">
                    </td>
                    <td>
                        <input type="number" 
                            step="0.01" 
                            min="0.01"
                            class="form-control form-control-sm variant-price" 
                            name="variants[${index}][mrp_price]" 
                            placeholder="0"
                            required>
                    </td>
                    <td>
                        <input type="number" 
                            step="0.01" 
                            min="0.01"
                            class="form-control form-control-sm variant-selling-price" 
                            name="variants[${index}][selling_price]" 
                            placeholder="0"
                            required>
                    </td>
                    <td>
                        <input type="number" 
                            min="0"
                            class="form-control form-control-sm" 
                            name="variants[${index}][quantity]" 
                            placeholder="0"
                            required>
                    </td>
                    <td>
                        <div class="variant-image-section">
                            <button class="btn btn-sm btn-info mb-2 load-variant-images" 
                                    type="button"
                                    data-variant-index="${index}"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#storageModal">
                                <i class="fas fa-folder-open me-1"></i>Load Images
                            </button>
                            <div class="variant-images-preview mt-2" id="variant-images-${index}">
                                <!-- Images will be displayed here -->
                            </div>
                            <input type="hidden" 
                                name="variants[${index}][images]" 
                                id="variant-images-input-${index}"
                                value="">
                        </div>
                    </td>
                </tr>
            `);
                });

                $('#varinat_box').removeClass('d-none');

                applyExistingParentValues();
            }

            /* =====================
                APPLY EXISTING PARENT VALUES TO NEW VARIANTS
            ===================== */
            function applyExistingParentValues() {
                $('.th-filter').each(function() {
                    let $filter = $(this);
                    let $input = $filter.find('.parent-input');
                    if ($input.length) {
                        let field = $input.data('field');
                        let val = $input.val();

                        if (val) {
                            if (field === 'sku') {
                                applySKUToAllVariants(val);
                            } else {
                                applyToAllVariants(field, val, false);
                            }
                        }
                    }
                });
            }

            /* =====================
                CARTESIAN PRODUCT HELPER
            ===================== */
            function cartesian(arrays) {
                return arrays.reduce((acc, curr) => {
                    const result = [];
                    acc.forEach(a => {
                        curr.forEach(b => {
                            result.push([...a, b]);
                        });
                    });
                    return result;
                }, [
                    []
                ]).filter(arr => arr.length > 0);
            }

            /* =====================
                VARIANT IMAGE MODAL HANDLING
            ===================== */
            $(document).on('click', '.load-variant-images', function(e) {
                e.preventDefault();
                const variantIndex = $(this).data('variant-index');
                $('#storageModal').data('target-variant-index', variantIndex);
            });

            $('#storageModal').on('hidden.bs.modal', function() {
                $(this).removeData('target-variant-index');
            });
        });
    </script>

    <?php
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\File;
    use Illuminate\Support\Facades\Log;
    
    $tempImages = DB::table('temp_images')->where('user_id', Auth::id())->get();
    if ($tempImages->isNotEmpty()) {
        foreach ($tempImages as $tempImage) {
            if (empty($tempImage->image_url)) {
                continue;
            }
            $relativePath = parse_url($tempImage->image_url, PHP_URL_PATH);
            $fullPath = public_path($relativePath);
            if (File::exists($fullPath)) {
                File::delete($fullPath);
            }
        }
    }
    DB::table('temp_images')->where('user_id', Auth::id())->delete();
    ?>
@endpush
