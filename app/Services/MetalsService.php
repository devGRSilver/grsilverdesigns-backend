<?php

namespace App\Services;

use App\Models\Metal;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MetalsService
{
    protected string $module = 'metals';

    /**
     * Get metals list (cached)
     */
    public function getMetals()
    {
        return Cache::remember('metals:list', 3600, function () {
            return Metal::select('id', 'name', 'price_per_gram', 'currency')
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Get active metals
     */
    public function getActiveMetals()
    {
        return Metal::select('id', 'name')
            ->orderBy('name')
            ->get();
    }

    /**
     * Datatable Listing with Permission-Based Actions
     */
    public function getDataForDataTable($request): array
    {
        $columns = ['id', 'name', 'price_per_gram', 'updated_at'];

        /** BASE QUERY */
        $query = Metal::query();

        /** SEARCH */
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('price_per_gram', 'LIKE', "%{$search}%")
                    ->orWhere('currency', 'LIKE', "%{$search}%");
            });
        }



        /** FILTERED COUNT (before pagination) */
        $recordsFiltered = $query->count();

        /** ORDER */
        $orderColIndex = $request->input('order.0.column', 0);
        $orderCol = $columns[$orderColIndex] ?? 'id';
        $orderDir = $request->input('order.0.dir', 'asc');
        $query->orderBy($orderCol, $orderDir);

        /** PAGINATION */
        $records = $query
            ->skip($request->start)
            ->take($request->length)
            ->get();

        /** FORMAT RESPONSE with Permission Checks */
        $data = $records->map(function ($metal, $index) use ($request) {
            // Check permissions for current user
            $canUpdate = checkPermission('metals.update');
            $canViewCategories = checkPermission('metals.view.any');
            $canAssign = checkPermission('metals.assign');

            // Price display
            $priceHtml = $metal->currency . ' ' . number_format($metal->price_per_gram, 2);
            if ($canUpdate) {
                $priceHtml = '
                    <div class="d-flex align-items-center">
                        <span class="me-2">' . $metal->currency . ' ' . number_format($metal->price_per_gram, 2) . '</span>
                        <button type="button" 
                                class="btn btn-sm btn-icon btn-label-primary edit-price"
                                data-id="' . $metal->id . '"
                                data-price="' . $metal->price_per_gram . '"
                                data-name="' . e($metal->name) . '">
                            <i class="bx bx-edit"></i>
                        </button>
                    </div>';
            }

            // Categories count with link (if can view categories)
            $categoryCount = 0; // You can add: $metal->assignedCategories()->count()
            $categoryHtml = $categoryCount;

            if ($canViewCategories && $categoryCount > 0) {
                // Link to view assigned categories
                $categoryHtml = '<a href="' . route('metals.index') . '#metal-' . $metal->id . '" class="text-primary">' . $categoryCount . '</a>';
            }

            // Action buttons
            $actionButtons = [];

            if ($canAssign) {
                $actionButtons[] = '<a href="' . route('metals.assign', strtolower($metal->name)) . '" 
                    class="btn btn-sm btn-icon btn-label-success open_modal" title="Assign Categories">
                    <i class="bx bx-category"></i>
                </a>';
            }

            return [
                'id'             => $request->start + $index + 1,
                'name'           => '<span class="badge bg-label-primary">' . ucfirst($metal->name) . '</span>',
                'price_per_gram' => $priceHtml,
                'category_count' => $categoryHtml,
                'updated_at'     => $metal->updated_at->format('d M Y h:i a'),
                'action'         => !empty($actionButtons) ? button_group($actionButtons) : 'No actions',
            ];
        });

        return [
            'draw'            => intval($request->draw),
            'recordsTotal'    => Metal::count(),
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ];
    }

    /**
     * Find metal by ID
     */
    public function findById(int $id): ?Metal
    {
        try {
            return Metal::findOrFail($id);
        } catch (Exception $e) {
            Log::error("Metal not found. ID: {$id}, Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create metal record
     */
    public function createRecord(array $validated): Metal
    {
        DB::beginTransaction();
        try {
            $metal = Metal::create($validated);

            Cache::forget('metals:list');
            Cache::forget('metals:active');

            DB::commit();
            return $metal;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Metal creation failed: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update metal record by ID
     */
    public function updateRecordById(int $id, array $validated): Metal
    {
        DB::beginTransaction();
        try {
            $metal = Metal::findOrFail($id);
            $metal->update($validated);

            Cache::forget('metals:list');
            Cache::forget('metals:active');

            DB::commit();
            return $metal;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Metal update failed. ID: {$id}, Error: " . $e->getMessage());
            throw $e;
        }
    }



    /**
     * Delete metal record
     */
    public function deleteRecordById(int $id): bool
    {
        DB::beginTransaction();
        try {
            $metal = Metal::findOrFail($id);

            // Check if metal is used in products or categories before deleting
            $hasAssignments = $metal->assignedCategories()->exists();

            if ($hasAssignments) {
                throw new Exception('Cannot delete metal. It is assigned to categories.');
            }

            $metal->delete();

            Cache::forget('metals:list');
            Cache::forget('metals:active');

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Metal delete failed. ID: {$id}, Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get metal with assigned categories
     */
    public function getMetalWithCategories(int $metalId): ?Metal
    {
        try {
            return Metal::with(['assignedCategories.category', 'assignedCategories.subCategory'])
                ->findOrFail($metalId);
        } catch (Exception $e) {
            Log::error("Metal with categories not found. ID: {$metalId}, Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Update price with validation
     */
    public function updatePrice(int $id, float $price): Metal
    {
        if ($price < 0) {
            throw new Exception('Price cannot be negative.');
        }

        DB::beginTransaction();
        try {
            $metal = Metal::findOrFail($id);
            $metal->update(['price_per_gram' => $price]);

            Cache::forget('metals:list');
            Cache::forget('metals:active');

            DB::commit();
            return $metal;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Metal price update failed. ID: {$id}, Error: " . $e->getMessage());
            throw $e;
        }
    }
}
