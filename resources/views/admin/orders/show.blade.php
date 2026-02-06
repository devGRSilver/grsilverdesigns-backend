@extends('layouts.admin')

@section('content')
    <div class="app-content-area">
        <div class="app-content-wrap">
            <div class="container-fluid">

                <!-- Page Header -->
                <div class="row mb-3">
                    <div class="col-xl-12">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h1 class="fs-18 mb-0">{{ $title ?? 'Users' }} : {{ $order->order_number }} </h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb breadcrumb-example1 mb-0">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.dashboard') }}">Home</a>
                                    </li>
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('orders.index') }}">Orders</a>
                                    </li>
                                    <li class="breadcrumb-item active">Details</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- Order Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card border-start border-primary border-3 h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-2">Order Total</h6>
                                        <h4 class="mb-0">${{ number_format($order->grand_total, 2) }}</h4>
                                    </div>
                                    <div class="avatar avatar-sm bg-primary bg-opacity-10 p-2">
                                        <i class="bi bi-currency-dollar text-primary fs-4"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card border-start border-success border-3 h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-2">Status</h6>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-success">{{ $order->status }}</span>
                                        </div>
                                    </div>
                                    <div class="avatar avatar-sm bg-success bg-opacity-10 p-2">
                                        <i class="bi bi-check-circle text-success fs-4"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card border-start border-info border-3 h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-2">Order Date</h6>
                                        <h6 class="mb-0">{{ $order->created_at->format('M d, Y') }}</h6>
                                    </div>
                                    <div class="avatar avatar-sm bg-info bg-opacity-10 p-2">
                                        <i class="bi bi-calendar text-info fs-4"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card border-start border-warning border-3 h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-2">Payment</h6>
                                        <div class="d-flex align-items-center gap-2">
                                            <span
                                                class="badge bg-success">{{ ucfirst($order->payment_status ?? 'Paid') }}</span>
                                        </div>
                                    </div>
                                    <div class="avatar avatar-sm bg-warning bg-opacity-10 p-2">
                                        <i class="bi bi-credit-card text-warning fs-4"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Left Column -->
                    <div class="col-lg-8">
                        <!-- Order Items -->
                        <div class="card mb-4">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                                <h5 class="mb-0">Order Items</h5>
                                <span class="badge bg-light text-dark">{{ count($order->items) }} items</span>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>

                                                <th class="border-0">Product</th>
                                                <th class="border-0 text-end">Price</th>
                                                <th class="border-0 text-center">Qty</th>
                                                <th class="border-0 text-end pe-4">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($order->items as $item)
                                                <tr>

                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="flex-shrink-0 me-3">
                                                                <img src="{{ $item->product_image ?? URL::asset('default_images/no_image.png') }}"
                                                                    alt="Product" class="rounded" width="60">
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <h6 class="mb-1">{{ $item->product_name }}</h6>
                                                                <small class="text-muted">SKU: {{ $item->sku }}</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-end">${{ number_format($item->unit_price, 2) }}</td>
                                                    <td class="text-center">{{ $item->quantity }}</td>
                                                    <td class="text-end pe-4 fw-semibold">
                                                        ${{ number_format($item->unit_price * $item->quantity, 2) }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center py-4">
                                                        <p class="text-muted mb-0">No items found</p>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Order Summary -->
                                <div class="p-4">
                                    <div class="row">
                                        <div class="col-md-6 offset-md-6">
                                            <table class="table table-borderless mb-0">
                                                <tbody>
                                                    <tr>
                                                        <td class="text-muted">Subtotal:</td>
                                                        <td class="text-end">${{ number_format($order->subtotal ?? 0, 2) }}
                                                        </td>
                                                    </tr>
                                                    @if ($order->discount > 0)
                                                        <tr>
                                                            <td class="text-muted">Discount:</td>
                                                            <td class="text-end text-danger">
                                                                -${{ number_format($order->discount, 2) }}</td>
                                                        </tr>
                                                    @endif
                                                    @if ($order->tax > 0)
                                                        <tr>
                                                            <td class="text-muted">Tax:</td>
                                                            <td class="text-end">+${{ number_format($order->tax, 2) }}</td>
                                                        </tr>
                                                    @endif
                                                    @if ($order->shipping_cost > 0)
                                                        <tr>
                                                            <td class="text-muted">Shipping:</td>
                                                            <td class="text-end">
                                                                +${{ number_format($order->shipping_cost, 2) }}</td>
                                                        </tr>
                                                    @endif
                                                    <tr class="border-top">
                                                        <td class="fw-bold">Total Amount:</td>
                                                        <td class="text-end fw-bold fs-5">
                                                            ${{ number_format($order->grand_total, 2) }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Order Timeline -->
                        <div class="card mt-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Order Status Timeline</h5>
                            </div>
                            <div class="card-body">
                                <div class="timeline mt-4">
                                    <!-- Completed Steps -->
                                    <div class="timeline-item completed">
                                        <div class="timeline-marker bg-primary"></div>
                                        <div class="timeline-content">
                                            <div class="d-flex justify-content-between">
                                                <h6 class="mb-1">Order Placed</h6>
                                                <small
                                                    class="text-muted">{{ $order->created_at->format('M d, g:i A') }}</small>
                                            </div>
                                            <p class="text-muted mb-0">Order #{{ $order->order_number }} has been placed
                                                successfully</p>
                                        </div>
                                    </div>

                                    <div class="timeline-item completed">
                                        <div class="timeline-marker bg-primary"></div>
                                        <div class="timeline-content">
                                            <div class="d-flex justify-content-between">
                                                <h6 class="mb-1">Payment Confirmed</h6>
                                                <small
                                                    class="text-muted">{{ $order->created_at->format('M d, g:i A') }}</small>
                                            </div>
                                            <p class="text-muted mb-0">Payment of
                                                ${{ number_format($order->grand_total, 2) }} received via
                                                {{ $order->payment_method ?? 'Credit Card' }}</p>
                                        </div>
                                    </div>

                                    <div class="timeline-item completed">
                                        <div class="timeline-marker bg-primary"></div>
                                        <div class="timeline-content">
                                            <div class="d-flex justify-content-between">
                                                <h6 class="mb-1">Processing</h6>
                                                <small
                                                    class="text-muted">{{ $order->created_at->addDay()->format('M d, g:i A') }}</small>
                                            </div>
                                            <p class="text-muted mb-0">Order is being prepared for shipment</p>
                                        </div>
                                    </div>

                                    <div class="timeline-item completed">
                                        <div class="timeline-marker bg-primary"></div>
                                        <div class="timeline-content">
                                            <div class="d-flex justify-content-between">
                                                <h6 class="mb-1">Shipped</h6>
                                                <small
                                                    class="text-muted">{{ $order->created_at->addDays(2)->format('M d, g:i A') }}</small>
                                            </div>
                                            <p class="text-muted mb-0">Package has left the warehouse</p>
                                            @if ($order->tracking_number)
                                                <small class="text-primary">Tracking:
                                                    {{ $order->tracking_number }}</small>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="timeline-item completed">
                                        <div class="timeline-marker bg-primary"></div>
                                        <div class="timeline-content">
                                            <div class="d-flex justify-content-between">
                                                <h6 class="mb-1">Out for Delivery</h6>
                                                <small
                                                    class="text-muted">{{ $order->created_at->addDays(3)->format('M d, g:i A') }}</small>
                                            </div>
                                            <p class="text-muted mb-0">Package with local delivery driver</p>
                                        </div>
                                    </div>

                                    <!-- Current Step -->
                                    <div class="timeline-item current">
                                        <div class="timeline-marker bg-success">
                                            <i class="bi bi-check text-white"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <div class="d-flex justify-content-between">
                                                <h6 class="mb-1">Delivered</h6>
                                                <small
                                                    class="text-muted">{{ $order->created_at->addDays(3)->format('M d, g:i A') }}</small>
                                            </div>
                                            <p class="text-muted mb-0">Package delivered to customer</p>
                                            @if ($order->delivered_to)
                                                <small class="text-success">Signed by: {{ $order->delivered_to }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-lg-4">
                        <!-- Customer Info -->
                        <div class="card mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Customer Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0 me-3">
                                        <img src="{{ $order->user->avatar ?? URL::asset('default_images/no_user.png') }}"
                                            alt="Avatar" class="rounded-circle" width="60">
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $order->user->name }}</h6>
                                        <p class="text-muted mb-0">Customer ID: #{{ $order->user->id }}</p>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <h6 class="text-muted mb-2">Contact Info</h6>
                                    <p class="mb-1">
                                        <i class="bi bi-envelope me-2 text-muted"></i>
                                        {{ $order->user->email }}
                                    </p>
                                    @if ($order->user->phone)
                                        <p class="mb-0">
                                            <i class="bi bi-telephone me-2 text-muted"></i>
                                            {{ $order->user->phone_code }} {{ $order->user->phone }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Shipping & Billing -->
                        <div class="card mt-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Shipping & Billing</h5>
                            </div>

                            <div class="card-body">

                                {{-- SHIPPING ADDRESS --}}
                                <div class="mb-4">
                                    <h6 class="text-muted mb-3">
                                        <i class="bi bi-truck me-2"></i> Shipping Address
                                    </h6>

                                    @php($shipping = $order->shipping_address)

                                    @if ($shipping)
                                        <address class="mb-0">
                                            <strong>{{ $shipping->name ?? $order->user->name }}</strong><br>

                                            @if ($shipping->company)
                                                {{ $shipping->company }}<br>
                                            @endif

                                            {{ $shipping->address_line_1 }}<br>

                                            @if ($shipping->address_line_2)
                                                {{ $shipping->address_line_2 }}<br>
                                            @endif

                                            {{ $shipping->city }},
                                            {{ $shipping->state }}
                                            {{ $shipping->postal_code }}<br>

                                            {{ $shipping->country }}<br>

                                            @if ($shipping->phone)
                                                <i class="bi bi-telephone me-1"></i> {{ $shipping->phone }}
                                            @endif
                                        </address>
                                    @else
                                        <span class="text-muted">No shipping address</span>
                                    @endif
                                </div>

                                {{-- BILLING ADDRESS --}}
                                <div>
                                    <h6 class="text-muted mb-3">
                                        <i class="bi bi-credit-card me-2"></i> Billing Address
                                    </h6>

                                    @php($billing = $order->billing_address)

                                    @if ($billing)
                                        <address class="mb-0">
                                            <strong>{{ $billing->name ?? $order->user->name }}</strong><br>

                                            @if ($billing->company)
                                                {{ $billing->company }}<br>
                                            @endif

                                            {{ $billing->address_line_1 }}<br>

                                            @if ($billing->address_line_2)
                                                {{ $billing->address_line_2 }}<br>
                                            @endif

                                            {{ $billing->city }},
                                            {{ $billing->state }}
                                            {{ $billing->postal_code }}<br>

                                            {{ $billing->country }}<br>

                                            @if ($billing->phone)
                                                <i class="bi bi-telephone me-1"></i> {{ $billing->phone }}
                                            @endif
                                        </address>
                                    @else
                                        <span class="text-muted">No billing address</span>
                                    @endif
                                </div>

                            </div>
                        </div>


                        <!-- Payment Details -->
                        <div class="card mt-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Payment Details</h5>
                            </div>

                            <div class="card-body">

                                @php($payment = $order->transaction)

                                {{-- PAYMENT METHOD --}}
                                <div class="mb-3">
                                    <h6 class="text-muted text-muted mt-3 mb-2">Payment Method</h6>

                                    <div class="d-flex align-items-center">
                                        <div class="bg-light rounded p-2 me-3">
                                            <i class="bi bi-credit-card-2-front text-primary fs-4"></i>
                                        </div>

                                        <div>
                                            <h6 class="mb-1">
                                                {{ ucfirst($payment->payment_method ?? 'N/A') }}
                                            </h6>
                                            <p class="text-muted mb-0">
                                                {{ $payment->payment_gateway ?? '—' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                {{-- PAYMENT STATUS --}}
                                <div class="mb-3">
                                    <h6 class="text-muted mb-2">Payment Status</h6>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <span
                                            class="badge 
                                                    @if ($payment?->status === 'paid') bg-success
                                                    @elseif($payment?->status === 'failed') bg-danger
                                                    @elseif($payment?->status === 'refunded') bg-warning
                                                    @else bg-secondary @endif
                                                ">
                                            {{ $payment->status ?? 'Pending' }}
                                        </span>

                                        <span class="text-muted">
                                            {{ optional($payment?->created_at)->format('M d, Y') }}
                                        </span>
                                    </div>
                                </div>

                                {{-- AMOUNT DETAILS --}}
                                <div>

                                    {{-- Gross Amount --}}
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span>Amount</span>
                                        <span class="fw-bold">
                                            {{ $payment?->currency_code ?? '$' }}
                                            {{ number_format($payment?->amount ?? 0, 2) }}
                                        </span>
                                    </div>

                                    {{-- Gateway Fee --}}
                                    @if (!empty($payment?->gateway_fee))
                                        <div class="d-flex justify-content-between align-items-center mb-1 text-muted">
                                            <span>Gateway Fee</span>
                                            <span>
                                                {{ $payment?->currency_code ?? '$' }}
                                                {{ number_format($payment->gateway_fee, 2) }}
                                            </span>
                                        </div>
                                    @endif

                                    {{-- Net Amount --}}
                                    <div class="d-flex justify-content-between align-items-center border-top pt-2 mt-2">
                                        <span class="fw-semibold">Net Amount</span>
                                        <span class="fw-bold">
                                            {{ $payment?->currency_code ?? '$' }}
                                            {{ number_format($payment?->amount - $payment?->gateway_fee, 2) }}
                                        </span>
                                    </div>
                                </div>



                            </div>
                        </div>


                        <!-- Order Actions -->
                        <div class="card mt-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button class="btn btn-outline-primary">
                                        <i class="bi bi-chat me-2"></i> Contact Customer
                                    </button>
                                    <button class="btn btn-outline-secondary">
                                        <i class="bi bi-receipt me-2"></i> View Invoice
                                    </button>
                                    <button class="btn btn-outline-danger">
                                        <i class="bi bi-arrow-clockwise me-2"></i> Download PDF
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
