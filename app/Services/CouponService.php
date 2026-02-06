<?php

namespace App\Services;

use App\Constants\Constant;
use App\Models\Coupon;
use App\Enums\CouponType;
use Illuminate\Support\Facades\DB;

class CouponService
{
    protected string $module = 'coupons';

    /**********************************************
     * DATATABLE LIST with Permission-Based Actions
     **********************************************/
    public function getDataForDataTable($request): array
    {
        $columns = [
            'id',
            'code',
            'name',
            'type',
            'value',
            'usage_limit',
            'usage_count',
            'status',
            'starts_at',
            'expires_at',
            'updated_at',
        ];

        $query = Coupon::query();

        // Search
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Type Filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Date Range Filter
        if ($range = $request->input('date_range')) {
            $dates = explode(' to ', $range);

            if (count($dates) === 2) {
                $start = trim($dates[0]);
                $end = trim($dates[1]);

                $query->whereBetween('created_at', [
                    "{$start} 00:00:00",
                    "{$end} 23:59:59",
                ]);
            } elseif (count($dates) === 1) {
                $start = trim($dates[0]);
                $query->whereDate('created_at', $start);
            }
        }


        // Ordering
        $orderCol = $columns[$request->input('order.0.column')] ?? 'id';
        $orderDir = $request->input('order.0.dir', 'desc');
        $query->orderBy($orderCol, $orderDir);

        $recordsFiltered = $query->count();
        $recordsTotal    = Coupon::count();

        $coupons = $query
            ->skip($request->start)
            ->take($request->length)
            ->get();

        $data = $coupons->map(function ($coupon, $index) use ($request) {
            // Check permissions for current user
            $canView = checkPermission('coupons.view');
            $canEdit = checkPermission('coupons.update');
            $canDelete = checkPermission('coupons.delete');
            $canUpdateStatus = checkPermission('coupons.update.status');

            // Code with link (only if can view)
            $codeHtml = '<code>' . $coupon->code . '</code>';
            if ($canView) {
                $codeHtml = '
                    <code>' . $coupon->code . '</code>
                ';
            }

            // Name
            $nameHtml = ucfirst($coupon->name);

            // Type badge
            $typeBadge = $coupon->type === CouponType::PERCENTAGE->value
                ? '<span class="badge bg-label-info">' . $coupon->value . '%</span>'
                : '<span class="badge bg-label-primary">$' . number_format($coupon->value, 2) . '</span>';

            // Usage progress
            $usageProgress = $coupon->usage_limit
                ? '<div class="progress" style="height: 6px;">
                    <div class="progress-bar" role="progressbar" 
                         style="width: ' . ($coupon->usage_count / $coupon->usage_limit * 100) . '%" 
                         aria-valuenow="' . $coupon->usage_count . '" 
                         aria-valuemin="0" 
                         aria-valuemax="' . $coupon->usage_limit . '">
                    </div>
                   </div>
                   <small class="text-muted">' . $coupon->usage_count . ' / ' . $coupon->usage_limit . '</small>'
                : '<span class="badge bg-label-secondary">Unlimited</span>';

            // Status dropdown (only if can update status)
            $statusHtml = $coupon->status
                ? '<span class="badge bg-label-success">Active</span>'
                : '<span class="badge bg-label-danger">Inactive</span>';

            if ($canUpdateStatus) {
                $statusHtml = status_dropdown($coupon->status, [
                    'id'     => $coupon->id,
                    'url'    => route($this->module . '.status', encrypt($coupon->id)),
                    'method' => 'PUT',
                ]);
            }

            // Validity dates
            $validityHtml = '
                <div class="text-nowrap">
                    <div><small>Start: ' . $coupon->starts_at->format('d M Y') . '</small></div>
                    <div><small>Expiry: ' . $coupon->expires_at->format('d M Y') . '</small></div>
                </div>';

            // Check if coupon is currently active
            // $isActive = $coupon->isActive();
            // $isValid = $coupon->isValid();

            // $validityStatus = $isValid
            //     ? '<span class="badge bg-label-success">Valid</span>'
            //     : '<span class="badge bg-label-danger">Invalid</span>';

            // Action buttons
            $actionButtons = [];

            if ($canEdit) {
                $actionButtons[] = btn_edit(route($this->module . '.edit', encrypt($coupon->id)), true);
            }

            if ($canView) {
                $actionButtons[] = btn_view(route($this->module . '.show', encrypt($coupon->id)), true);
            }

            if ($canDelete) {
                $actionButtons[] = btn_delete(route($this->module . '.delete', encrypt($coupon->id)), true);
            }

            return [
                'id'            => $request->start + $index + 1,
                'code'          => $codeHtml,
                'name'          => $nameHtml,
                'usage'         => $usageProgress,
                'status'        => $statusHtml,
                'validity'      => $validityHtml,
                'validity_status' => '-',
                'updated_at'    => $coupon->updated_at->format('d M Y'),
                'discount'    => $typeBadge,
                'action'        => !empty($actionButtons) ? button_group($actionButtons) : 'No actions',
            ];
        });

        return [
            'draw'            => (int) $request->draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ];
    }

