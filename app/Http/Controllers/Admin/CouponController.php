<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ResponseController;
use App\Http\Requests\Admin\StatusUpdateRequest;
use App\Http\Requests\Admin\CouponRequest;
use App\Services\CategoriesService;
use App\Services\CouponService;
use App\Services\ProductService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CouponController extends ResponseController
{
    protected string $resource = 'coupons';
    protected string $resourceName = 'Coupon';

    protected CouponService $couponService;
    protected ProductService $productService;
    protected CategoriesService $categoryService;
    protected UserService $userService;

    public function __construct(
        CouponService $couponService,
        ProductService $productService,
        CategoriesService $categoryService,
        UserService $userService
    ) {
        $this->couponService = $couponService;
        $this->productService = $productService;
        $this->categoryService = $categoryService;
        $this->userService = $userService;
    }

    /**
     * List Coupons
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return response()->json(
                $this->couponService->getDataForDataTable($request)
            );
        }

        return view("admin.{$this->resource}.index", [
            'title'        => "{$this->resourceName} List",
            'resource'     => $this->resource,
            'resourceName' => $this->resourceName,
        ]);
    }

    /**
     * Create Form
     */
    public function create()
    {
        return view("admin.{$this->resource}.add", [
            'title'           => "Add {$this->resourceName}",
            'couponTypes'     => $this->couponService->getCouponTypes(),
            'categories'      => $this->categoryService->getActiveParentCategories(),
        ]);
    }

    /**
     * Store Coupon
     */
    public function store(CouponRequest $request)
    {
        try {
            $this->couponService->createRecord($request->validated());

            return $this->successResponse(
                [],
                "{$this->resourceName} created successfully."
            );
        } catch (\Exception $e) {
            Log::error('Coupon store failed: ' . $e->getMessage());

            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Edit Form
     */
    public function edit(string $encryptedId)
    {
        try {
            $id = decrypt($encryptedId);
            $coupon = $this->couponService->findById($id);

            // Check if coupon exists
            if (!$coupon) {
                return redirect()->route('coupons.index')
                    ->with('error', "{$this->resourceName} not found.");
            }

            return view("admin.{$this->resource}.edit", [
                'title'       => "Edit {$this->resourceName}",
                'coupon'      => $coupon,
                'couponTypes' => $this->couponService->getCouponTypes(),
                'categories'  => $this->categoryService->getActiveParentCategories(),
            ]);
        } catch (\Exception $e) {
            Log::error('Coupon edit failed: ' . $e->getMessage());

            return redirect()->route('coupons.index')
                ->with('error', 'Invalid coupon reference.');
        }
    }

    /**
     * Update Coupon
     */
    public function update(CouponRequest $request, string $encryptedId)
    {
        try {
            $id = decrypt($encryptedId);

            $this->couponService->updateRecordById(
                $id,
                $request->validated()
            );

            return $this->successResponse(
                [],
                "{$this->resourceName} updated successfully."
            );
        } catch (\Exception $e) {
            dd($e);
            Log::error('Coupon update failed: ' . $e->getMessage());

            return $this->errorResponse(
                "Failed to update {$this->resourceName}.",
                500
            );
        }
    }

    /**
     * Show Coupon Details
     */
    public function show(string $encryptedId)
    {
        try {
            $id = decrypt($encryptedId);
            $coupon = $this->couponService->findById($id);

            // Check if coupon exists
            if (!$coupon) {
                return redirect()->route('coupons.index')
                    ->with('error', "{$this->resourceName} not found.");
            }

            return view("admin.{$this->resource}.show", [
                'title'  => "{$this->resourceName} Details",
                'coupon' => $coupon,
            ]);
        } catch (\Exception $e) {
            Log::error('Coupon show failed: ' . $e->getMessage());

            return redirect()->route('coupons.index')
                ->with('error', 'Invalid coupon reference.');
        }
    }

    /**
     * Delete Coupon (Soft Delete)
     */
    public function delete(string $encryptedId)
    {
        try {
            $id = decrypt($encryptedId);
            $this->couponService->deleteRecordById($id);

            return $this->successResponse(
                [],
                "{$this->resourceName} deleted successfully."
            );
        } catch (\Exception $e) {
            Log::error('Coupon delete failed: ' . $e->getMessage());

            return $this->errorResponse(
                "Failed to delete {$this->resourceName}.",
                500
            );
        }
    }

    /**
     * Restore Soft Deleted Coupon
     */
    public function restore(string $encryptedId)
    {
        try {
            $id = decrypt($encryptedId);
            $this->couponService->restoreRecordById($id);

            return $this->successResponse(
                [],
                "{$this->resourceName} restored successfully."
            );
        } catch (\Exception $e) {
            Log::error('Coupon restore failed: ' . $e->getMessage());

            return $this->errorResponse(
                "Failed to restore {$this->resourceName}.",
                500
            );
        }
    }

    /**
     * Force Delete Coupon
     */
    public function forceDelete(string $encryptedId)
    {
        try {
            $id = decrypt($encryptedId);
            $this->couponService->forceDeleteRecordById($id);

            return $this->successResponse(
                [],
                "{$this->resourceName} permanently deleted."
            );
        } catch (\Exception $e) {
            Log::error('Coupon force delete failed: ' . $e->getMessage());

            return $this->errorResponse(
                "Failed to permanently delete {$this->resourceName}.",
                500
            );
        }
    }

    /**
     * Update Status
     */
    public function updateStatus(StatusUpdateRequest $request, string $encryptedId)
    {
        try {
            $id = decrypt($encryptedId);

            $this->couponService->updateStatusById(
                $id,
                $request->status
            );

            return $this->successResponse(
                [],
                'Status updated successfully.'
            );
        } catch (\Exception $e) {
            Log::error('Coupon status update failed: ' . $e->getMessage());

            return $this->errorResponse(
                'Failed to update status.',
                500
            );
        }
    }

    /**
     * Validate Coupon Code (for checkout)
     */
    public function validateCoupon(Request $request)
    {
        try {
            $request->validate([
                'code' => 'required|string',
                'user_id' => 'nullable|exists:users,id',
                'cart_total' => 'nullable|numeric|min:0',
                'item_count' => 'nullable|integer|min:0',
            ]);

            $result = $this->couponService->validateCoupon(
                $request->code,
                $request->user_id,
                $request->cart_total,
                $request->item_count
            );

            if ($result['valid']) {
                return $this->successResponse(
                    $result['data'],
                    'Coupon is valid.'
                );
            }

            return $this->errorResponse(
                $result['message'],
                400
            );
        } catch (\Exception $e) {
            Log::error('Coupon validation failed: ' . $e->getMessage());

            return $this->errorResponse(
                'Failed to validate coupon.',
                500
            );
        }
    }

    /**
     * Get Coupon Usage Statistics
     */
    public function statistics(string $encryptedId)
    {
        try {
            $id = decrypt($encryptedId);
            $statistics = $this->couponService->getStatistics($id);

            return $this->successResponse(
                $statistics,
                'Coupon statistics retrieved.'
            );
        } catch (\Exception $e) {
            Log::error('Coupon statistics failed: ' . $e->getMessage());

            return $this->errorResponse(
                'Failed to get coupon statistics.',
                500
            );
        }
    }
}
