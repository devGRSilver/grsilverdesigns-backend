<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ResponseController;
use App\Http\Requests\Admin\BlogRequest;
use App\Http\Requests\Admin\StatusUpdateRequest;
use App\Services\BlogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BlogController extends ResponseController
{
    protected string $resource = 'blogs';
    protected string $resourceName = 'Blog';

    public function __construct(
        protected BlogService $blogService
    ) {}

    /**
     * Blog List
     */
    public function index(Request $request)
    {
        // Optional: Add permission check (though middleware handles it)
        // if (!auth()->guard('admin')->user()->can('blogs.view.any')) {
        //     abort(403, 'Unauthorized');
        // }

        if ($request->ajax()) {
            return response()->json(
                $this->blogService->getDataForDataTable($request)
            );
        }

        return view("admin.{$this->resource}.index", [
            'title'        => "{$this->resourceName} List",
            'resource'     => $this->resource,
            'resourceName' => $this->resourceName,
        ]);
    }

    /**
     * Create Blog Form
     */
    public function create()
    {
        // Permission: blogs.create (handled by middleware)
        return view("admin.{$this->resource}.add", [
            'title' => "Add {$this->resourceName}",
        ]);
    }

    /**
     * Store Blog
     */
    public function store(BlogRequest $request)
    {
        // Permission: blogs.create (handled by middleware)
        try {
            $this->blogService->createRecord($request->validated());

            return $this->successResponse(
                [],
                "{$this->resourceName} created successfully.",
                route('blogs.index')
            );
        } catch (\Exception $e) {
            Log::error('Blog store failed: ' . $e->getMessage());

            return $this->errorResponse(
                "Failed to create {$this->resourceName}.",
                500
            );
        }
    }

    /**
     * Edit Blog Form
     */
    public function edit(string $encryptedId)
    {
        // Permission: blogs.update (handled by middleware)
        try {
            $id   = decrypt($encryptedId);
            $blog = $this->blogService->findById($id);

            return view("admin.{$this->resource}.edit", [
                'title' => "Edit {$this->resourceName}",
                'blog'  => $blog,
            ]);
        } catch (\Exception $e) {
            Log::error('Blog edit failed: ' . $e->getMessage());

            return redirect()->route('blogs.index')
                ->with('error', "{$this->resourceName} not found.");
        }
    }

    /**
     * Update Blog
     */
    public function update(BlogRequest $request, string $encryptedId)
    {
        // Permission: blogs.update (handled by middleware)
        try {
            $id = decrypt($encryptedId);

            $this->blogService->updateRecordById(
                $id,
                $request->validated()
            );

            return $this->successResponse(
                [],
                "{$this->resourceName} updated successfully.",
                route('blogs.index')
            );
        } catch (\Exception $e) {
            Log::error('Blog update failed: ' . $e->getMessage());
            return $this->errorResponse(
                "Failed to update {$this->resourceName}.",
                500
            );
        }
    }

    /**
     * Show Blog
     */
    public function show(string $encryptedId)
    {
        // Permission: blogs.view (handled by middleware)
        try {
            $id   = decrypt($encryptedId);
            $blog = $this->blogService->findById($id);

            return view("admin.{$this->resource}.show", [
                'title' => "{$this->resourceName} Details",
                'blog'  => $blog,
            ]);
        } catch (\Exception $e) {
            Log::error('Blog show failed: ' . $e->getMessage());

            return redirect()->route('blogs.index')
                ->with('error', "{$this->resourceName} not found.");
        }
    }

    /**
     * Delete Blog
     */
    public function delete(string $encryptedId)
    {
        // Permission: blogs.delete (handled by middleware)
        try {
            $id = decrypt($encryptedId);

            $this->blogService->deleteRecordById($id);

            return $this->successResponse(
                [],
                "{$this->resourceName} deleted successfully."
            );
        } catch (\Exception $e) {
            Log::error('Blog delete failed: ' . $e->getMessage());

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
        // Permission: blogs.update.status (handled by middleware)
        try {
            $id = decrypt($encryptedId);

            $this->blogService->updateStatusById($id, $request->status);

            return $this->successResponse([], 'Status updated successfully.');
        } catch (\Exception $e) {
            Log::error('Blog status update failed: ' . $e->getMessage());

            return $this->errorResponse(
                'Failed to update status.',
                500
            );
        }
    }
}
