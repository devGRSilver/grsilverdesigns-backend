<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ResponseController;
use App\Http\Requests\Admin\BannerRequest;
use App\Http\Requests\Admin\StatusUpdateRequest;
use App\Models\Banner;
use App\Services\BannerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BannerController extends ResponseController
{
    protected string $resource = 'banners';
    protected string $resourceName = 'Banner';

    public function __construct(
        protected BannerService $bannerService
    ) {}

    /**
     * Display a listing of banners.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            return $this->bannerService->getDataForDataTable($request);
        }

        return view("admin.{$this->resource}.index", [
            'title'        => "{$this->resourceName} List",
            'resource'     => $this->resource,
            'resourceName' => $this->resourceName,
        ]);
    }

    /**
     * Show the form for creating a new banner.
     */
    public function create()
    {
        $types = config('banner.types', []);
        $groupKeys = config('banner.group_keys', []);

        return view("admin.{$this->resource}.add", [
            'title'     => "Create {$this->resourceName}",
            'types'     => $types,
            'groupKeys' => $groupKeys,
        ]);
    }

    /**
     * Store a newly created banner.
     */
    public function store(BannerRequest $request)
    {
        try {
            $banner = $this->bannerService->createRecord($request->validated());

            return $this->successResponse(
                ['id' => encrypt($banner->id)],
                "{$this->resourceName} created successfully.",
                route('banners.index')
            );
        } catch (\Exception $e) {
            Log::error('Banner creation failed: ' . $e->getMessage());

            return $this->errorResponse(
                'Failed to create banner. Please try again.',
                500
            );
        }
    }

    /**
     * Show the form for editing the specified banner.
     */
    public function edit(Banner $banner)
    {
        $types = config('banner.types', []);
        $groupKeys = config('banner.group_keys', []);

        return view("admin.{$this->resource}.edit", [
            'title'     => "Edit {$this->resourceName}",
            'banner'    => $banner,
            'types'     => $types,
            'groupKeys' => $groupKeys,
        ]);
    }

    /**
     * Update the specified banner.
     */
    public function update(BannerRequest $request, Banner $banner)
    {
        try {
            $this->bannerService->updateRecord($banner, $request->validated());

            return $this->successResponse(
                [],
                "{$this->resourceName} updated successfully.",
                route('banners.index')
            );
        } catch (\Exception $e) {
            Log::error('Banner update failed: ' . $e->getMessage());

            return $this->errorResponse(
                'Failed to update banner. Please try again.',
                500
            );
        }
    }

    /**
     * Display the specified banner.
     */
    public function show(Banner $banner)
    {
        return view("admin.{$this->resource}.show", [
            'title'  => "{$this->resourceName} Details",
            'banner' => $banner,
        ]);
    }

    /**
     * Remove the specified banner.
     */
    public function destroy(Banner $banner)
    {
        try {
            $this->bannerService->deleteRecord($banner);

            return $this->successResponse(
                [],
                "{$this->resourceName} deleted successfully."
            );
        } catch (\Exception $e) {
            Log::error('Banner deletion failed: ' . $e->getMessage());

            return $this->errorResponse(
                'Failed to delete banner. Please try again.',
                500
            );
        }
    }

    /**
     * Update banner status.
     */
    public function updateStatus(StatusUpdateRequest $request, Banner $banner)
    {
        try {
            $this->bannerService->updateStatus($banner, $request->validated('status'));

            return $this->successResponse(
                [],
                'Status updated successfully.'
            );
        } catch (\Exception $e) {
            Log::error('Banner status update failed: ' . $e->getMessage());

            return $this->errorResponse(
                'Failed to update status.',
                500
            );
        }
    }
}
