<?php

namespace App\Services;

use App\Constants\Constant;
use App\Models\Newsletter;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NewsletterService
{
    protected string $module = 'newsletters';

    /**********************************************
     * DataTable with Permission-Based UI
     **********************************************/
    public function getDataForDataTable($request): array
    {
        $columns = [
            'id',
            'email',
            'name',
            'status',
            'subscribed_at'
        ];

        $baseQuery = Newsletter::query();

        /** SEARCH */
        if ($search = $request->input('search.value')) {
            $baseQuery->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        /** FILTER STATUS */
        if ($request->filled('status')) {
            $baseQuery->where('status', $request->status);
        }

        /** DATE RANGE FILTER (optional) */
        if ($range = $request->input('date_range')) {
            [$start, $end] = array_pad(explode(' to ', $range), 2, $range);
            $baseQuery->whereBetween('created_at', [
                "$start 00:00:00",
                "$end 23:59:59",
            ]);
        }

        $recordsFiltered = (clone $baseQuery)->count();
        $recordsTotal    = Newsletter::count();

        /** ORDER */
        $orderCol = $columns[$request->input('order.0.column')] ?? 'id';
        $orderDir = $request->input('order.0.dir', 'desc');

        $newsletters = $baseQuery
            ->orderBy($orderCol, $orderDir)
            ->skip($request->start)
            ->take($request->length)
            ->get();

        $data = $newsletters->map(function ($newsletter, $index) use ($request) {
            // Check permissions for current user
            $canUpdateStatus = Auth::guard('admin')->user()->can('newsletters.update.status');
            $canDelete = Auth::guard('admin')->user()->can('newsletters.delete');
            $canExport = Auth::guard('admin')->user()->can('newsletters.export');

            // Status dropdown (only if can update status)
            $statusHtml = $newsletter->status
                ? '<span class="badge bg-label-success">Subscribed</span>'
                : '<span class="badge bg-label-danger">Unsubscribed</span>';

            if ($canUpdateStatus) {
                $statusHtml = status_dropdown($newsletter->status, [
                    'id'     => $newsletter->id,
                    'url'    => route($this->module . '.status', encrypt($newsletter->id)),
                    'method' => 'PUT',
                ]);
            }

            // Email with copy button
            $emailHtml = '<div class="d-flex align-items-center gap-2">
                            <span>' . e($newsletter->email) . '</span>
                            <button class="btn btn-sm btn-icon btn-label-secondary copy-email" 
                                    data-email="' . e($newsletter->email) . '"
                                    title="Copy email">
                                <i class="bx bx-copy"></i>
                            </button>
                          </div>';

            // Action buttons
            $actionButtons = [];

            if ($canDelete) {
                $actionButtons[] = '<button class="btn btn-sm btn-icon btn-label-danger delete-newsletter" 
                    data-id="' . encrypt($newsletter->id) . '"
                    data-email="' . e($newsletter->email) . '"
                    title="Delete">
                    <i class="bx bx-trash"></i>
                </button>';
            }

            if ($canExport) {
                $actionButtons[] = '<button class="btn btn-sm btn-icon btn-label-info export-single" 
                    data-id="' . $newsletter->id . '"
                    data-email="' . e($newsletter->email) . '"
                    title="Export">
                    <i class="bx bx-download"></i>
                </button>';
            }

            return [
                'id'            => $request->start + $index + 1,
                'email'         => $emailHtml,
                'name'          => $newsletter->name ?? '<span class="text-muted">-</span>',
                'status'        => $statusHtml,
                'subscribed_at' => optional($newsletter->subscribed_at)->format('d F Y'),
                'action'        => !empty($actionButtons) ? button_group($actionButtons) : 'No actions',
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
     * Status Update (Subscribe / Unsubscribe)
     **********************************************/
    public function updateStatusById(int $id, string $status): Newsletter
    {
        DB::beginTransaction();
        try {
            $newsletter = Newsletter::findOrFail($id);

            if ($status == Constant::IN_ACTIVE) {
                $newsletter->update([
                    'status'          => Constant::IN_ACTIVE,
                    'unsubscribed_at' => now(),
                ]);
            } else {
                $newsletter->update([
                    'status'          => Constant::ACTIVE,
                    'unsubscribed_at' => null,
                    'subscribed_at'   => $newsletter->subscribed_at ?? now(),
                ]);
            }

            DB::commit();
            return $newsletter;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Newsletter status update failed. ID {$id}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**********************************************
     * Find Newsletter by ID
     **********************************************/
    public function findById(int $id): Newsletter
    {
        return Newsletter::findOrFail($id);
    }

    /**********************************************
     * Delete Newsletter
     **********************************************/
    public function deleteRecordById(int $id): bool
    {
        DB::beginTransaction();
        try {
            $newsletter = Newsletter::findOrFail($id);
            $newsletter->delete();

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Newsletter delete failed. ID {$id}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**********************************************
     * Get All Subscribers for Export
     **********************************************/
    public function getAllSubscribers()
    {
        return Newsletter::select('email', 'name', 'status', 'subscribed_at', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**********************************************
     * Get Subscriber Statistics
     **********************************************/
    public function getStatistics(): array
    {
        $total = Newsletter::count();
        $subscribed = Newsletter::where('status', Constant::ACTIVE)->count();
        $unsubscribed = Newsletter::where('status', Constant::IN_ACTIVE)->count();

        // Recent subscribers (last 30 days)
        $recent = Newsletter::where('created_at', '>=', now()->subDays(30))
            ->where('status', Constant::ACTIVE)
            ->count();

        return [
            'total' => $total,
            'subscribed' => $subscribed,
            'unsubscribed' => $unsubscribed,
            'recent' => $recent,
            'subscribed_percentage' => $total > 0 ? round(($subscribed / $total) * 100, 2) : 0,
        ];
    }

    /**********************************************
     * Bulk Update Status
     **********************************************/
    public function bulkUpdateStatus(array $ids, string $status): int
    {
        DB::beginTransaction();
        try {
            $updateData = ['status' => $status];

            if ($status == Constant::IN_ACTIVE) {
                $updateData['unsubscribed_at'] = now();
            } else {
                $updateData['unsubscribed_at'] = null;
            }

            $updated = Newsletter::whereIn('id', $ids)->update($updateData);

            DB::commit();
            return $updated;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Newsletter bulk status update failed: {$e->getMessage()}");
            throw $e;
        }
    }
}
