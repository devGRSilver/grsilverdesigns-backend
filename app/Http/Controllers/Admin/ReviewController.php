<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ResponseController;
use App\Http\Requests\Admin\StatusUpdateRequest;
use App\Services\ReviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReviewController extends ResponseController
{
    protected string $resource = 'reviews';
    protected string $resourceName = 'Review';

    protected ReviewService $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    /**
     * Review List
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return response()->json(
                $this->reviewService->getDataForDataTable($request)
            );
        }

        return view("admin.{$this->resource}.index", [
            'title'        => "{$this->resourceName} List",
            'resource'     => $this->resource,
            'resourceName' => $this->resourceName,
        ]);
    }

    /**
     * Create Review Form
     */
    public function create()
    {
        return view("admin.{$this->resource}.add", [
            'title' => "Add {$this->resourceName}",
        ]);
    }





    /**
     * Show Review
     */
    public function show(string $encryptedId)
    {
        try {
            $id     = decrypt($encryptedId);
            $review = $this->reviewService->findById($id);

            return view("admin.{$this->resource}.show", [
                'title'  => "{$this->resourceName} Details",
                'review' => $review,
            ]);
        } catch (\Exception $e) {
            Log::error('Review show failed: ' . $e->getMessage());

            return redirect()->route('reviews.index')
                ->with('error', "{$this->resourceName} not found.");
        }
    }

    /**
     * Delete Review
     */
    public function delete(string $encryptedId)
    {
        try {
            $id = decrypt($encryptedId);

            $this->reviewService->deleteRecordById($id);

            return $this->successResponse(
                [],
                "{$this->resourceName} deleted successfully."
            );
        } catch (\Exception $e) {
            Log::error('Review delete failed: ' . $e->getMessage());

            return $this->errorResponse(
                "Failed to delete {$this->resourceName}.",
                500
            );
        }
    }

    /**
     * Update Review Status
     */
    public function updateStatus(StatusUpdateRequest $request, string $encryptedId)
    {
        try {
            $id = decrypt($encryptedId);

            $this->reviewService->updateStatusById($id, $request->status);

            return $this->successResponse([], 'Status updated successfully.');
        } catch (\Exception $e) {
            Log::error('Review status update failed: ' . $e->getMessage());

            return $this->errorResponse(
                'Failed to update status.',
                500
            );
        }
    }
}
