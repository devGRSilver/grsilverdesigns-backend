<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ResponseController;
use App\Models\MetalAssignCategory;
use App\Services\CategoriesService;
use App\Services\MetalsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MetalController extends ResponseController
{
    protected string $resource = 'metals';
    protected string $resourceName = 'Metal';

    public function __construct(
        protected MetalsService $metalService,
        protected CategoriesService $categoriesService
    ) {}

    /**
     * Metal Price & Categories List
     */
    public function index(Request $request)
    {
        // Permission: metals.view.any (handled by middleware)
        $goldCategory = MetalAssignCategory::select(
            'category_id',
            DB::raw('GROUP_CONCAT(sub_category_id ORDER BY sub_category_id ASC) AS sub_category_ids')
        )
            ->where('metal_id', 1)
            ->groupBy('category_id')
            ->get();

        $silverCategory = MetalAssignCategory::select(
            'category_id',
            DB::raw('GROUP_CONCAT(sub_category_id ORDER BY sub_category_id ASC) AS sub_category_ids')
        )
            ->where('metal_id', 2)
            ->groupBy('category_id')
            ->get();

        return view("admin.{$this->resource}.index", [
            'title'           => "{$this->resourceName} Price Update",
            'goldCategory'    => $goldCategory,
            'silverCategory'  => $silverCategory,
            'metals'          => $this->metalService->getMetals(),
        ]);
    }

    /**
     * Assign Category Form
     */
    public function assign($type)
    {
        // Permission: metals.assign (handled by middleware)
        return view("admin.{$this->resource}.add", [
            'title'       => "Assign Category with " . ucfirst($type),
            'categories'  => $this->categoriesService->getActiveParentCategories(),
            'metals'      => $this->metalService->getMetals(),
            'type'        => $type,
        ]);
    }

    /**
     * Assign Category
     */
    public function assignCategory(Request $request)
    {
        // Permission: metals.assign (handled by middleware)
        $validated = $request->validate([
            'metal_id'        => 'required|exists:metals,id',
            'parent_id'       => 'required',
            'sub_category_id' => 'required|array',
            'sub_category_id.*' => 'required',
        ]);

        DB::beginTransaction();

        try {
            foreach ($validated['sub_category_id'] as $subId) {
                MetalAssignCategory::updateOrCreate(
                    [
                        'metal_id'        => $validated['metal_id'],
                        'category_id'     => decrypt($validated['parent_id']),
                        'sub_category_id' => $subId,
                    ],
                    [
                        'metal_id'        => $validated['metal_id'],
                        'category_id'     => decrypt($validated['parent_id']),
                        'sub_category_id' => $subId,
                    ]
                );
            }

            DB::commit();

            return $this->successResponse(
                [],
                "Categories assigned successfully",
                route('metals.index')
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Metal assign category failed: ' . $e->getMessage());
            return $this->errorResponse(
                "Failed to assign categories to metal.",
                500
            );
        }
    }

    /**
     * Update Metal Price
     */
    public function update(Request $request, $encryptedId)
    {
        // Permission: metals.update (handled by middleware)
        try {
            $id = decrypt($encryptedId); // Fixed: Added decrypt

            $validated = $request->validate([
                'price' => 'required|numeric|min:0',
            ]);

            $this->metalService->updateRecordById($id, [
                'price_per_gram' => $validated['price']
            ]);

            return $this->successResponse(
                [],
                "{$this->resourceName} price updated successfully."
            );
        } catch (\Exception $e) {
            Log::error('Metal update failed: ' . $e->getMessage());
            return $this->errorResponse(
                "Failed to update {$this->resourceName} price.",
                500
            );
        }
    }

    /**
     * Delete Main Category Assignment
     */
    public function deleteMainCategory($metal_id, $category_id)
    {
        // Permission: metals.category.delete (handled by middleware)
        DB::beginTransaction();
        try {
            MetalAssignCategory::where('metal_id', $metal_id)
                ->where('category_id', $category_id)
                ->delete();

            DB::commit();

            return $this->successResponse(
                [],
                "Assigned category has been removed successfully."
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Metal delete main category failed: ' . $e->getMessage());
            return $this->errorResponse(
                "Failed to remove assigned category.",
                500
            );
        }
    }

    /**
     * Delete Sub Category Assignment
     */
    public function deleteSubCategory($metal_id, $category_id, $sub_category_id)
    {
        // Permission: metals.subcategory.delete (handled by middleware)
        DB::beginTransaction();
        try {
            MetalAssignCategory::where([
                'metal_id'        => $metal_id,
                'category_id'     => $category_id,
                'sub_category_id' => $sub_category_id,
            ])->delete();

            DB::commit();

            return $this->successResponse(
                [],
                "Assigned sub-category has been removed successfully."
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Metal delete sub-category failed: ' . $e->getMessage());
            return $this->errorResponse(
                "Failed to remove assigned sub-category.",
                500
            );
        }
    }
}
