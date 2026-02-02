<?php

namespace App\Services;

use App\Constants\Constant;
use App\Models\Blog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BlogService
{
    protected string $module = 'blogs';

    /**********************************************
     * DATATABLE LIST with Permission-Based Actions
     **********************************************/
    public function getDataForDataTable($request): array
    {
        $columns = [
            'id',
            'featured_image',
            'title',
            'slug',
            'status',
            'updated_at',
        ];

        $query = Blog::query();

        // Search
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
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
        $recordsTotal    = Blog::count();

        $blogs = $query
            ->skip($request->start)
            ->take($request->length)
            ->get();

        $data = $blogs->map(function ($blog, $index) use ($request) {
            // Check permissions for current user
            $canView = checkPermission('blogs.view');
            $canEdit = checkPermission('blogs.update');
            $canDelete = checkPermission('blogs.delete');
            $canUpdateStatus = checkPermission('blogs.update.status');

            // Title with link (only if can view)
            $titleHtml = ucfirst($blog->title);
            if ($canView) {
                $titleHtml = '<a href="' . route($this->module . '.show', encrypt($blog->id)) . '">' . ucfirst($blog->title) . '</a>';
            }

            // Status dropdown (only if can update status)
            $statusHtml = $blog->status
                ? '<span class="badge bg-label-success">Active</span>'
                : '<span class="badge bg-label-danger">Inactive</span>';

            if ($canUpdateStatus) {
                $statusHtml = status_dropdown($blog->status, [
                    'id'     => $blog->id,
                    'url'    => route($this->module . '.status', encrypt($blog->id)),
                    'method' => 'PUT',
                ]);
            }

            // Action buttons (only show buttons user has permission for)
            $actionButtons = [];

            if ($canEdit) {
                $actionButtons[] = btn_edit(route($this->module . '.edit', encrypt($blog->id)), false);
            }

            if ($canView) {
                $actionButtons[] = btn_view(route($this->module . '.show', encrypt($blog->id)), true);
            }

            if ($canDelete) {
                $actionButtons[] = btn_delete(route($this->module . '.delete', encrypt($blog->id)), true);
            }

            return [
                'id'          => $request->start + $index + 1,
                'image'       => image_show($blog->featured_image, 50, 50),
                'title'       => $titleHtml,
                'slug'        => $blog->slug,
                'status'      => $statusHtml,
                'updated_at'  => $blog->updated_at->format('d M Y'),
                'action'      => !empty($actionButtons) ? button_group($actionButtons) : 'No actions',
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
    public function findById(int $id): Blog
    {
        return Blog::findOrFail($id);
    }

    /**********************************************
     * CREATE
     **********************************************/
    public function createRecord(array $validated): Blog
    {
        return DB::transaction(function () use ($validated) {

            $data = [
                'title'             => $validated['title'],
                'slug'              => $this->generateUniqueSlug($validated['title']),
                'short_description' => $validated['short_description'] ?? null,
                'content'           => $validated['content'],
                'status'            => $validated['status'] ?? Constant::ACTIVE,
                'published_at'      => $validated['status']
                    ? now()
                    : null,
                'created_by'        => Auth::guard('admin')->id(),

                // SEO
                'meta_title'        => $validated['meta_title'] ?? null,
                'meta_description'  => $validated['meta_description'] ?? null,
                'meta_keywords'     => !empty($validated['meta_keywords'])
                    ? json_encode($validated['meta_keywords'])
                    : null,
            ];

            if (!empty($validated['featured_image'])) {
                $data['featured_image'] = imageUpload(
                    $validated['featured_image'],
                    'uploads/blogs',
                    800,
                    800
                );
            }

            return Blog::create($data);
        });
    }

    /**********************************************
     * UPDATE
     **********************************************/
    public function updateRecordById(int $id, array $validated): Blog
    {
        return DB::transaction(function () use ($id, $validated) {

            $blog = Blog::findOrFail($id);

            $data = [
                'title'             => $validated['title'],
                'short_description' => $validated['short_description'] ?? null,
                'content'           => $validated['content'],
                'status'            => $validated['status'] ?? Constant::ACTIVE,
                'published_at'      => $validated['status']
                    ? ($blog->published_at ?? now())
                    : null,

                // SEO
                'meta_title'        => $validated['meta_title'] ?? null,
                'meta_description'  => $validated['meta_description'] ?? null,
                'meta_keywords'     => !empty($validated['meta_keywords'])
                    ? json_encode($validated['meta_keywords'])
                    : null,
            ];

            if ($blog->title !== $validated['title']) {
                $data['slug'] = $this->generateUniqueSlug(
                    $validated['title'],
                    $blog->id
                );
            }

            if (!empty($validated['featured_image'])) {
                $this->deleteFile($blog->featured_image);
                $data['featured_image'] = imageUpload(
                    $validated['featured_image'],
                    'uploads/blogs',
                    800,
                    800
                );
            }

            $blog->update($data);

            return $blog;
        });
    }

    /**********************************************
     * DELETE
     **********************************************/
    public function deleteRecordById(int $id): bool
    {
        return DB::transaction(function () use ($id) {

            $blog = Blog::findOrFail($id);

            $this->deleteFile($blog->featured_image);

            return (bool) $blog->delete();
        });
    }

    /**********************************************
     * STATUS
     **********************************************/
    public function updateStatusById(int $id, int $status): Blog
    {
        $blog = Blog::findOrFail($id);

        $blog->update([
            'status'       => $status,
            'published_at' => $status
                ? ($blog->published_at ?? now())
                : null,
        ]);

        return $blog;
    }

    /**********************************************
     * HELPERS
     **********************************************/
    private function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = Str::slug($title);

        $count = Blog::where('slug', 'like', "{$slug}%")
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->count();

        return $count ? "{$slug}-{$count}" : $slug;
    }

    private function deleteFile(?string $path): void
    {
        if ($path && file_exists(public_path($path))) {
            unlink(public_path($path));
        }
    }
}
