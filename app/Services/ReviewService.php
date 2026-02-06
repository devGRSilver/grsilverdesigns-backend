<?php

namespace App\Services;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewService
{
    protected string $module = 'reviews';

    /**********************************************
     * DATATABLE LIST with Permission-Based Actions
     **********************************************/
    public function getDataForDataTable(Request $request): array
    {
        $columns = [
            'id',
            'user_id',
            'rating',
            'comment',
            'status',
            'updated_at',
        ];

        $query = Review::with('user');

        // Search
        if ($search = $request->input('search.value')) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('comment', 'like', "%{$search}%");
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Ordering
        $orderCol = $columns[$request->input('order.0.column')] ?? 'id';
        $orderDir = $request->input('order.0.dir', 'desc');
        $query->orderBy($orderCol, $orderDir);

        $recordsFiltered = $query->count();
        $recordsTotal    = Review::count();

        $reviews = $query
            ->skip($request->start)
            ->take($request->length)
            ->get();

        $data = $reviews->map(function ($review, $index) use ($request) {
            // Check permissions
            $canView = checkPermission('reviews.view');
            // $canEdit = checkPermission('reviews.update');
            $canDelete = checkPermission('reviews.delete');
            $canUpdateStatus = checkPermission('reviews.update.status');

            // Status dropdown - only interactive if user can update status
            $statusHtml = $review->status
                ? '<span class="badge bg-label-success">Published</span>'
                : '<span class="badge bg-label-danger">Unpublished</span>';

            if ($canUpdateStatus) {
                $statusHtml = status_custom_dropdown($review->status, [
                    'id'        => $review->id,
                    'url'       => route($this->module . '.status', encrypt($review->id)),
                    'method'    => 'PUT',
                    'level_one' => 'Published',
                    'level_two' => 'Unpublished',
                ]);
            }

            // Action buttons
            $actionButtons = [];

            // View button
            if ($canView) {
                $actionButtons[] = btn_view(route($this->module . '.show', encrypt($review->id)), true);
            }

            // Edit button
            // if ($canEdit) {
            //     $actionButtons[] = btn_edit(route($this->module . '.edit', encrypt($review->id)), true);
            // }

            // Delete button
            if ($canDelete) {
                $actionButtons[] = btn_delete(route($this->module . '.delete', encrypt($review->id)), true);
            }

            return [
                'id' => $request->start + $index + 1,
                'user' => $review->user
                    ? $review->user->name
                    : 'Guest',
                'rating' => view_rating($order->rating ?? 0),
                'comment' => str($review->comment)->limit(50),
                'status' => $statusHtml,
                'updated_at' => $review->updated_at->format('d M Y'),
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
     * FIND
     **********************************************/
    public function findById(int $id): Review
    {
        return Review::with('user')->findOrFail($id);
    }

    /**********************************************
     * CREATE
     **********************************************/
    public function createRecord(array $validated): Review
    {
        return DB::transaction(function () use ($validated) {
            return Review::create([
                'user_id'    => $validated['user_id'] ?? auth()->id(),
                'rating'     => $validated['rating'],
                'comment'    => $validated['comment'] ?? null,
                'status'     => $validated['status'] ?? false,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });
    }

    /**********************************************
     * UPDATE
     **********************************************/
    public function updateRecordById(int $id, array $validated): Review
    {
        return DB::transaction(function () use ($id, $validated) {
            $review = Review::findOrFail($id);

            $review->update([
                'rating'  => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
                'status'  => $validated['status'] ?? $review->status,
            ]);

            return $review;
        });
    }

    /**********************************************
     * DELETE
     **********************************************/
    public function deleteRecordById(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            return (bool) Review::findOrFail($id)->delete();
        });
    }

    /**********************************************
     * STATUS
     **********************************************/
    public function updateStatusById(int $id, bool $status): Review
    {
        $review = Review::findOrFail($id);

        $review->update([
            'status' => $status,
        ]);

        return $review;
    }
}
