<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;

class SettingService
{
    /**
     * Get settings (single row)
     */
    public function get(): Setting
    {
        return Setting::firstOrCreate(['id' => 1]);
    }

    /**
     * Update settings
     */
    public function update(array $validated): Setting
    {
        return DB::transaction(function () use ($validated) {

            $settings = Setting::firstOrCreate(['id' => 1]);

            /* =======================
             * SITE LOGO
             * ======================= */
            if (
                isset($validated['site_logo']) &&
                $validated['site_logo'] instanceof UploadedFile
            ) {
                $validated['site_logo'] = imageUpload(
                    $validated['site_logo'],
                    'uploads/settings',
                    800,
                    800
                );
            } else {
                unset($validated['site_logo']);
            }

            /* =======================
             * FAVICON
             * ======================= */
            if (
                isset($validated['site_favicon']) &&
                $validated['site_favicon'] instanceof UploadedFile
            ) {
                $validated['site_favicon'] = imageUpload(
                    $validated['site_favicon'],
                    'uploads/settings',
                    64,
                    64
                );
            } else {
                unset($validated['site_favicon']);
            }

            /* =======================
             * CHECKBOX FIX
             * ======================= */
            $validated['maintenance_mode'] = $validated['maintenance_mode'] ?? false;

            $settings->update($validated);

            return $settings;
        });
    }
}
