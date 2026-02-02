@extends('layouts.admin')

@section('content')
    <div class="app-content-area">
        <div class="app-content-wrap">
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

                                    <li class="breadcrumb-item">
                                        <a href="{{ route('products.index') }}">{{ $resource }}</a>
                                    </li>


                                    <li class="breadcrumb-item active" aria-current="page"> Product Create</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- Main Content Row -->
                <div class="row">
                    <!-- Product Images Section -->
                    <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-12 mb-4">
                        <div class="card h-100">
                            <div class="mb-3 text-center">
                                <span class="badge bg-primary mb-2">Main Image</span>
                                <div>{!! image_show($product->main_image, 400, 600) !!}</div>
                            </div>

                            <div class="mb-3 text-center">
                                <span class="badge bg-secondary mb-2">Secondary Image</span>
                                <div>{!! image_show($product->secondary_image, 400, 600) !!}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Product Details Section -->
                    <div class="col-xxl-8 col-xl-8 col-lg-8 col-md-12 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-5">
                                    <h2 class="product-title">{{ $product->name ?? '-' }}</h2>
                                    <a class="badge bg-pink radius-4" href="javascript:void(0);">
                                        {{ $product->label ?? '' }}
                                    </a>
                                </div>

                                <div class="product-meta d-flex flex-wrap gap-2 mb-3">
                                    <p class="brand d-flex align-items-center gap-2 text-black fw-6 mb-0">
                                        <span class="text-body-secondary">Brand:</span>
                                        <a href="javascript:void(0);">GR Silver International</a>
                                    </p>
                                    <p class="sku-number d-flex align-items-center gap-2 text-black fw-6 mb-0">
                                        <span class="text-body-secondary">SKU:</span> {{ $product->sku ?? '-' }}
                                    </p>
                                </div>

                                <div class="product-rating text-warning mb-3">
                                    <i class="ri-star-fill"></i>
                                    <i class="ri-star-fill"></i>
                                    <i class="ri-star-fill"></i>
                                    <i class="ri-star-fill"></i>
                                    <i class="ri-star-half-fill"></i>
                                    <span class="text-black fw-5 ms-2">4.9</span>
                                    <span class="text-body-secondary ms-2">(33K Customer Reviews)</span>
                                </div>

                                <!-- Product Specifications Table -->
                                <div class="product-specifications mb-4">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover align-middle w-100 mb-0">
                                            <tbody>
                                                <tr>
                                                    <th width="30%">Product Name</th>
                                                    <td>{{ $product->name ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Category</th>
                                                    <td>
                                                        {{ $product->category->name ?? '-' }}
                                                        <span class="text-muted">/</span>
                                                        {{ $product->subcategory->name ?? '-' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Slug</th>
                                                    <td class="text-muted">{{ $product->slug ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>SKU</th>
                                                    <td><span class="badge bg-secondary">{{ $product->sku ?? '-' }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Label</th>
                                                    <td>{{ $product->label ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Featured</th>
                                                    <td>
                                                        @if ($product->is_featured)
                                                            <span class="badge bg-success">Yes</span>
                                                        @else
                                                            <span class="badge bg-danger">No</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Status</th>
                                                    <td>
                                                        @if ($product->status)
                                                            <span class="badge bg-success">Active</span>
                                                        @else
                                                            <span class="badge bg-secondary">Inactive</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>SEO Title</th>
                                                    <td>{{ $product->seo_title ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>SEO Image</th>
                                                    <td>
                                                        @if ($product->seo_image)
                                                            <img src="{{ asset($product->seo_image) }}"
                                                                class="img-thumbnail" style="max-width:120px;">
                                                        @else
                                                            <span class="text-muted">No Image</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>SEO Description</th>
                                                    <td class="text-muted">{{ $product->seo_description ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>SEO Keywords</th>
                                                    <td>
                                                        @if ($product->seo_keywords)
                                                            @foreach (explode(',', $product->seo_keywords) as $keyword)
                                                                <span
                                                                    class="badge bg-light text-dark border me-1">{{ trim($keyword) }}</span>
                                                            @endforeach
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Created At</th>
                                                    <td>{{ optional($product->created_at)->format('d M Y, h:i A') }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Updated At</th>
                                                    <td>{{ optional($product->updated_at)->format('d M Y, h:i A') }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>


                                <div class="accordion" id="accordionExample">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingOne">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapseOne" aria-expanded="true"
                                                aria-controls="collapseOne">
                                                Short Description
                                            </button>
                                        </h2>
                                        <div id="collapseOne" class="accordion-collapse collapse show"
                                            aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                {!! $product->short_description ?? '-' !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingTwo">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapseTwo"
                                                aria-expanded="false" aria-controls="collapseTwo">
                                                Long Description
                                            </button>
                                        </h2>
                                        <div id="collapseTwo" class="accordion-collapse collapse"
                                            aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                            <div class="accordion-body"> {!! $product->description ?? '-' !!}
                                            </div>
                                        </div>
                                    </div>

                                </div>



                                <!-- Descriptions -->

                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabs Section -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="bd-product-information">
                                <div class="tab-style-six">
                                    <ul class="nav nav-pills mobile-nav text-nowrap" id="pills-tab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="pills-information-tab"
                                                data-bs-toggle="pill" data-bs-target="#pills-information" type="button"
                                                role="tab" aria-controls="pills-information" aria-selected="true">
                                                Product Variants
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="pills-review-tab" data-bs-toggle="pill"
                                                data-bs-target="#pills-review" type="button" role="tab"
                                                aria-controls="pills-review" aria-selected="false">
                                                Reviews
                                            </button>
                                        </li>
                                    </ul>

                                    <div class="tab-content" id="pills-tabContent">
                                        <!-- Product Variants Tab -->
                                        <div class="tab-pane fade show active" id="pills-information" role="tabpanel">
                                            <div class="table-responsive mt-4">
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
                                                            <th> Status </th>
                                                            <th> Images </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($product->variants as $variant)
                                                            <tr>
                                                                <td>{{ $loop->iteration }}</td>
                                                                <td>{{ $variant->variant_name }}</td>
                                                                <td>{{ $variant->sku }}</td>
                                                                <td>{{ number_format($variant->price, 2) }}</td>
                                                                <td>{{ number_format($variant->selling_price, 2) }}</td>
                                                                <td>{{ $variant->stock_quantity }}</td>
                                                                <td>
                                                                    <span
                                                                        class="badge {{ $variant->stock_status === 'in_stock' ? 'bg-success' : 'bg-danger' }}">
                                                                        {{ ucfirst(str_replace('_', ' ', $variant->stock_status)) }}
                                                                    </span>
                                                                </td>

                                                                <td> {!! status_dropdown($variant->status, [
                                                                    'id' => $variant->id,
                                                                    'url' => route('products.variant.status', encrypt($variant->id)),
                                                                    'method' => 'PUT',
                                                                ]) !!}</td>


                                                                <td>
                                                                    @if ($variant->images->count())
                                                                        <div class="d-flex flex-wrap gap-2">
                                                                            @foreach ($variant->images as $imageData)
                                                                                {!! image_show_with_delete(
                                                                                    $imageData->image_url,
                                                                                    50,
                                                                                    50,
                                                                                    route('products.image.delete', encrypt($imageData->id)),
                                                                                ) !!}
                                                                            @endforeach
                                                                        </div>
                                                                    @else
                                                                        <span class="text-muted">No images</span>
                                                                    @endif
                                                                </td>

                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="9" class="text-center text-muted">
                                                                    No variants found for this product.
                                                                </td>
                                                            </tr>
                                                        @endforelse

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <!-- Reviews Tab -->
                                        <div class="tab-pane fade" id="pills-review" role="tabpanel">
                                            <div class="product-review-rating-wrapper mb-4">
                                                <div class="product-rating-box d-flex align-items-center gap-3 mb-4">
                                                    <div class="product-rating-box-number fs-2 fw-bold text-primary">4.8
                                                    </div>
                                                    <div class="product-rating-box-icon d-flex">
                                                        <i class="ri-star-fill text-warning fs-5"></i>
                                                        <i class="ri-star-fill text-warning fs-5"></i>
                                                        <i class="ri-star-fill text-warning fs-5"></i>
                                                        <i class="ri-star-fill text-warning fs-5"></i>
                                                        <i class="ri-star-fill text-warning fs-5"></i>
                                                    </div>
                                                    <span class="product-rating-box-title">(150 Reviews)</span>
                                                </div>

                                                <!-- Review Progress Bars -->
                                                <div class="product-review-progress-wrapper">
                                                    @php $ratings = [5=>['percent'=>70, 'count'=>105], 4=>['percent'=>10, 'count'=>15], 3=>['percent'=>5, 'count'=>8], 2=>['percent'=>1, 'count'=>1], 1=>['percent'=>1, 'count'=>2]] @endphp
                                                    @foreach ($ratings as $stars => $data)
                                                        <div
                                                            class="product-review-progress-bar d-flex align-items-center gap-3 mb-2">
                                                            <div class="product-review-text fw-bold">{{ $stars }}
                                                            </div>
                                                            <div class="single-progress flex-grow-1">
                                                                <div class="progress" style="height: 8px;">
                                                                    <div class="progress-bar {{ $stars >= 4 ? 'bg-success' : ($stars == 3 ? 'bg-info' : 'bg-warning bg-opacity-50') }}"
                                                                        role="progressbar"
                                                                        style="width: {{ $data['percent'] }}%"
                                                                        aria-valuenow="{{ $data['percent'] }}"
                                                                        aria-valuemin="0" aria-valuemax="100">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="product-review-meta">
                                                                <span
                                                                    class="product-review-percent">{{ $data['percent'] }}%</span>
                                                                <span
                                                                    class="product-review-number ms-2">{{ $data['count'] }}</span>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>

                                            <!-- Sample Review -->
                                            <div class="product-comment">
                                                <ul>
                                                    <li>
                                                        <div
                                                            class="product-comment-box d-flex align-items-start gap-3 p-3 border rounded">
                                                            <div class="product-comment-avatar flex-shrink-0">
                                                                <img src="https://media.istockphoto.com/id/814423752/photo/eye-of-model-with-colorful-art-make-up-close-up.jpg?s=612x612&w=0&k=20&c=l15OdMWjgCKycMMShP8UK94ELVlEGvt7GmB_esHWPYE="
                                                                    alt="Jessica Taylor" class="rounded-circle"
                                                                    style="width: 50px; height: 50px; object-fit: cover;">
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <div
                                                                    class="product-comment-name d-flex justify-content-between flex-wrap gap-2 mb-2">
                                                                    <h6 class="mb-0"><a
                                                                            href="javascript:void(0);">Jessica Taylor</a>
                                                                    </h6>
                                                                    <span class="text-body-secondary small">Sep 5,
                                                                        2024</span>
                                                                </div>
                                                                <div class="product-user-rating text-warning mb-2">
                                                                    <i class="ri-star-fill"></i><i
                                                                        class="ri-star-fill"></i><i
                                                                        class="ri-star-fill"></i><i
                                                                        class="ri-star-fill"></i><i
                                                                        class="ri-star-fill"></i>
                                                                </div>
                                                                <p class="mb-3">These t-shirts are incredibly
                                                                    comfortable! The fabric is soft and breathable, perfect
                                                                    for all-day wear. The slim fit is flattering without
                                                                    being too tight. I've already bought three colors!</p>
                                                                <a href="javascript:void(0);"
                                                                    class="btn btn-sm btn-primary">Reply</a>
                                                            </div>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
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
    <link rel="stylesheet" href="{{ asset('assets/admin/css/plugins/jquery-confirm.min.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('assets/admin/js/plugins/jquery-confirm.min.js') }}"></script>
@endpush
