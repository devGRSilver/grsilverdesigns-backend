<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ResponseController;
use App\Http\Requests\Admin\ContentRequest;
use App\Http\Requests\Admin\StatusUpdateRequest;
use App\Services\ContentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContentController extends ResponseController
{
    protected string $resource = 'contents';
    protected string $resourceName = 'Content';

    public function __construct(
        protected ContentService $contentService
    ) {}

    /**
     * Content List
     */
    public function index(Request $request)
    {
        // Permission: contents.view.any (handled by middleware)
        if ($request->ajax()) {
            return response()->json(
                $this->contentService->getDataForDataTable($request)
            );
        }

        return view("admin.{$this->resource}.index", [
            'title'        => "{$this->resourceName} List",
            'resource'     => $this->resource,
            'resourceName' => $this->resourceName,
        ]);
    }

    /**
     * Edit Content Form
     */
    public function edit(string $encryptedId)
    {
        // Permission: contents.update (handled by middleware)
        try {
            $id = decrypt($encryptedId);

            return view("admin.{$this->resource}.edit", [
                'title'   => "Edit {$this->resourceName}",
                'content' => $this->contentService->findById($id),
            ]);
        } catch (\Exception $e) {
            Log::error('Content edit failed: ' . $e->getMessage());

            return redirect()->route('contents.index')
                ->with('error', "{$this->resourceName} not found.");
        }
    }

    /**
     * Update Content
     */
    public function update(ContentRequest $request, string $encryptedId)
    {
        // Permission: contents.update (handled by middleware)
        try {
            $id = decrypt($encryptedId);
            $this->contentService->updateRecordById($id, $request->validated());

            return $this->successResponse(
                [],
                "{$this->resourceName} updated successfully.",
                route('contents.index')
            );
        } catch (\Exception $e) {
            Log::error('Content update failed: ' . $e->getMessage());
            return $this->errorResponse(
                "Failed to update {$this->resourceName}.",
                500
            );
        }
    }

    /**
     * Show Content Details
     */
    public function show(string $encryptedId)
    {
        // Permission: contents.view (handled by middleware)
        try {
            $id = decrypt($encryptedId);
            $content = $this->contentService->findById($id);

            return view("admin.{$this->resource}.show", [
                'title'    => "{$this->resourceName} Details",
                'content'  => $content,
            ]);
        } catch (\Exception $e) {
            Log::error('Content show failed: ' . $e->getMessage());

            return redirect()->route('contents.index')
                ->with('error', "{$this->resourceName} not found.");
        }
    }

    /**
     * Update Status
     */
    public function updateStatus(StatusUpdateRequest $request, string $encryptedId)
    {
        // Permission: contents.update.status (handled by middleware)
        try {
            $id = decrypt($encryptedId);

            $this->contentService->updateStatus($id, $request->status);

            return $this->successResponse(
                [],
                'Status updated successfully.'
            );
        } catch (\Exception $e) {
            Log::error('Content status update failed: ' . $e->getMessage());

            return $this->errorResponse(
                'Failed to update status.',
                500
            );
        }
    }
}