    /**********************************************
     * FIND
     **********************************************/
    public function findById(int $id): ?Coupon
    {
        return Coupon::find($id);
    }



    public function getCouponTypes(): array
    {
        return [
            CouponType::PERCENTAGE->value    => 'Percentage Discount',
            CouponType::FIXED_AMOUNT->value  => 'Fixed Amount Discount',
        ];
    }



    /**********************************************
     * CREATE
     **********************************************/
    public function createRecord(array $validated): Coupon
    {
        return DB::transaction(function () use ($validated) {

            $data = [
                'code'                 => strtoupper($validated['code']),
                'name'                 => $validated['name'],
                'description'          => $validated['description'] ?? null,
                'type'                 => $validated['type'],
                'value'                => $validated['value'],
                'usage_limit'          => $validated['usage_limit'] ?? null,
                'user_limit'           => $validated['user_limit'] ?? null,
                'min_purchase_amount'  => $validated['min_purchase_amount'] ?? null,
                'min_items'            => $validated['min_items'] ?? null,
                'starts_at'            => $validated['starts_at'],
                'expires_at'           => $validated['expires_at'],
                'status'               => $validated['status'] ?? Constant::ACTIVE,
                'first_order_only'     => $validated['first_order_only'] ?? false,
                'free_shipping'        => $validated['free_shipping'] ?? false,
                'included_categories'  => isset($validated['included_categories']) && !empty($validated['included_categories'])
                    ? json_encode($validated['included_categories'])
                    : null,
            ];

            return Coupon::create($data);
        });
    }
    /**********************************************
     * UPDATE
     **********************************************/
    public function updateRecordById(int $id, array $validated): Coupon
    {
        return DB::transaction(function () use ($id, $validated) {

            $coupon = Coupon::findOrFail($id);

            $data = [
                'code'                 => strtoupper($validated['code']),
                'name'                 => $validated['name'],
                'description'          => $validated['description'] ?? null,
                'type'                 => $validated['type'],
                'value'                => $validated['value'],
                'usage_limit'          => $validated['usage_limit'] ?? null,
                'user_limit'           => $validated['user_limit'] ?? null,
                'min_purchase_amount'  => $validated['min_purchase_amount'] ?? null,
                'min_items'            => $validated['min_items'] ?? null,
                'starts_at'            => $validated['starts_at'],
                'expires_at'           => $validated['expires_at'],
                'status'               => $validated['status'] ?? Constant::ACTIVE,
                'first_order_only'     => $validated['first_order_only'] ?? false,
                'free_shipping'        => $validated['free_shipping'] ?? false,
                'included_categories'  => isset($validated['included_categories']) && !empty($validated['included_categories'])
                    ? json_encode($validated['included_categories'])
                    : null,


            ];

            $coupon->update($data);

            return $coupon;
        });
    }

