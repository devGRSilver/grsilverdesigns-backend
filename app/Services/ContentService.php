<?php

namespace App\Services;

use App\Constants\Constant;
use App\Models\Content;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ContentService
{
    protected string $module = 'contents';

    /**********************************************
     * DataTable with Permission-Based UI
     **********************************************/
    public function getDataForDataTable($request): array
    {
        $columns = [
            'id',
            'title',
            'slug',
            'type',
            'status',
            'created_at',
        ];

        $query = Content::query();

        /* ---------- SEARCH ---------- */
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('meta_title', 'like', "%{$search}%");
            });
        }

        /* ---------- FILTER ---------- */
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        /* ---------- COUNTS ---------- */
        $recordsFiltered = (clone $query)->count();
        $recordsTotal    = Content::count();

        /* ---------- ORDER ---------- */
        $orderIndex = (int) $request->input('order.0.column', 0);
        $orderCol   = $columns[$orderIndex] ?? 'id';
        $orderDir   = $request->input('order.0.dir', 'desc');

        $query->orderBy($orderCol, $orderDir);

        /* ---------- PAGINATION (FIXED) ---------- */
        $start  = max((int) $request->input('start', 0), 0);
        $length = (int) $request->input('length', 10);

        // DataTables sends -1 for "Show all"
        if ($length === -1) {
            $length = 1000; // safe upper limit
        }

        $contents = $query
            ->limit($length)
            ->offset($start)
            ->get();

        /* ---------- DATA ---------- */
        $data = $contents->values()->map(function ($content, $index) use ($start) {

            $canView         = checkPermission('contents.view');
            $canEdit         = checkPermission('contents.update');
            $canUpdateStatus = checkPermission('contents.update.status');

            /* Title */
            $title = e(ucfirst($content->title));
            $titleHtml = $canView
                ? '<a href="' . route($this->module . '.show', encrypt($content->id)) . '">' . $title . '</a>'
                : $title;

            /* Status */
            $statusHtml = $content->status
                ? '<span class="badge bg-label-success">Active</span>'
                : '<span class="badge bg-label-danger">Inactive</span>';

            if ($canUpdateStatus) {
                $statusHtml = status_dropdown($content->status, [
                    'id'     => $content->id,
                    'url'    => route($this->module . '.status', encrypt($content->id)),
                    'method' => 'PUT',
                ]);
            }

            /* Type */
            $typeHtml = '<span class="badge bg-label-info">' . ucfirst($content->type ?? '-') . '</span>';

            /* Actions */
            $actions = [];

            if ($canView) {
                $actions[] = btn_view(route($this->module . '.show', encrypt($content->id)), true);
            }

            if ($canEdit) {
                $actions[] = btn_edit(route($this->module . '.edit', encrypt($content->id)), false);
            }

            return [
                'id'         => $start + $index + 1,
                'title'      => $titleHtml,
                'slug'       => e($content->slug),
                'image'      => image_show($content->image, 70),
                'type'       => $typeHtml,
                'status'     => $statusHtml,
                'updated_at' => $content->updated_at->format('d F Y'),
                'action'     => $actions ? button_group($actions) : 'No actions',
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
     * Find Content
     **********************************************/
    public function findById(int $id): Content
    {
        return Content::findOrFail($id);
    }

    /**********************************************
     * Create / Update
     **********************************************/
    public function createRecord(array $validated): Content
    {
        return $this->saveContent(null, $validated);
    }

    public function updateRecordById(int $id, array $validated): Content
    {
        return $this->saveContent($id, $validated);
    }

    /**********************************************
     * Delete Content
     **********************************************/
    public function deleteRecordById(int $id): bool
    {
        DB::beginTransaction();

        try {
            Content::findOrFail($id)->delete();

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Content delete failed. ID {$id}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**********************************************
     * Status Update
     **********************************************/
    public function updateStatus(int $id, bool $status): Content
    {
        DB::beginTransaction();

        try {
            $content = Content::findOrFail($id);
            $content->update(['status' => $status]);

            DB::commit();
            return $content;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Content status update failed. ID {$id}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**********************************************
     * Save Content
     **********************************************/
    private function saveContent(?int $id, array $validated): Content
    {
        DB::beginTransaction();

        try {
            $content = $id ? Content::findOrFail($id) : new Content();

            $oldTitle = $content->title;

            $content->fill([
                'title'            => $validated['title'],
                'type'             => $validated['type'] ?? 'page',
                'description'      => $validated['description'] ?? null,
                'meta_title'       => $validated['meta_title'] ?? null,
                'meta_description' => $validated['meta_description'] ?? null,
                'meta_keywords'    => $validated['meta_keywords'] ?? null,
                'status'           => $validated['status'] ?? Constant::ACTIVE,
            ]);

            if (!empty($validated['image'])) {
                // Delete old image if exists
                if ($content->image && file_exists(public_path($content->image))) {
                    unlink(public_path($content->image));
                }
                $content->image = imageUpload($validated['image'], 'uploads/content', 1600, 1600);
            }

            if (!$id || $oldTitle !== $validated['title']) {
                $content->slug = $this->generateUniqueSlug(
                    Str::slug($validated['title']),
                    $id
                );
            }

            $content->save();

            DB::commit();
            return $content;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Content save failed: {$e->getMessage()}");
            throw $e;
        }
    }

    /**********************************************
     * Helpers
     **********************************************/
    private function generateUniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $original = $slug;
        $count = 1;

        while (
            Content::where('slug', $slug)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = "{$original}-{$count}";
            $count++;
        }

        return $slug;
    }
}
