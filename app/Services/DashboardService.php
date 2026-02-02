<?php

namespace App\Services;

use App\Constants\Constant;
use App\Enums\OrderStatus;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Cache duration in minutes
     */
    protected int $cacheDuration = 1;

    /**
     * Generate cache key based on method name and request data
     */
    private function generateCacheKey(string $method, array $requestData): string
    {
        $key = 'dashboard_' . $method . '_' . md5(serialize($requestData));
        return $key;
    }

    /**
     * Clear all dashboard cache
     */
    public function clearDashboardCache(): void
    {
        Cache::forget('dashboard_stats_*');
        Cache::forget('dashboard_revenue_*');
        Cache::forget('dashboard_countries_*');
        Cache::forget('dashboard_trending_*');
        Cache::forget('dashboard_bestselling_*');
        Cache::forget('dashboard_categories_*');
        Cache::forget('dashboard_transactions_*');
        Cache::forget('dashboard_customers_*');
        Cache::forget('dashboard_orders_*');
    }

    /**
     * Get dashboard statistics with caching
     */
    public function getStats(array $requestData): array
    {
        $cacheKey = $this->generateCacheKey('stats', $requestData);

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($requestData) {
            $startDate = null;
            $endDate   = null;

            if (!empty($requestData['date_range'])) {
                [$start, $end] = array_pad(
                    explode(' to ', $requestData['date_range']),
                    2,
                    $requestData['date_range']
                );

                $startDate = Carbon::parse($start)->startOfDay();
                $endDate   = Carbon::parse($end)->endOfDay();
            }

            // Product stats (date-filtered)
            $productQuery = Product::query();

            if ($startDate && $endDate) {
                $productQuery->whereBetween('products.created_at', [$startDate, $endDate]);
            }

            $totalProducts = $productQuery->count();

            $activeProductsQuery = Product::where('status', Constant::ACTIVE);

            if ($startDate && $endDate) {
                $activeProductsQuery->whereBetween('products.created_at', [$startDate, $endDate]);
            }

            $activeProducts = $activeProductsQuery->count();

            /*
         |--------------------------------------------------------------------------
         | Order stats (date-filtered)
         |--------------------------------------------------------------------------
         */
            $orderQuery = Order::query();

            if ($startDate && $endDate) {
                $orderQuery->whereBetween('orders.created_at', [$startDate, $endDate]);
            }

            $orderStats = $orderQuery->selectRaw(
                "
            COUNT(*) as total_orders,

            SUM(CASE 
                WHEN status = ? THEN 1 
                ELSE 0 
            END) as new_orders,

            SUM(CASE 
                WHEN status IN (?, ?, ?, ?, ?, ?) THEN 1 
                ELSE 0 
            END) as processing_orders,

            SUM(CASE 
                WHEN status = ? THEN 1 
                ELSE 0 
            END) as completed_orders,

            SUM(CASE 
                WHEN status IN (?, ?) THEN 1 
                ELSE 0 
            END) as cancelled_orders
            ",
                [
                    // New
                    OrderStatus::PENDING_PAYMENT,

                    // Processing
                    OrderStatus::PAYMENT_RECEIVED,
                    OrderStatus::CONFIRMED,
                    OrderStatus::PROCESSING,
                    OrderStatus::PACKED,
                    OrderStatus::SHIPPED,
                    OrderStatus::OUT_FOR_DELIVERY,

                    // Completed
                    OrderStatus::DELIVERED,

                    // Cancelled
                    OrderStatus::CANCEL_REQUESTED,
                    OrderStatus::CANCELLED,
                ]
            )->first();

            /*
         |--------------------------------------------------------------------------
         | User stats (date-filtered)
         |--------------------------------------------------------------------------
         */
            $userQuery = User::whereHas('roles', function ($q) {
                $q->where('name', 'user');
            });

            if ($startDate && $endDate) {
                $userQuery->whereBetween('users.created_at', [$startDate, $endDate]);
            }

            $totalCustomers = $userQuery->count();

            $activeCustomersQuery = User::where('status', Constant::ACTIVE)
                ->whereHas('roles', function ($q) {
                    $q->where('name', 'user');
                });

            if ($startDate && $endDate) {
                $activeCustomersQuery->whereBetween('users.created_at', [$startDate, $endDate]);
            }

            $activeCustomers = $activeCustomersQuery->count();

            /*
         |--------------------------------------------------------------------------
         | Final Response
         |--------------------------------------------------------------------------
         */
            return [
                'total_orders'      => (int) ($orderStats->total_orders ?? 0),
                'new_orders'        => (int) ($orderStats->new_orders ?? 0),
                'processing_orders' => (int) ($orderStats->processing_orders ?? 0),
                'completed_orders'  => (int) ($orderStats->completed_orders ?? 0),
                'cancelled_orders'  => (int) ($orderStats->cancelled_orders ?? 0),

                'total_customers'   => $totalCustomers,
                'active_customers'  => $activeCustomers,

                'total_products'    => $totalProducts,
                'active_products'   => $activeProducts,
            ];
        });
    }


    public function getRevenueChart(array $requestData): array
    {
        $cacheKey = $this->generateCacheKey('revenue', $requestData);

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($requestData) {
            if (empty($requestData['date_range'])) {
                return [
                    'labels'  => [],
                    'orders'  => [],
                    'revenue' => [],
                    'profit'  => [],
                ];
            }

            [$start, $end] = array_pad(explode(' to ', $requestData['date_range']), 2, null);

            $startDate = Carbon::parse($start)->startOfDay();
            $endDate   = Carbon::parse($end ?? $start)->endOfDay();

            $endDate = min($endDate, now()); // prevent future dates

            if ($startDate->gt($endDate)) {
                return ['labels' => [], 'orders' => [], 'revenue' => [], 'profit' => []];
            }

            $diffDays  = $startDate->diffInDays($endDate);
            $sameMonth = $startDate->isSameMonth($endDate);
            $sameYear  = $startDate->isSameYear($endDate);

            $labels = $orders = $revenue = $profit = [];

            // Determine period type: day, month, year
            if ($diffDays <= 31) {
                $periodFormat = '%Y-%m-%d';
                $labelFormat  = 'd M';
            } elseif ($sameYear) {
                $periodFormat = '%Y-%m';
                $labelFormat  = 'M Y';
            } else {
                $periodFormat = '%Y';
                $labelFormat  = 'Y';
            }

            // Aggregate data from order_items
            $data = DB::table('orders as o')
                ->leftJoin('order_items as oi', 'oi.order_id', '=', 'o.id')
                ->selectRaw("
                DATE_FORMAT(o.created_at, '{$periodFormat}') as period,
                COUNT(DISTINCT o.id) as total_orders,
                COALESCE(SUM(o.grand_total),0) as revenue,
                COALESCE(SUM(oi.profit),0) as profit
            ")
                ->whereBetween('o.created_at', [$startDate, $endDate])
                ->whereNull('o.cancelled_at')
                ->groupBy('period')
                ->orderBy('period')
                ->get()
                ->keyBy('period');

            // Fill gaps
            $current = $startDate->copy();
            while ($current <= $endDate) {
                $key = $current->format(
                    $diffDays <= 31 ? 'Y-m-d' : ($sameYear ? 'Y-m' : 'Y')
                );

                $labels[]  = $current->format($labelFormat);
                $orders[]  = (int) ($data[$key]->total_orders ?? 0);
                $revenue[] = (float) ($data[$key]->revenue ?? 0);
                $profit[]  = (float) ($data[$key]->profit ?? 0);

                $current = $diffDays <= 31
                    ? $current->addDay()
                    : ($sameYear ? $current->addMonth() : $current->addYear());
            }

            return compact('labels', 'orders', 'revenue', 'profit');
        });
    }


    /**
     * Get top countries with proper date filtering and caching
     */
    public function getTopCountries(array $requestData): array
    {
        $cacheKey = $this->generateCacheKey('countries', $requestData);

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($requestData) {
            $colors = [
                'bg-primary',
                'bg-success',
                'bg-info',
                'bg-warning',
                'bg-secondary',
                'bg-danger',
            ];

            $startDate = null;
            $endDate   = null;

            if (!empty($requestData['date_range'])) {
                $dateRangeParts = explode(' to ', $requestData['date_range']);
                $start = $dateRangeParts[0];
                $end = $dateRangeParts[1] ?? $dateRangeParts[0];

                $startDate = Carbon::parse($start)->startOfDay();
                $endDate   = Carbon::parse($end)->endOfDay();
            }

            /*
         |--------------------------------------------------------------------------
         | Total Orders (with same filters)
         |--------------------------------------------------------------------------
         */
            $totalOrdersQuery = DB::table('orders as o')
                ->join('order_addresses as oa', 'oa.order_id', '=', 'o.id');

            if ($startDate && $endDate) {
                $totalOrdersQuery->whereBetween('o.created_at', [$startDate, $endDate]);
            }

            $totalOrders = $totalOrdersQuery->count();

            if ($totalOrders === 0) {
                return [];
            }

            /*
         |--------------------------------------------------------------------------
         | Country-wise percentage (using subquery for accurate calculation)
         |--------------------------------------------------------------------------
         */
            $resultsQuery = DB::table('orders as o')
                ->join('order_addresses as oa', 'oa.order_id', '=', 'o.id')
                ->select(
                    'oa.country',
                    DB::raw('COUNT(o.id) as total_orders'),
                    DB::raw("ROUND((COUNT(o.id) * 100.0) / ?, 2) as percentage")
                )
                ->addBinding($totalOrders, 'select');

            if ($startDate && $endDate) {
                $resultsQuery->whereBetween('o.created_at', [$startDate, $endDate]);
            }

            $results = $resultsQuery
                ->groupBy('oa.country')
                ->orderByDesc('total_orders')
                ->limit(10)
                ->get();

            return $results->values()->map(function ($item, $index) use ($colors) {
                return [
                    'country'       => $item->country ?? 'Unknown',
                    'total_orders'  => (int) $item->total_orders,
                    'percentage'    => (float) $item->percentage,
                    'color'         => $colors[$index % count($colors)],
                ];
            })->toArray();
        });
    }

    /**
     * Get trending products with caching
     */
    public function getTrendingProducts(array $requestData): array
    {
        $cacheKey = $this->generateCacheKey('trending', $requestData);

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($requestData) {
            $limit = $requestData['limit'] ?? 5;

            $startDate = null;
            $endDate   = null;

            if (!empty($requestData['date_range'])) {
                $dateRangeParts = explode(' to ', $requestData['date_range']);
                $start = $dateRangeParts[0];
                $end = $dateRangeParts[1] ?? $dateRangeParts[0];

                $startDate = Carbon::parse($start)->startOfDay();
                $endDate   = Carbon::parse($end)->endOfDay();
            }

            $query = DB::table('order_items as oi')
                ->join('products as p', 'p.id', '=', 'oi.product_id')
                ->join('orders as o', 'o.id', '=', 'oi.order_id')
                ->select(
                    'p.id',
                    'p.name',
                    'p.selling_price',
                    'p.main_image',
                    DB::raw('SUM(oi.quantity) as total_sold'),
                    DB::raw('COUNT(DISTINCT o.id) as order_count')
                )
                ->whereNull('o.cancelled_at')
                ->where('o.status', OrderStatus::DELIVERED->value);

            if ($startDate && $endDate) {
                $query->whereBetween('o.created_at', [$startDate, $endDate]);
            }

            $products = $query
                ->groupBy('p.id', 'p.name', 'p.selling_price', 'p.main_image')
                ->orderByDesc('total_sold')
                ->limit($limit)
                ->get();

            return $products->map(function ($product) {
                return [
                    'id'        => $product->id,
                    'name'      => $product->name,
                    'price'     => number_format($product->selling_price, 2),
                    'main_image'     => $product->main_image
                        ? $product->main_image
                        : asset('default_images/no_image.png'),
                    'total_sold' => (int) $product->total_sold,
                    'order_count' => (int) $product->order_count,
                    'url'       => route('products.show', $product->id),
                ];
            })->toArray();
        });
    }

    public function getBestSellingProducts(array $requestData): array
    {
        $cacheKey = $this->generateCacheKey('bestselling', $requestData);

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($requestData) {

            $limit = $requestData['limit'] ?? 5;

            $variantStockSub = DB::table('product_variants')
                ->select(
                    'product_id',
                    DB::raw('SUM(stock_quantity) as total_variant_qty')
                )
                ->groupBy('product_id');

            $query = DB::table('order_items as oi')
                ->join('products as p', 'p.id', '=', 'oi.product_id')
                ->join('orders as o', 'o.id', '=', 'oi.order_id')
                ->leftJoinSub($variantStockSub, 'vs', function ($join) {
                    $join->on('vs.product_id', '=', 'p.id');
                })
                ->where('o.status', OrderStatus::DELIVERED->value)
                ->whereNull('o.cancelled_at')
                ->select(
                    'p.id',
                    'p.name',
                    'p.selling_price',
                    'p.main_image',
                    DB::raw('SUM(oi.quantity) as sold_qty'),
                    DB::raw('COUNT(DISTINCT o.id) as orders'),
                    DB::raw('SUM(oi.quantity * oi.total) as total_revenue'),
                    DB::raw('COALESCE(vs.total_variant_qty, 0) as available_qty')
                );

            if (!empty($requestData['date_range'])) {
                [$start, $end] = array_pad(explode(' to ', $requestData['date_range']), 2, null);

                $query->whereBetween('o.created_at', [
                    Carbon::parse($start)->startOfDay(),
                    Carbon::parse($end ?? $start)->endOfDay(),
                ]);
            }

            $products = $query
                ->groupBy(
                    'p.id',
                    'p.name',
                    'p.selling_price',
                    'p.main_image',
                    'vs.total_variant_qty'
                )
                ->orderByDesc('sold_qty')
                ->limit($limit)
                ->get();

            return $products->map(fn($product) => [
                'image'     => $product->main_image ?: asset('default_images/no_image.png'),
                'name'      => $product->name,
                'available' => (int) $product->available_qty,
                'price'     => number_format($product->selling_price, 2),
                'orders'    => (int) $product->orders,
                'qty'       => (int) $product->sold_qty,
                'total'     => number_format($product->total_revenue, 2),
            ])->toArray();
        });
    }


    /**
     * Get top categories with date filtering and caching
     */
    public function getTopCategories(array $requestData): array
    {
        $cacheKey = $this->generateCacheKey('categories', $requestData);

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($requestData) {
            $limit = $requestData['limit'] ?? 5;

            $startDate = null;
            $endDate   = null;

            if (!empty($requestData['date_range'])) {
                $dateRangeParts = explode(' to ', $requestData['date_range']);
                $start = $dateRangeParts[0];
                $end = $dateRangeParts[1] ?? $dateRangeParts[0];

                $startDate = Carbon::parse($start)->startOfDay();
                $endDate   = Carbon::parse($end)->endOfDay();
            }

            $query = Category::query()
                ->select([
                    'categories.id',
                    'categories.name',
                    'categories.slug',
                    DB::raw('COALESCE(SUM(order_items.total), 0) as revenue'),
                    DB::raw('COUNT(DISTINCT order_items.order_id) as orders'),
                    DB::raw('COALESCE(SUM(order_items.total) / NULLIF(COUNT(DISTINCT order_items.order_id), 0), 0) as avg_order'),
                ])
                ->join('products', 'products.category_id', '=', 'categories.id')
                ->join('order_items', 'order_items.product_id', '=', 'products.id')
                ->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->where('orders.status', OrderStatus::DELIVERED)
                ->whereNull('orders.cancelled_at');

            if ($startDate && $endDate) {
                $query->whereBetween('orders.created_at', [$startDate, $endDate]);
            }

            return $query
                ->groupBy('categories.id', 'categories.name', 'categories.slug')
                ->orderByDesc('revenue')
                ->limit($limit)
                ->get()
                ->map(function ($category) {
                    return [
                        'name'      => $category->name,
                        'slug'      => $category->slug,
                        'revenue'   => number_format($category->revenue, 2),
                        'orders'    => (int) $category->orders,
                        'avg_order' => number_format($category->avg_order, 2),
                    ];
                })
                ->toArray();
        });
    }

    /**
     * Get recent transactions with date filtering and caching
     */
    public function getRecentTransactions(array $requestData): array
    {
        $cacheKey = $this->generateCacheKey('transactions', $requestData);

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($requestData) {
            $limit = $requestData['limit'] ?? 5;

            $startDate = null;
            $endDate   = null;

            if (!empty($requestData['date_range'])) {
                $dateRangeParts = explode(' to ', $requestData['date_range']);
                $start = $dateRangeParts[0];
                $end = $dateRangeParts[1] ?? $dateRangeParts[0];

                $startDate = Carbon::parse($start)->startOfDay();
                $endDate   = Carbon::parse($end)->endOfDay();
            }

            $query = Transaction::with('order');

            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }

            return $query
                ->latest()
                ->limit($limit)
                ->get()
                ->map(function ($transaction) {
                    return [
                        'title'        => $transaction->transaction_id,
                        'date'         => $transaction->created_at->format('M d, Y H:i'),
                        'method'       => ucfirst($transaction->payment_method),
                        'amount'       => ($transaction->amount < 0 ? '-' : '') .
                            $transaction->currency_code . ' ' .
                            number_format(abs($transaction->amount), 2),
                        'amount_class' => $transaction->amount < 0 ? 'text-danger' : 'text-success',
                        'status'       => ucfirst(strtolower($transaction->status->value)),
                        'status_class' => $this->getTransactionStatusClass($transaction->status->value),
                    ];
                })
                ->toArray();
        });
    }

    /**
     * Get top customers with date filtering and caching
     */
    public function getTopCustomers(array $requestData): array
    {
        $cacheKey = $this->generateCacheKey('customers', $requestData);

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($requestData) {
            $limit = $requestData['limit'] ?? 5;

            $startDate = null;
            $endDate   = null;

            if (!empty($requestData['date_range'])) {
                $dateRangeParts = explode(' to ', $requestData['date_range']);
                $start = $dateRangeParts[0];
                $end = $dateRangeParts[1] ?? $dateRangeParts[0];

                $startDate = Carbon::parse($start)->startOfDay();
                $endDate   = Carbon::parse($end)->endOfDay();
            }

            $query = DB::table('users as u')
                ->join('model_has_roles as mhr', 'mhr.model_id', '=', 'u.id')
                ->join('roles as r', 'r.id', '=', 'mhr.role_id')
                ->join('orders as o', 'o.user_id', '=', 'u.id')
                ->select(
                    'u.id',
                    'u.name',
                    'u.email',
                    'u.profile_picture',
                    DB::raw('COUNT(DISTINCT o.id) as total_orders'),
                    DB::raw('COALESCE(SUM(o.grand_total), 0) as total_spent')
                )
                ->where('r.name', 'user')
                ->where('u.status', Constant::ACTIVE)
                ->whereNull('o.cancelled_at');

            if ($startDate && $endDate) {
                $query->whereBetween('o.created_at', [$startDate, $endDate]);
            }

            $customers = $query
                ->groupBy('u.id', 'u.name', 'u.email', 'u.profile_picture')
                ->orderByDesc('total_orders')
                ->limit($limit)
                ->get();

            return $customers->map(function ($customer) {
                $nameParts = explode(' ', $customer->name);
                $initials = strtoupper(substr($nameParts[0], 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''));

                return [
                    'avatar'     => $customer->avatar ?? null,
                    'initials'   => $initials,
                    'name'       => $customer->name,
                    'email'      => $customer->email,
                    'orders'     => (int) $customer->total_orders,
                    'total_spent' => number_format($customer->total_spent, 2),
                ];
            })->toArray();
        });
    }

    /**
     * Get recent orders with date filtering and caching
     */
    public function getRecentOrders(array $requestData): array
    {
        $cacheKey = $this->generateCacheKey('orders', $requestData);

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($requestData) {
            $limit = $requestData['limit'] ?? 5;

            $query = Order::with(['user', 'items.product']);

            if (!empty($requestData['date_range'])) {
                [$start, $end] = array_pad(explode(' to ', $requestData['date_range']), 2, null);

                $query->whereBetween('created_at', [
                    Carbon::parse($start)->startOfDay(),
                    Carbon::parse($end ?? $start)->endOfDay()
                ]);
            }

            $orders = $query->latest()->limit($limit)->get();

            return $orders->map(function ($order) {
                $firstItem = $order->items?->first();

                return [
                    'id'            => $order->id,
                    'main_image'      => '#' . $order->main_image,
                    'order_id'      => '#' . $order->order_number,
                    'status'      => $order->status,
                    'customer_name' => $order->user->name ?? 'Guest',
                    'customer_email' => $order->user->email ?? '',
                    'product_name'  => $firstItem?->product?->name ?? 'N/A',
                    'quantity'      => $order->items?->sum('quantity') ?? 0,
                    'amount'        => number_format($order->grand_total, 2),
                    'date'          => $order->created_at->format('M d, Y'),
                ];
            })->toArray();
        });
    }

    /**
     * Helper: Get transaction status CSS class
     */
    private function getTransactionStatusClass(string $status): string
    {
        return match (strtolower($status)) {
            'success', 'completed' => 'bg-success',
            'pending' => 'bg-warning',
            'failed', 'rejected' => 'bg-danger',
            default => 'bg-secondary',
        };
    }
}
