<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ResponseController;
use App\Http\Requests\Admin\AttributeRequest;
use App\Http\Requests\Admin\AttributeValueRequest;
use App\Http\Requests\Admin\StatusUpdateRequest;
use App\Models\AttributeValue;
use App\Services\AttributeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AttributeController extends ResponseController
{
    protected string $resource = 'attributes';
    protected string $resourceName = 'Attribute';
    protected AttributeService $attributeService;

    public function __construct(AttributeService $attributeService)
    {
        $this->attributeService = $attributeService;
    }

    /**
     * Fetch Attribute Values (AJAX)
     */
    public function fetchAttributeValues($encryptedId)
    {
        try {
            $attributeId = $encryptedId; // Fixed: Added decrypt
            $values = AttributeValue::where('attribute_id', $attributeId)
                ->select('id', 'value')
                ->get();

            return $this->successResponse($values, 'Attribute values fetched.');
        } catch (\Exception $e) {
            Log::error($e);
            return $this->errorResponse('Failed to fetch attribute values.', 500);
        }
    }

    /**
     * List Attributes
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return response()->json(
                $this->attributeService->getDataForDataTable($request)
            );
        }

        return view("admin.{$this->resource}.index", [
            'title'        => "{$this->resourceName} List",
            'resource'     => $this->resource,
            'resourceName' => $this->resourceName,
        ]);
    }

    /**
     * Create Form
     */
    public function create()
    {
        return view("admin.{$this->resource}.add", [
            'title' => "Add {$this->resourceName}",
        ]);
    }

    /**
     * Add Attribute Value Form
     */
    public function addValue($encryptedId)
    {
        return view("admin.{$this->resource}.addValue", [
            'attribute_id' => $encryptedId,
            'title'        => "Add {$this->resourceName} Value",
        ]);
    }

    /**
     * Store Attribute
     */
    public function store(AttributeRequest $request)
    {
        try {
            $this->attributeService->createRecord($request->validated());
            return $this->successResponse([], "{$this->resourceName} created successfully.");
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->errorResponse('Failed to create attribute.', 500);
        }
    }

    /**
     * Store Attribute Value
     */
    public function storeValue(AttributeValueRequest $request, string $encryptedId)
    {
        try {
            $attributeId = decrypt($encryptedId);

            $this->attributeService->createAttributeValue(
                $attributeId,
                $request->validated()
            );

            return $this->successResponse(
                [],
                "{$this->resourceName} value created successfully."
            );
        } catch (\Exception $e) {
            Log::error('Attribute value creation failed: ' . $e->getMessage());

            return $this->errorResponse(
                'Failed to create attribute value.',
                500
            );
        }
    }

    /**
     * Delete Attribute
     */
    public function delete($encryptedId)
    {
        try {
            $id = decrypt($encryptedId);
            $this->attributeService->deleteRecordById($id);

            return $this->successResponse([], "{$this->resourceName} deleted successfully.");
        } catch (\Exception $e) {
            Log::error($e);
            return $this->errorResponse("Failed to delete {$this->resourceName}.", 500);
        }
    }

    /**
     * Delete Attribute Value
     */
    public function deleteValue($encryptedId)
    {
        try {
            $id = decrypt($encryptedId);
            $this->attributeService->deleteAttributeValue($id);

            return $this->successResponse([], "{$this->resourceName} value deleted successfully.");
        } catch (\Exception $e) {
            Log::error($e);
            return $this->errorResponse("Failed to delete {$this->resourceName} value.", 500);
        }
    }

    /**
     * Update Status
     */
    public function updateStatus(StatusUpdateRequest $request, $encryptedId)
    {
        try {
            $id = decrypt($encryptedId);
            $this->attributeService->updateStatusById($id, $request->status);
            return $this->successResponse([], 'Status updated successfully.');
        } catch (\Exception $e) {
            Log::error($e);
            return $this->errorResponse('Failed to update status.', 500);
        }
    }
}
