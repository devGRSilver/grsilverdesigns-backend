<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ResponseController;
use App\Http\Requests\Admin\SettingRequest;
use App\Services\SettingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class SettingController extends ResponseController
{
    protected string $resource = 'settings';
    protected string $resourceName = 'Settings';

    protected SettingService $settingService;

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    /**
     * Settings Page
     */
    public function index()
    {
        $admin    = User::find(Auth::guard('admin')->id());
        $settings = $this->settingService->get();

        return view("admin.{$this->resource}.index", [
            'title'        => $this->resourceName,
            'resource'     => $this->resource,
            'resourceName' => $this->resourceName,
            'data'         => $admin,
            'settings'     => $settings,
        ]);
    }

    /**
     * Update Settings
     */
    public function update(SettingRequest $request)
    {
        try {
            $this->settingService->update($request->validated());

            return $this->successResponse(
                [],
                "{$this->resourceName} updated successfully."
            );
        } catch (\Exception $e) {

            Log::error('Settings update failed: ' . $e->getMessage());

            return $this->errorResponse(
                "Failed to update {$this->resourceName}.",
                500
            );
        }
    }
}
