<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ResponseController;
use App\Http\Requests\Admin\StatusUpdateRequest;
use App\Http\Requests\Admin\SubCategoriesRequest;
use App\Services\CategoriesService;
use App\Services\SubCategoriesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SubcategoryController extends ResponseController
{
    protected string $resource = 'subcategories';
    protected string $resourceName = 'Sub Category';

    protected SubCategoriesService $subCategoriesService;
    protected CategoriesService $categoriesService;

    public function __construct(
        SubCategoriesService $subCategoriesService,
        CategoriesService $categoriesService
    ) {
        $this->subCategoriesService = $subCategoriesService;
        $this->categoriesService   = $categoriesService;
    }

    /**
     * List Sub Categories
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return response()->json(
                $this->subCategoriesService->getDataForDataTable($request)
            );
        }

        return view("admin.{$this->resource}.index", [
            'title'        => "{$this->resourceName} List",
            'resource'     => $this->resource,
            'resourceName' => $this->resourceName,
            'categories'   => $this->categoriesService->getParentCategories(),
        ]);
    }

    /**
     * Create Form
     */
    public function create()
    {
        return view("admin.{$this->resource}.add", [
            'title'      => "Add {$this->resourceName}",
            'categories' => $this->categoriesService->getActiveParentCategories(),
        ]);
    }

    /**
     * Store Sub Category
     */
    public function store(SubCategoriesRequest $request)
    {
        try {
            $this->subCategoriesService->createRecord($request->validated());

            return $this->successResponse(
                [],
                "{$this->resourceName} created successfully."
            );
        } catch (\Exception $e) {
            Log::error('SubCategory store failed: ' . $e->getMessage());

            return $this->errorResponse(
                "Failed to create {$this->resourceName}.",
                500
            );
        }
    }

    /**
     * Edit Form
     */
    public function edit(string $encryptedId)
    {
        try {
            $id = decrypt($encryptedId);
            $category = $this->subCategoriesService->findById($id);

            // Check if category exists
            if (!$category) {
                return redirect()->route('subcategories.index')
                    ->with('error', "{$this->resourceName} not found.");
            }

            return view("admin.{$this->resource}.edit", [
                'title'      => "Edit {$this->resourceName}",
                'category'   => $category,
                'categories' => $this->categoriesService->getActiveParentCategories(),
            ]);
        } catch (\Exception $e) {
            Log::error('SubCategory edit failed: ' . $e->getMessage());

            return redirect()->route('subcategories.index')
                ->with('error', 'Invalid subcategory reference.');
        }
    }

    /**
     * Update Sub Category
     */
    public function update(SubCategoriesRequest $request, string $encryptedId)
    {
        try {
            $id = decrypt($encryptedId);

            $this->subCategoriesService->updateRecordById(
                $id,
                $request->validated()
            );

            return $this->successResponse(
                [],
                "{$this->resourceName} updated successfully."
            );
        } catch (\Exception $e) {
            Log::error('SubCategory update failed: ' . $e->getMessage());

            return $this->errorResponse(
                "Failed to update {$this->resourceName}.",
                500
            );
        }
    }

    /**
     * Show Sub Category Details
     */
    public function show(string $encryptedId)
    {
        try {
            $id = decrypt($encryptedId);
            $category = $this->subCategoriesService->findById($id);

            // Check if category exists
            if (!$category) {
                return redirect()->route('subcategories.index')
                    ->with('error', "{$this->resourceName} not found.");
            }

            return view("admin.{$this->resource}.show", [
                'title'    => "{$this->resourceName} Details",
                'category' => $category,
            ]);
        } catch (\Exception $e) {
            Log::error('SubCategory show failed: ' . $e->getMessage());

            return redirect()->route('subcategories.index')
                ->with('error', 'Invalid subcategory reference.');
        }
    }

    /**
     * Delete Sub Category
     */
    public function delete(string $encryptedId)
    {
        try {
            $id = decrypt($encryptedId);
            $this->subCategoriesService->deleteRecordById($id);

            return $this->successResponse(
                [],
                "{$this->resourceName} deleted successfully."
            );
        } catch (\Exception $e) {
            Log::error('SubCategory delete failed: ' . $e->getMessage());

            return $this->errorResponse(
                "Failed to delete {$this->resourceName}.",
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

            $this->subCategoriesService->updateStatusById(
                $id,
                $request->status
            );

            return $this->successResponse(
                [],
                'Status updated successfully.'
            );
        } catch (\Exception $e) {
            Log::error('SubCategory status update failed: ' . $e->getMessage());

            return $this->errorResponse(
                'Failed to update status.',
                500
            );
        }
    }

    /**
     * Get Sub Categories by Parent (AJAX)
     */
    public function subcategories(string $encryptedId)
    {
        try {
            $parentId = decrypt($encryptedId); // Fixed: Added decrypt
            return response()->json(
                $this->subCategoriesService->getActiveSubCategories($parentId)
            );
        } catch (\Exception $e) {
            Log::error('Fetch subcategories failed: ' . $e->getMessage());

            return response()->json([
                'error' => 'Failed to fetch subcategories',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
