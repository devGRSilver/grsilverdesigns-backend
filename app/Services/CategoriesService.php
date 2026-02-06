<?php

namespace App\Services;

use App\Constants\Constant;
use App\Models\Category;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CategoriesService
{
    protected string $module = 'categories';

    /**********************************************
     * Parent Categories
     **********************************************/
    public function getParentCategories()
    {
        return Category::ParentCategory()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
    }

    public function getActiveParentCategories()
    {
        return Category::where('status', Constant::ACTIVE)
            ->whereNull('parent_id')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
    }


    public function getActiveSubCategories()
    {
        return  Category::query()
            ->leftJoin('categories as parents', 'parents.id', '=', 'categories.parent_id')
            ->where('categories.status', Constant::ACTIVE)
            ->select(
                'categories.id',
                'categories.name',
                'categories.parent_id',
                'parents.name as parent_name'
            )
            ->orderByRaw('CASE WHEN parents.name IS NULL THEN 0 ELSE 1 END') // parent first
            ->orderBy('parent_name')
            ->orderBy('categories.name')
            ->get();
    }




    /**********************************************
     * DataTable with Permission-Based Actions
     **********************************************/
    public function getDataForDataTable($request)
    {
        $columns = ['id', 'image', 'banner_image', 'name', 'slug', 'status', 'created_at'];

        $baseQuery = Category::ParentCategory()->withCount('subCategories');

        /** SEARCH */
        if ($search = $request->input('search.value')) {
            $baseQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('meta_title', 'like', "%{$search}%");
            });
        }

        /** FILTER */
        if ($request->filled('status')) {
            $baseQuery->where('status', $request->status);
        }

        $recordsFiltered = (clone $baseQuery)->count();
        $recordsTotal    = Category::ParentCategory()->count();

        /** ORDER */
        $orderCol = $columns[$request->input('order.0.column')] ?? 'id';
        $orderDir = $request->input('order.0.dir', 'asc');

        $categories = $baseQuery
            ->orderBy($orderCol, $orderDir)
            ->skip($request->start)
            ->take($request->length)
            ->get();

        $data = $categories->map(function ($category, $index) use ($request) {
            // Check permissions for action buttons
            $canView = checkPermission('categories.view');
            $canEdit = checkPermission('categories.update');
            $canDelete = checkPermission('categories.delete');
            $canUpdateStatus = checkPermission('categories.update.status');

            // Status dropdown - only show toggle if user has permission
            $statusHtml = '';
            if ($canUpdateStatus) {
                $statusHtml = status_dropdown($category->status, [
                    'id'  => $category->id,
                    'url' => route($this->module . '.status', encrypt($category->id)),
                ]);
            } else {
                $statusHtml = $category->status
                    ? '<span class="badge bg-label-success">Active</span>'
                    : '<span class="badge bg-label-danger">Inactive</span>';
            }

            // Subcategories count link
            $subCategoriesCount = '';
            if ($category->sub_categories_count > 0) {
                $subCategoriesCount = redirect_to_link(
                    route('subcategories.index', ['parent_id' => $category->id]),
                    $category->sub_categories_count
                );
            } else {
                $subCategoriesCount = '<span class="badge bg-label-secondary">0</span>';
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
                'id'    => $request->start + $index + 1,
                'image' => image_show($category->image, 50),
                'banner_image' => image_show($category->banner_image, 50),
                'name'  => ucfirst($category->name),
                'slug'  => $category->slug,
                'status' => $statusHtml,
                'sub_categories_count' => $subCategoriesCount,
                'updated_at' => $category->updated_at->format('d F Y'),
                'action' => !empty($actionButtons) ? button_group($actionButtons) : 'No actions',
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
     * Find Category
     **********************************************/
    public function findById(int $id)
    {
        return Category::with('parent')
            ->withCount('subCategories')
            ->findOrFail($id);
    }

    /**********************************************
     * Create / Update
     **********************************************/
    public function createRecord(array $validated)
    {
        return $this->saveCategory(null, $validated);
    }

    public function updateRecordById(int $id, array $validated)
    {
        return $this->saveCategory($id, $validated);
    }

    /**********************************************
     * Delete Category (Safe)
     **********************************************/
    public function deleteRecordById(int $id)
    {
        DB::beginTransaction();

        try {
            $category = Category::with('subCategories')->findOrFail($id);

            if ($category->subCategories()->exists()) {
                throw new Exception('Cannot delete category with subcategories.');
            }

            // Delete associated files
            $this->deleteFile($category->image);
            $this->deleteFile($category->banner_image);

            $category->delete();

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Category delete failed. ID {$id}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**********************************************
     * Status Update
     **********************************************/
    public function updateStatusById(int $id, bool $status)
    {
        DB::beginTransaction();

        try {
            $category = Category::findOrFail($id);
            $category->update(['status' => $status]);

            DB::commit();
            return $category;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Status update failed. ID {$id}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**********************************************
     * Save Category (Private Method)
     **********************************************/
    private function saveCategory(?int $id, array $validated)
    {
        DB::beginTransaction();

        try {
            $category = $id ? Category::findOrFail($id) : new Category();

            $originalName = $category->name;

            $category->fill([
                'parent_id'        => $validated['parent_id'] ?? null,
                'name'             => $validated['name'],
                'is_primary'       => !empty($validated['is_primary']),
                'meta_title'       => $validated['meta_title'] ?? null,
                'meta_description' => $validated['meta_description'] ?? null,
                'status'           => $validated['status'] ?? Constant::ACTIVE,
                'meta_keywords'    => $validated['meta_keywords'] ?? null,
            ]);

            // Update slug only if name changed or new category
            if (!$id || $originalName !== $validated['name']) {
                $category->slug = $this->generateUniqueSlug(
                    Str::slug($validated['name']),
                    $id
                );
            }

            // Handle image upload
            if (!empty($validated['image'])) {
                $this->deleteFile($category->image);
                $category->image = imageUpload($validated['image'], 'uploads/category', 800, 800);
            }

            // Handle banner image upload
            if (!empty($validated['banner_image'])) {
                $this->deleteFile($category->banner_image);
                $category->banner_image = imageUpload($validated['banner_image'], 'uploads/category', 500, 1600);
            }

            $category->save();

            DB::commit();
            return $category;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Category save failed: {$e->getMessage()}");
            throw $e;
        }
    }

    /**********************************************
     * Helper Methods
     **********************************************/
    private function generateUniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $original = $slug;
        $count = 1;

        while (
            Category::where('slug', $slug)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = "{$original}-{$count}";
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
     * Additional Useful Methods
     **********************************************/

    /**
     * Get categories for dropdown (with hierarchy)
     */
    public function getCategoriesForDropdown(?int $excludeId = null): array
    {
        $categories = Category::ParentCategory()
            ->with('subCategories')
            ->orderBy('name')
            ->get();

        $result = [];

        foreach ($categories as $parent) {
            if ($excludeId && $parent->id == $excludeId) {
                continue;
            }

            $result[$parent->name] = [];

            foreach ($parent->subCategories as $child) {
                if ($excludeId && $child->id == $excludeId) {
                    continue;
                }
                $result[$parent->name][$child->id] = str_repeat('&nbsp;&nbsp;', 2) . $child->name;
            }
        }

        return $result;
    }

    /**
     * Check if category can be deleted
     */
    public function canDeleteCategory(int $id): array
    {
        $category = Category::withCount(['subCategories', 'products'])
            ->findOrFail($id);

        $canDelete = true;
        $message = '';

        if ($category->sub_categories_count > 0) {
            $canDelete = false;
            $message = "Cannot delete category with {$category->sub_categories_count} subcategories.";
        } elseif ($category->products_count > 0) {
            $canDelete = false;
            $message = "Cannot delete category with {$category->products_count} products.";
        }

        return [
            'can_delete' => $canDelete,
            'message' => $message,
            'subcategories_count' => $category->sub_categories_count,
            'products_count' => $category->products_count,
        ];
    }
}
