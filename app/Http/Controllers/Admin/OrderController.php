<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ResponseController;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends ResponseController
{
    protected string $resource = 'orders';
    protected string $resourceName = 'Order';

    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Orders List
     */
    public function index(Request $request)
    {
        // Optional: Additional permission check (middleware already handles it)
        // if (cannot('orders.view.any')) {
        //     abort(403, 'Unauthorized action.');
        // }

        if ($request->ajax()) {
            return response()->json(
                $this->orderService->getDataForDataTable($request)
            );
        }

        return view("admin.{$this->resource}.index", [
            'title'        => "{$this->resourceName} List",
            'resource'     => $this->resource,
            'resourceName' => $this->resourceName,
        ]);
    }

    /**
     * Create Order Form
     */
    public function create()
    {
        return view("admin.{$this->resource}.add", [
            'title' => "Create {$this->resourceName}",
        ]);
    }

    /**
     * Show Order Details
     */
    public function show(string $encryptedId)
    {
        try {
            $id = decrypt($encryptedId);
            $order = $this->orderService->findById($id);

            return view("admin.{$this->resource}.show", [
                'title' => "{$this->resourceName} Details",
                'order' => $order,
            ]);
        } catch (\Exception $e) {
            Log::error('Order show failed: ' . $e->getMessage());

            return redirect()
                ->route('orders.index')
                ->with('error', "{$this->resourceName} not found.");
        }
    }

    // Add these methods as you develop them:

    /**
     * Store Order
     */
    // public function store(Request $request)
    // {
    //     try {
    //         $validated = $request->validate([
    //             // validation rules
    //         ]);

    //         $this->orderService->createOrder($validated);

    //         return $this->successResponse([], "{$this->resourceName} created successfully.");
    //     } catch (\Exception $e) {
    //         Log::error('Order creation failed: ' . $e->getMessage());
    //         return $this->errorResponse('Failed to create order.', 500);
    //     }
    // }

    /**
 * Edit Order Form
 */
    // public function edit(string $encryptedId)
    // {
    //     try {
    //         $id = decrypt($encryptedId);
    //         $order = $this->orderService->findById($id);

    //         return view("admin.{$this->resource}.edit", [
    //             'title' => "Edit {$this->resourceName}",
    //             'order' => $order,
    //         ]);
    //     } catch (\Exception $e) {
    //         Log::error('Order edit failed: ' . $e->getMessage());
    //         return redirect()->route('orders.index')->with('error', 'Order not found.');
    //     }
    // }

    /**
 * Update Order
 */
    // public function update(Request $request, string $encryptedId)
    // {
    //     try {
    //         $id = decrypt($encryptedId);
    //         $validated = $request->validate([
    //             // validation rules
    //         ]);

    //         $this->orderService->updateOrder($id, $validated);

    //         return $this->successResponse([], "{$this->resourceName} updated successfully.");
    //     } catch (\Exception $e) {
    //         Log::error('Order update failed: ' . $e->getMessage());
    //         return $this->errorResponse('Failed to update order.', 500);
    //     }
    // }

    /**
 * Update Order Status
 */
    // public function updateStatus(Request $request, string $encryptedId)
    // {
    //     try {
    //         $id = decrypt($encryptedId);
    //         $status = $request->validate(['status' => 'required|integer']);

    //         $this->orderService->updateStatus($id, $status);

    //         return $this->successResponse([], "{$this->resourceName} status updated successfully.");
    //     } catch (\Exception $e) {
    //         Log::error('Order status update failed: ' . $e->getMessage());
    //         return $this->errorResponse('Failed to update order status.', 500);
    //     }
    // }

    /**
 * Delete Order
 */
    // public function delete(string $encryptedId)
    // {
    //     try {
    //         $id = decrypt($encryptedId);
    //         $this->orderService->deleteOrder($id);

    //         return $this->successResponse([], "{$this->resourceName} deleted successfully.");
    //     } catch (\Exception $e) {
    //         Log::error('Order deletion failed: ' . $e->getMessage());
    //         return $this->errorResponse('Failed to delete order.', 500);
    //     }
    // }
}