    /**********************************************
     * DELETE (Soft Delete)
     **********************************************/
    public function deleteRecordById(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $coupon = Coupon::findOrFail($id);
            return (bool) $coupon->delete();
        });
    }

    /**********************************************
     * RESTORE
     **********************************************/
    public function restoreRecordById(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $coupon = Coupon::withTrashed()->findOrFail($id);
            return (bool) $coupon->restore();
        });
    }

    /**********************************************
     * FORCE DELETE
     **********************************************/
    public function forceDeleteRecordById(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $coupon = Coupon::withTrashed()->findOrFail($id);
            return (bool) $coupon->forceDelete();
        });
    }

    /**********************************************
     * STATUS
     **********************************************/
    public function updateStatusById(int $id, int $status): Coupon
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->update(['status' => $status]);
        return $coupon;
    }

    /**********************************************
     * COUPON VALIDATION
     **********************************************/
    public function validateCoupon(
        string $code,
        ?int $userId = null,
        ?float $cartTotal = null,
        ?int $itemCount = null
    ): array {
        $coupon = Coupon::where('code', strtoupper($code))->first();

        if (!$coupon) {
            return [
                'valid'   => false,
                'message' => 'Coupon code is invalid.',
            ];
        }

        // Basic validations
        if (!$coupon->isActive()) {
            return [
                'valid'   => false,
                'message' => 'This coupon is not active.',
            ];
        }

        if (!$coupon->canBeUsed()) {
            return [
                'valid'   => false,
                'message' => 'This coupon has reached its usage limit.',
            ];
        }

        // User-specific validations
        if ($userId) {
            // Check if coupon is restricted to specific users
            if (
                $coupon->included_users &&
                !in_array($userId, $coupon->included_users)
            ) {
                return [
                    'valid'   => false,
                    'message' => 'This coupon is not available for your account.',
                ];
            }

            // Check if first order only
            if ($coupon->first_order_only) {
                // You need to check if this is user's first order
                // This depends on your order model
                $hasPreviousOrders = false; // Implement this check
                if ($hasPreviousOrders) {
                    return [
                        'valid'   => false,
                        'message' => 'This coupon is for first-time customers only.',
                    ];
                }
            }
        }

        // Cart amount validation
        if ($cartTotal !== null && $coupon->min_purchase_amount) {
            if ($cartTotal < $coupon->min_purchase_amount) {
                return [
                    'valid'   => false,
                    'message' => 'Minimum purchase amount required: $' . number_format($coupon->min_purchase_amount, 2),
                ];
            }
        }

        // Item count validation
        if ($itemCount !== null && $coupon->min_items) {
            if ($itemCount < $coupon->min_items) {
                return [
                    'valid'   => false,
                    'message' => 'Minimum ' . $coupon->min_items . ' items required.',
                ];
            }
        }

        // Calculate discount
        $discount = $this->calculateDiscount($coupon, $cartTotal);

        return [
            'valid'   => true,
            'message' => 'Coupon applied successfully.',
            'data'    => [
                'coupon'   => $coupon,
                'discount' => $discount,
                'free_shipping' => $coupon->free_shipping,
            ],
        ];
    }

    /**********************************************
     * CALCULATE DISCOUNT
     **********************************************/
    public function calculateDiscount(Coupon $coupon, ?float $amount = null): float
    {
        if ($coupon->type === CouponType::PERCENTAGE->value && $amount) {
            return ($amount * $coupon->value) / 100;
        }

        return $coupon->value ?? 0;
    }

    /**********************************************
     * GET STATISTICS
     **********************************************/
    public function getStatistics(int $id): array
    {
        $coupon = Coupon::findOrFail($id);

        return [
            'total_usage'      => $coupon->usage_count,
            'remaining_uses'   => $coupon->usage_limit ? $coupon->usage_limit - $coupon->usage_count : 'Unlimited',
            'usage_percentage' => $coupon->usage_limit
                ? ($coupon->usage_count / $coupon->usage_limit * 100)
                : 100,
            'is_active'        => $coupon->isActive(),
            'is_valid'         => $coupon->isValid(),
            'days_remaining'   => $coupon->expires_at->diffInDays(now()),
        ];
    }



    /**********************************************
     * INCREMENT USAGE
     **********************************************/
    public function incrementUsage(int $id): bool
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->increment('usage_count');
        return true;
    }

    /**********************************************
     * DECREMENT USAGE
     **********************************************/
    public function decrementUsage(int $id): bool
    {
        $coupon = Coupon::findOrFail($id);
        if ($coupon->usage_count > 0) {
            $coupon->decrement('usage_count');
        }
        return true;
    }

    /**********************************************
     * GET ACTIVE COUPONS
     **********************************************/
    public function getActiveCoupons(): array
    {
        return Coupon::active()
            ->valid()
            ->orderBy('name')
            ->get()
            ->map(function ($coupon) {
                return [
                    'id'   => $coupon->id,
                    'code' => $coupon->code,
                    'name' => $coupon->name . ' (' . $coupon->code . ')',
                ];
            })
            ->toArray();
    }
}
