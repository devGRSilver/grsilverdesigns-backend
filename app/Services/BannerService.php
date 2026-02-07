<?php

namespace App\Services;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BannerService
{
    protected string $uploadPath = 'uploads/banners';

    /**
     * Get banners for DataTable
     */
    public function getDataForDataTable(Request $request): array
    {
        $query = Banner::query();

        // Search
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }



        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Total records
        $recordsTotal = Banner::count();
        $recordsFiltered = $query->count();

        // Ordering
        $orderCol = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');

        // Map column index to actual column name
        $columns = ['id', 'title', 'type', 'group_key', 'status', 'created_at', 'id'];
        $orderColumn = $columns[$orderCol] ?? 'id';

        $query->orderBy($orderColumn, $orderDir);

        // Pagination
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);

        $banners = $query
            ->skip($start)
            ->take($length)
            ->get();

        $data = $banners->map(function ($banner, $index) use ($start) {
            // Permissions
            $canEdit = checkPermission('banners.update');
            $canDelete = checkPermission('banners.delete');
            $canView = checkPermission('banners.view');
            $canUpdateStatus = checkPermission('banners.update.status');

            // Title with link
            $titleHtml = e($banner->title);


            // Image
            $imageHtml = image_show($banner->image_url, 60);

            // Status
            $statusHtml = $banner->status
                ? '<span class="badge bg-success">Active</span>'
                : '<span class="badge bg-danger">Inactive</span>';

            // Add status dropdown if user has permission
            if ($canUpdateStatus) {
                $statusHtml = status_dropdown($banner->status, [
                    'id' => $banner->id,
                    'url' => route('banners.status', $banner->id),
                    'method' => 'PUT',
                ]);
            }

            // Actions
            $actionButtons = [];
            if ($canEdit) {
                $actionButtons[] = btn_edit(route('banners.edit', $banner->id), true);
            }
            if ($canView) {
                $actionButtons[] = btn_view(route('banners.show', $banner->id), true);
            }
            if ($canDelete) {
                // Fixed: Changed from 'banners.show' to 'banners.delete'
                $actionButtons[] = btn_delete(route('banners.delete', $banner->id), true);
            }

            // IMPORTANT: The keys here must match your DataTable columns configuration
            return [
                'id' => $start + $index + 1, // Changed from 'id'
                'image' => $imageHtml,
                'title' => $titleHtml,
                'type' => $banner->type,
                'group_key' => $banner->group_key,
                'status' => $statusHtml,
                'created_at' => $banner->created_at->format('d M Y'),
                'action' => button_group($actionButtons),
            ];
        });

        return [
            'draw' => (int) $request->input('draw', 0),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }





    /**
     * Create new banner
     */
    public function createBanner(array $data): Banner
    {
        return DB::transaction(function () use ($data) {
            // Handle image upload
            if (isset($data['image_url']) && $data['image_url']) {
                $data['image_url'] = $this->uploadImage($data['image_url']);
            }

            $data['created_by'] = Auth::id();

            return Banner::create($data);
        });
    }

    /**
     * Update banner
     */
    public function updateBanner(Banner $banner, array $data): Banner
    {
        return DB::transaction(function () use ($banner, $data) {
            // Handle image upload
            if (isset($data['image_url']) && $data['image_url']) {
                // Delete old image
                if ($banner->image_url) {
                    Storage::delete('public/' . $banner->image_url);
                }
                $data['image_url'] = $this->uploadImage($data['image_url']);
            }

            $banner->update($data);

            return $banner;
        });
    }

    /**
     * Delete banner
     */
    public function deleteBanner(Banner $banner): bool
    {
        return DB::transaction(function () use ($banner) {
            // Delete image
            if ($banner->image_url) {
                Storage::delete('public/' . $banner->image_url);
            }

            return $banner->delete();
        });
    }

    /**
     * Update status
     */
    public function updateStatus(Banner $banner, int $status): bool
    {
        return $banner->update(['status' => $status]);
    }

    /**
     * Upload image
     */
    private function uploadImage($image): string
    {
        return $image->store($this->uploadPath, 'public');
    }

    /**
     * Get banner statistics
     */
    public function getStatistics(): array
    {
        return [
            'total' => Banner::count(),
            'active' => Banner::where('status', 1)->count(),
            'types' => Banner::distinct('type')->count('type'),
            'groups' => Banner::distinct('group_key')->count('group_key'),
        ];
    }
}
