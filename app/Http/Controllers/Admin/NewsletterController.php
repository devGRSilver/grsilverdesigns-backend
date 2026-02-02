<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ResponseController;
use App\Http\Requests\Admin\NewsletterRequest;
use App\Http\Requests\Admin\StatusUpdateRequest;
use App\Services\NewsletterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NewsletterController extends ResponseController
{
    protected string $resource = 'newsletters';
    protected string $resourceName = 'Newsletter';

    public function __construct(
        protected NewsletterService $newsletterService
    ) {}

    /**
     * Newsletter List
     */
    public function index(Request $request)
    {
        // Permission: newsletters.view.any (handled by middleware)
        if ($request->ajax()) {
            return response()->json(
                $this->newsletterService->getDataForDataTable($request)
            );
        }

        return view("admin.{$this->resource}.index", [
            'title'        => "{$this->resourceName} Subscribers",
            'resource'     => $this->resource,
            'resourceName' => $this->resourceName,
        ]);
    }

    /**
     * Update Status (Subscribe/Unsubscribe)
     */
    public function updateStatus(StatusUpdateRequest $request, string $encryptedId)
    {
        // Permission: newsletters.update.status (handled by middleware)
        try {
            $id = decrypt($encryptedId);

            $this->newsletterService->updateStatusById($id, $request->status);

            return $this->successResponse(
                [],
                'Newsletter status updated successfully.'
            );
        } catch (\Exception $e) {
            Log::error('Newsletter status update failed: ' . $e->getMessage());

            return $this->errorResponse(
                'Failed to update newsletter status.',
                500
            );
        }
    }

    /**
     * Show Newsletter Subscriber Details (optional - if needed)
     */
    public function show(string $encryptedId)
    {
        // Permission: newsletters.view (handled by middleware)
        try {
            $id = decrypt($encryptedId);
            $subscriber = $this->newsletterService->findById($id);

            return view("admin.{$this->resource}.show", [
                'title'      => "Subscriber Details",
                'subscriber' => $subscriber,
            ]);
        } catch (\Exception $e) {
            Log::error('Newsletter show failed: ' . $e->getMessage());

            return redirect()->route('newsletters.index')
                ->with('error', 'Subscriber not found.');
        }
    }

    /**
     * Delete Newsletter Subscriber (optional - if needed)
     */
    public function delete(string $encryptedId)
    {
        // Permission: newsletters.delete (handled by middleware)
        try {
            $id = decrypt($encryptedId);
            $this->newsletterService->deleteRecordById($id);

            return $this->successResponse(
                [],
                'Subscriber deleted successfully.'
            );
        } catch (\Exception $e) {
            Log::error('Newsletter delete failed: ' . $e->getMessage());

            return $this->errorResponse(
                'Failed to delete subscriber.',
                500
            );
        }
    }

    /**
     * Export Subscribers (optional - if needed)
     */
    public function export(Request $request)
    {
        // Permission: newsletters.export (handled by middleware)
        try {
            $subscribers = $this->newsletterService->getAllSubscribers();

            // Return CSV or Excel file
            // Example: return Excel::download(new NewsletterExport($subscribers), 'subscribers.xlsx');

            return $this->successResponse(
                $subscribers,
                'Export started successfully.'
            );
        } catch (\Exception $e) {
            Log::error('Newsletter export failed: ' . $e->getMessage());

            return $this->errorResponse(
                'Failed to export subscribers.',
                500
            );
        }
    }
}
