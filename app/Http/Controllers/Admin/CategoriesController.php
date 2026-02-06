<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ResponseController;
use App\Http\Requests\Admin\CategoriesRequest;
use App\Http\Requests\Admin\StatusUpdateRequest;
use App\Services\CategoriesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoriesController extends ResponseController
{
    protected string $resource = 'categories';
    protected string $resourceName = 'Category';
    protected CategoriesService $categoriesService;

    public function __construct(CategoriesService $categoriesService)
    {
        $this->categoriesService = $categoriesService;
    }

    /**
     * Category List
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return response()->json(
                $this->categoriesService->getDataForDataTable($request)
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
     * Store Category
     */
    public function store(CategoriesRequest $request)
    {
        try {
            $this->categoriesService->createRecord($request->validated());

            return $this->successResponse(
                [],
                "{$this->resourceName} created successfully."
            );
        } catch (\Exception $e) {
            Log::error('Category store failed: ' . $e->getMessage());

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
            $category = $this->categoriesService->findById($id);

            // Prevent editing if category is not found
            if (!$category) {
                return redirect()->route('categories.index')
                    ->with('error', "{$this->resourceName} not found.");
            }

            return view("admin.{$this->resource}.edit", [
                'title'      => "Edit {$this->resourceName}",
                'category'   => $category,
                'categories' => $this->categoriesService->getActiveParentCategories(),
            ]);
        } catch (\Exception $e) {
            Log::error('Category edit failed: ' . $e->getMessage());

            return redirect()->route('categories.index')
                ->with('error', 'Invalid category reference.');
        }
    }

    /**
     * Update Category
     */
    public function update(CategoriesRequest $request, string $encryptedId)
    {
        try {
            $id = decrypt($encryptedId);

            $this->categoriesService->updateRecordById(
                $id,
                $request->validated()
            );

            return $this->successResponse(
                [],
                "{$this->resourceName} updated successfully."
            );
        } catch (\Exception $e) {
            Log::error('Category update failed: ' . $e->getMessage());

            return $this->errorResponse(
                "Failed to update {$this->resourceName}.",
                500
            );
        }
    }

    /**
     * Show Category Details
     */
    public function show(string $encryptedId)
    {
        try {
            $id = decrypt($encryptedId);
            $category = $this->categoriesService->findById($id);

            // Handle category not found
            if (!$category) {
                return redirect()->route('categories.index')
                    ->with('error', "{$this->resourceName} not found.");
            }

            return view("admin.{$this->resource}.show", [
                'title'    => "{$this->resourceName} Details",
                'category' => $category,
            ]);
        } catch (\Exception $e) {
            Log::error('Category show failed: ' . $e->getMessage());

            return redirect()->route('categories.index')
                ->with('error', 'Invalid category reference.');
        }
    }

    /**
     * Delete Category
     */
    public function delete(string $encryptedId)
    {
        try {
            $id = decrypt($encryptedId);
            $this->categoriesService->deleteRecordById($id);

            return $this->successResponse(
                [],
                "{$this->resourceName} deleted successfully."
            );
        } catch (\Exception $e) {
            Log::error('Category delete failed: ' . $e->getMessage());

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

            $this->categoriesService->updateStatusById(
                $id,
                $request->status
            );

            return $this->successResponse(
                [],
                'Status updated successfully.'
            );
        } catch (\Exception $e) {
            Log::error('Category status update failed: ' . $e->getMessage());

            return $this->errorResponse(
                'Failed to update status.',
                500
            );
        }
    }
}
