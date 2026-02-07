<?php

namespace App\Services;

use App\Models\Attribute;
use App\Models\AttributeValue;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AttributeService
{
    protected $module = 'attributes';

    /**********************************************
     * Get All Attributes (cached)
     **********************************************/
    public function getAllAttributes()
    {
        return Attribute::select('id', 'name', 'slug', 'type', 'status')
            ->orderBy('name', 'asc')
            ->get();
    }

    /**********************************************
     * Get Active Attributes (cached)
     **********************************************/
    public function getActiveAttributes()
    {
        return Attribute::active()
            ->select('id', 'name', 'slug', 'type')
            ->orderBy('name', 'asc')
            ->get();
    }

    /**********************************************
     * Find Attribute by ID
     **********************************************/
    public function findById($id)
    {
        return Attribute::findOrFail($id);
    }

    /**********************************************
     * Create Attribute
     **********************************************/
    public function createRecord($validated)
    {
        DB::beginTransaction();
        try {
            $data = [
                'name'   => $validated['name'],
                'slug'   => Str::slug($validated['name']),
                'type'   => $validated['type'],
                'status' => $validated['status'] ?? true,
            ];
            $attribute = Attribute::create($data);

            DB::commit();
            return $attribute;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Attribute creation failed: " . $e->getMessage());
            throw $e;
        }
    }

    /**********************************************
     * Create Attribute Value
     **********************************************/
    public function createAttributeValue(int $attribute_id, array $validated)
    {
        DB::beginTransaction();
        try {
            $nextSortOrder = AttributeValue::where('attribute_id', $attribute_id)->max('sort_order');

            $data = [
                'attribute_id' => $attribute_id,
                'value'        => $validated['name'],
                'sort_order'   => ($nextSortOrder ?? 0) + 1,
            ];

            $attributeValue = AttributeValue::create($data);
            DB::commit();
            return $attributeValue;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('AttributeValue creation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**********************************************
     * Update Attribute by ID
     **********************************************/
    public function updateRecordById($id, array $validated)
    {
        DB::beginTransaction();
        try {
            $attribute = Attribute::findOrFail($id);

            $data = [
                'name'   => $validated['name'],
                'type'   => $validated['type'],
                'status' => $validated['status'] ?? true,
            ];

            // Update slug if name changed
            if ($attribute->name !== $validated['name']) {
                $data['slug'] = Str::slug($validated['name']);
            }

            $attribute->update($data);

            DB::commit();
            return $attribute;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Attribute update failed. ID: {$id}. Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**********************************************
     * Delete Attribute by ID
     **********************************************/
    public function deleteRecordById($id)
    {
        DB::beginTransaction();
        try {
            $attribute = Attribute::findOrFail($id);
            $attribute->delete();

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Attribute delete failed. ID: {$id}. Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**********************************************
     * Delete Attribute Value by ID
     **********************************************/
    public function deleteAttributeValue($id)
    {
        DB::beginTransaction();
        try {
            $attributeValue = AttributeValue::findOrFail($id);
            $attributeValue->delete();

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("AttributeValue delete failed. ID: {$id}. Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**********************************************
     * Update Status by ID
     **********************************************/
    public function updateStatusById($id, $status)
    {
        DB::beginTransaction();
        try {
            $attribute = Attribute::findOrFail($id);
            $attribute->status = $status;
            $attribute->save();

            DB::commit();
            return $attribute;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Attribute status update failed. ID: {$id}. Error: " . $e->getMessage());
            throw $e;
        }
    }



    /**********************************************
     * Datatable List with permission-based actions
     **********************************************/
    public function getDataForDataTable($request)
    {
        $columns = ['id', 'name', 'slug', 'status', 'created_at'];

        $query = Attribute::with('values');

        // SEARCH
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('slug', 'LIKE', "%{$search}%");
            });
        }

        // STATUS FILTER
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // ORDERING
        $orderIndex = $request->input('order.0.column', 0);
        $orderCol   = $columns[$orderIndex] ?? 'id';
        $orderDir   = $request->input('order.0.dir', 'asc');

        $query->orderBy($orderCol, $orderDir);

        // PAGINATION
        $datas = $query->skip($request->start)->take($request->length)->get();

        $data = $datas->map(function ($attribute, $index) use ($request) {
            // Check permissions
            $canViewValues = checkPermission('attributes.view.any');
            $canCreateValues = checkPermission('attributes.values.create');
            $canDeleteValues = checkPermission('attributes.values.delete');
            $canUpdateStatus = checkPermission('attributes.update.status');
            $canDelete = checkPermission('attributes.delete');

            // Attribute value badges (only show if can view)
            $badges = '';
            if ($canViewValues) {
                $badges = $attribute->values->map(function ($item) use ($canDeleteValues) {
                    $badgeHtml = '<span class="badge badge-secondary me-1 mb-1 d-inline-flex align-items-center">'
                        . e($item->value);

                    if ($canDeleteValues) {
                        $deleteUrl = route('attributes.values.delete', encrypt($item->id));
                        $badgeHtml .= '<a href="' . $deleteUrl . '" class="ms-1 text-danger delete-attribute-value delete_record">&times;</a>';
                    }

                    $badgeHtml .= '</span>';
                    return $badgeHtml;
                })->implode(' ');
            }

            // Add new value badge (only show if can create)
            $addNewBadge = '';
            if ($canCreateValues) {
                $addUrl = route('attributes.values.create', encrypt($attribute->id));
                $addNewBadge = '<a href="' . $addUrl . '" class="badge bg-label-success me-1 mb-1 modal_open">+ Add New Value</a>';
            }

            // Status dropdown (only show if can update)
            $statusHtml = $attribute->status
                ? '<span class="badge bg-label-success">Active</span>'
                : '<span class="badge bg-label-danger">Inactive</span>';

            if ($canUpdateStatus) {
                $statusHtml = status_dropdown($attribute->status, [
                    'id'     => $attribute->id,
                    'url'    => route($this->module . '.status', encrypt($attribute->id)),
                    'method' => 'PUT',
                ]);
            }

            // Action buttons
            $actionButtons = [];

            // Delete button
            if ($canDelete) {
                $actionButtons[] = btn_delete(route($this->module . '.delete', encrypt($attribute->id)));
            }

            return [
                'id'              => $request->start + $index + 1,
                'name'            => ucfirst($attribute->name),
                'slug'            => $attribute->slug,
                'status'          => $statusHtml,
                'attribute_value' => $badges . $addNewBadge,
                'created_at'      => $attribute->created_at->format('d M Y'),
                'action'          => !empty($actionButtons) ? button_group($actionButtons) : 'No actions',
            ];
        });

        return [
            'draw'            => intval($request->draw),
            'recordsTotal'    => Attribute::count(),
            'recordsFiltered' => $query->count(),
            'data'            => $data,
        ];
    }
}
