<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ResponseController;
use App\Services\TransactionsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TransactionsController extends ResponseController
{
    protected string $resource = 'transactions';
    protected string $resourceName = 'Transaction';

    public function __construct(
        protected TransactionsService $transactionsService
    ) {}

    /**
     * Transactions List
     */
    public function index(Request $request)
    {
        // Permission: transactions.view.any (handled by middleware)
        if ($request->ajax()) {
            return response()->json(
                $this->transactionsService->getDataForDataTable($request)
            );
        }

        return view("admin.{$this->resource}.index", [
            'title'        => "{$this->resourceName} History",
            'resource'     => $this->resource,
            'resourceName' => $this->resourceName,
        ]);
    }

    /**
     * Show Transaction Details
     */
    public function show(string $encryptedId)
    {
        // Permission: transactions.view (handled by middleware)
        try {
            $id = decrypt($encryptedId);
            $transaction = $this->transactionsService->findById($id);

            return view("admin.{$this->resource}.show", [
                'title'       => "{$this->resourceName} Details",
                'transaction' => $transaction,
                'resource'    => $this->resource,
                'resourceName' => $this->resourceName,
            ]);
        } catch (\Exception $e) {
            Log::error('Transaction show failed: ' . $e->getMessage());

            return redirect()->route('transactions.index')
                ->with('error', "{$this->resourceName} not found.");
        }
    }

    /**
     * Export Transactions (optional - if needed)
     */
    public function export(Request $request)
    {
        // Permission: transactions.export (handled by middleware)
        try {
            $transactions = $this->transactionsService->getTransactionsForExport($request);

            // Return CSV or Excel file
            // Example: return Excel::download(new TransactionsExport($transactions), 'transactions.xlsx');

            return $this->successResponse(
                $transactions,
                'Transactions export started successfully.'
            );
        } catch (\Exception $e) {
            Log::error('Transactions export failed: ' . $e->getMessage());

            return $this->errorResponse(
                'Failed to export transactions.',
                500
            );
        }
    }

    /**
     * Update Transaction Status (optional - if needed)
     */
    public function updateStatus(Request $request, string $encryptedId)
    {
        // Permission: transactions.update.status (handled by middleware)
        try {
            $id = decrypt($encryptedId);
            $validated = $request->validate([
                'status' => 'required|in:pending,completed,failed,refunded',
            ]);

            $transaction = $this->transactionsService->updateStatusById($id, $validated['status']);

            return $this->successResponse(
                [],
                'Transaction status updated successfully.'
            );
        } catch (\Exception $e) {
            Log::error('Transaction status update failed: ' . $e->getMessage());

            return $this->errorResponse(
                'Failed to update transaction status.',
                500
            );
        }
    }

    /**
     * Refund Transaction (optional - if needed)
     */
    public function refund(string $encryptedId)
    {
        // Permission: transactions.refund (handled by middleware)
        try {
            $id = decrypt($encryptedId);
            $transaction = $this->transactionsService->refundTransaction($id);

            return $this->successResponse(
                [],
                'Transaction refunded successfully.'
            );
        } catch (\Exception $e) {
            Log::error('Transaction refund failed: ' . $e->getMessage());

            return $this->errorResponse(
                'Failed to refund transaction.',
                500
            );
        }
    }

    /**
     * Get Transaction Statistics (optional - for dashboard)
     */
    public function statistics()
    {
        // Permission: transactions.view.any (handled by middleware)
        try {
            $statistics = $this->transactionsService->getStatistics();

            return $this->successResponse(
                $statistics,
                'Transaction statistics fetched successfully.'
            );
        } catch (\Exception $e) {
            Log::error('Transaction statistics failed: ' . $e->getMessage());

            return $this->errorResponse(
                'Failed to fetch transaction statistics.',
                500
            );
        }
    }
}
