<?php

namespace App\Services;

use App\Constants\Constant;
use App\Models\Category;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SubCategoriesService
{
    protected string $module = 'subcategories';

    /**********************************************
     * DATATABLE LIST with Permission-Based Actions
     **********************************************/
    public function getDataForDataTable($request): array
    {
        $columns = ['id', 'image', 'banner_image', 'name', 'slug', 'status', 'updated_at'];

        $query = Category::query()
            ->whereNotNull('parent_id')
            ->with('parent');

        // Search
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('meta_title', 'like', "%{$search}%");
            });
        }

        // Filters
        if ($request->filled('parent_id')) {
            $query->where('parent_id', $request->parent_id);
        }

        if ($request->filled('is_primary')) {
            $query->where('is_primary', $request->is_primary);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Ordering
        $orderCol = $columns[$request->input('order.0.column')] ?? 'id';
        $orderDir = $request->input('order.0.dir', 'desc');
        $query->orderBy($orderCol, $orderDir);

        $recordsFiltered = $query->count();
        $recordsTotal = Category::whereNotNull('parent_id')->count();

        $datas = $query
            ->skip($request->start)
            ->take($request->length)
            ->get();

        $data = $datas->map(function ($category, $index) use ($request) {
            // Check permissions for action buttons
            $canView = checkPermission('subcategories.view');
            $canEdit = checkPermission('subcategories.update');
            $canDelete = checkPermission('subcategories.delete');
            $canUpdateStatus = checkPermission('subcategories.update.status');

            // Status dropdown - only show toggle if user has permission
            $statusHtml = '';
            if ($canUpdateStatus) {
                $statusHtml = status_dropdown($category->status, [
                    'id'     => $category->id,
                    'url'    => route($this->module . '.status', encrypt($category->id)),
                    'method' => 'PUT',
                ]);
            } else {
                $statusHtml = $category->status
                    ? '<span class="badge bg-label-success">Active</span>'
                    : '<span class="badge bg-label-danger">Inactive</span>';
            }

            // Action buttons - only show buttons for allowed actions
            $actionButtons = [];

            if ($canEdit) {
                $actionButtons[] = btn_edit(route($this->module . '.edit', encrypt($category->id)), true);
            }

            if ($canView) {
                $actionButtons[] = btn_view(route($this->module . '.show', encrypt($category->id)), true);
            }

            if ($canDelete) {
                $actionButtons[] = btn_delete(route($this->module . '.delete', encrypt($category->id)), true);
            }

            return [
                'id'           => $request->start + $index + 1,
                'image'        => image_show($category->image, 50, 50),
                'banner_image' => image_show($category->banner_image, 50, 50),
                'name'         => ucfirst($category->name),
                'parent_name'  => $category->parent->name ?? '-',
                'slug'         => $category->slug,
                'product_count' => 0,
                'status'       => $statusHtml,
                'updated_at'   => $category->updated_at->format('d M Y'),
                'action'       => !empty($actionButtons) ? button_group($actionButtons) : 'No actions',
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
    public function findById(int $id): Category
    {
        return Category::whereNotNull('parent_id')
            ->with('parent')
            ->findOrFail($id);
    }

    /**********************************************
     * CREATE
     **********************************************/
    public function createRecord(array $validated): Category
    {
        DB::beginTransaction();

        try {
            $slug = $this->generateUniqueSlug($validated['name']);

            $data = [
                'parent_id'        => $validated['category_id'],
                'name'             => $validated['name'],
                'slug'             => $slug,
                'is_primary'       => !empty($validated['is_primary']),
                'meta_title'       => $validated['meta_title'] ?? null,
                'meta_description' => $validated['meta_description'] ?? null,
                'meta_keywords'    => !empty($validated['meta_keywords'])
                    ? json_encode($validated['meta_keywords'])
                    : null,
                'status'           => $validated['status'] ?? Constant::ACTIVE,
            ];

            if (!empty($validated['image'])) {
                $data['image'] = imageUpload($validated['image'], 'uploads/category', 800, 800);
            }

            if (!empty($validated['banner_image'])) {
                $data['banner_image'] = imageUpload($validated['banner_image'], 'uploads/category', 500, 1600);
            }

            $category = Category::create($data);

            DB::commit();
            return $category;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Subcategory creation failed: {$e->getMessage()}");
            throw $e;
        }
    }

    /**********************************************
     * UPDATE
     **********************************************/
    public function updateRecordById(int $id, array $validated): Category
    {
        DB::beginTransaction();

        try {
            $category = Category::whereNotNull('parent_id')->findOrFail($id);

            $data = [
                'parent_id'        => $validated['category_id'],
                'name'             => $validated['name'],
                'is_primary'       => !empty($validated['is_primary']),
                'meta_title'       => $validated['meta_title'] ?? null,
                'meta_description' => $validated['meta_description'] ?? null,
                'meta_keywords'    => !empty($validated['meta_keywords'])
                    ? json_encode($validated['meta_keywords'])
                    : null,
                'status'           => $validated['status'] ?? Constant::ACTIVE,
            ];

            // Update slug only if name changed
            if ($category->name !== $validated['name']) {
                $data['slug'] = $this->generateUniqueSlug($validated['name'], $category->id);
            }

            // Handle image upload
            if (!empty($validated['image'])) {
                $this->deleteFile($category->image);
                $data['image'] = imageUpload($validated['image'], 'uploads/category', 800, 800);
            }

            // Handle banner image upload
            if (!empty($validated['banner_image'])) {
                $this->deleteFile($category->banner_image);
                $data['banner_image'] = imageUpload($validated['banner_image'], 'uploads/category', 500, 1600);
            }

            $category->update($data);

            DB::commit();
            return $category;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Subcategory update failed. ID {$id}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**********************************************
     * DELETE
     **********************************************/
    public function deleteRecordById(int $id): bool
    {
        DB::beginTransaction();

        try {
            $category = Category::whereNotNull('parent_id')->findOrFail($id);

            // Check if category has products before deletion (optional)
            // if ($category->products()->exists()) {
            //     throw new Exception('Cannot delete subcategory with products.');
            // }

            // Delete associated files
            $this->deleteFile($category->image);
            $this->deleteFile($category->banner_image);

            $category->delete();

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Subcategory delete failed. ID {$id}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**********************************************
     * STATUS
     **********************************************/
    public function updateStatusById(int $id, int $status): Category
    {
        DB::beginTransaction();

        try {
            $category = Category::whereNotNull('parent_id')->findOrFail($id);
            $category->update(['status' => $status]);

            DB::commit();
            return $category;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Subcategory status update failed. ID {$id}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**********************************************
     * ACTIVE SUBCATEGORIES
     **********************************************/
    public function getActiveSubCategories(int $parentId)
    {
        try {
            return Category::where([
                'parent_id' => $parentId,
                'status'    => Constant::ACTIVE,
            ])->select('id', 'name')->get();
        } catch (Exception $e) {
            Log::error("Failed to fetch active subcategories for parent ID {$parentId}: {$e->getMessage()}");
            return collect(); // Return empty collection on error
        }
    }

    /**********************************************
     * HELPER METHODS
     **********************************************/
    private function generateUniqueSlug(string $name, $ignoreId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while (
            Category::where('slug', $slug)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        return $slug;
    }

    private function deleteFile(?string $path): void
    {
        if ($path && file_exists(public_path($path))) {
            @unlink(public_path($path)); // Suppress errors if file doesn't exist
        }
    }

    /**********************************************
     * ADDITIONAL METHODS
     **********************************************/

    /**
     * Get subcategories for dropdown (by parent ID)
     */
    public function getSubcategoriesForDropdown(int $parentId): array
    {
        return Category::where('parent_id', $parentId)
            ->where('status', Constant::ACTIVE)
            ->select('id', 'name')
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    /**
     * Check if subcategory can be deleted
     */
    public function canDeleteSubcategory(int $id): array
    {
        $subcategory = Category::withCount('products')
            ->whereNotNull('parent_id')
            ->findOrFail($id);

        $canDelete = true;
        $message = '';

        if ($subcategory->products_count > 0) {
            $canDelete = false;
            $message = "Cannot delete subcategory with {$subcategory->products_count} products.";
        }

        return [
            'can_delete' => $canDelete,
            'message' => $message,
            'products_count' => $subcategory->products_count,
        ];
    }

    /**
     * Get subcategories count by parent
     */
    public function getCountByParent(int $parentId): int
    {
        return Category::where('parent_id', $parentId)->count();
    }
}
