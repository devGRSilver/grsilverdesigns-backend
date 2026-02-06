<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Enums\OrderStatus;

class OrderService
{
    protected string $module = 'orders';

    /**********************************************
     * DATATABLE LIST with Permission-Based Actions
     **********************************************/
    public function getDataForDataTable(Request $request): array
    {
        $columns = [
            'id',
            'order_number',
            'user_id',
            'status',
            'grand_total',
            'payment_status',
            'created_at',
        ];

        $query = Order::with('user')->withCount('items');

        /** SEARCH */
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        /** DATE FILTER */
        if ($range = $request->input('date_range')) {
            [$start, $end] = array_pad(explode(' to ', $range), 2, $range);
            $query->whereBetween('created_at', [
                "$start 00:00:00",
                "$end 23:59:59",
            ]);
        }

        if ($user_id = $request->input('user_id')) {
            $query->where('user_id', $user_id);
        }


        /** STATUS FILTER */
        if ($request->filled('status')) {
            if (in_array($request->status, array_map(fn($s) => $s->value, OrderStatus::cases()))) {
                $query->where('status', $request->status);
            }
        }

        /** ORDERING */
        $orderCol = $columns[$request->input('order.0.column')] ?? 'id';
        $orderDir = $request->input('order.0.dir', 'desc');
        $query->orderBy($orderCol, $orderDir);

        $recordsFiltered = $query->count();

        $totalQuery = Order::query();
        if ($request->filled('user_id')) {
            $totalQuery->where('user_id', $request->user_id);
        }
        $recordsTotal = $totalQuery->count();



        $orders = $query
            ->skip($request->start)
            ->take($request->length)
            ->get();

        /** MAP DATA */
        $data = $orders->map(function ($order, $index) use ($request) {
            // Check permissions
            $canView = checkPermission('orders.view');
            $canEdit = checkPermission('orders.update');
            $canDelete = checkPermission('orders.delete');
            $canUpdateStatus = checkPermission('orders.update.status');
            $canPrint = checkPermission('orders.print');
            $canExport = checkPermission('orders.export');

            // Order number link - only link if user can view
            $orderNumberHtml = $order->order_number ?? '—';
            if ($canView) {
                $orderNumberHtml = redirect_to_link(
                    route('orders.show', encrypt($order->id)),
                    $orderNumberHtml
                );
            }

            // Customer link - only link if user can view users
            $customerHtml = $order->user?->name ?? 'Guest';
            if (checkPermission('users.view') && $order->user_id) {
                $customerHtml = redirect_to_link(
                    route('users.show', encrypt($order->user_id)),
                    $customerHtml
                );
            }

            // Status - make interactive if user can update status
            $statusHtml = view_order_status($order->status ?? 'unknown');
            // if ($canUpdateStatus) {
            //     $statusHtml = status_dropdown($order->status, [
            //         'id'     => $order->id,
            //         'url'    => route('orders.status', encrypt($order->id)),
            //         'method' => 'PUT',
            //     ]);
            // }

            // Action buttons
            $actionButtons = [];

            // View button
            if ($canView) {
                $actionButtons[] = btn_view(route('orders.show', encrypt($order->id)));
            }


            // Print button
            if ($canPrint) {
                $actionButtons[] = '<a href="#" 
                    class="btn btn-sm btn-icon btn-label-secondary" title="Print Invoice" target="_blank">
                    <i class="bx bx-printer"></i>
                </a>';
            }

            // Export button (if individual export)
            if ($canExport) {
                $actionButtons[] = '<a href="#" 
                    class="btn btn-sm btn-icon btn-label-info" title="Export Order">
                    <i class="bx bx-download"></i>
                </a>';
            }


            return [
                'id'           => $request->start + $index + 1,
                'order_number' => $orderNumberHtml,
                'customer'     => $customerHtml,
                'items_count'  => $order->items_count,
                'amount'       => number_format($order->grand_total ?? 0, 2) . ' ' . strtoupper($order->currency_code ?? '$'),
                'status'       => $statusHtml,
                'rating'       => view_rating($order->rating ?? 0), // Replace with actual rating
                'created_at'   => $order->created_at?->format('d M Y') ?? '—',
                'action'       => !empty($actionButtons) ? button_group($actionButtons) : 'No actions',
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
     * FIND ORDER BY ID
     **********************************************/
    public function findById(int $id): Order
    {
        return Order::with(['user', 'items', 'shipping_address', 'billing_address'])->findOrFail($id);
    }

    /**********************************************
     * HELPER: Get Payment Status Badge
     **********************************************/
    private function getPaymentStatusBadge(string $status): string
    {
        $badges = [
            'paid'      => '<span class="badge bg-label-success">Paid</span>',
            'pending'   => '<span class="badge bg-label-warning">Pending</span>',
            'failed'    => '<span class="badge bg-label-danger">Failed</span>',
            'refunded'  => '<span class="badge bg-label-info">Refunded</span>',
            'cancelled' => '<span class="badge bg-label-secondary">Cancelled</span>',
        ];

        return $badges[$status] ?? '<span class="badge bg-label-secondary">' . ucfirst($status) . '</span>';
    }

    /**********************************************
     * UPDATE ORDER STATUS
     **********************************************/
    public function updateStatus(int $id, string $status): Order
    {
        DB::beginTransaction();
        try {
            $order = Order::findOrFail($id);

            // Validate status
            if (!in_array($status, array_map(fn($s) => $s->value, OrderStatus::cases()))) {
                throw new \InvalidArgumentException("Invalid order status: {$status}");
            }

            $order->status = $status;
            $order->save();

            // You can add status history logging here
            // $this->addStatusHistory($order, $status);

            DB::commit();
            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**********************************************
     * DELETE ORDER
     **********************************************/
    public function deleteOrder(int $id): bool
    {
        DB::beginTransaction();
        try {
            $order = Order::findOrFail($id);

            // Check if order can be deleted (e.g., not completed/delivered)
            if (in_array($order->status, ['completed', 'delivered', 'shipped'])) {
                throw new \Exception("Cannot delete {$order->status} order.");
            }

            $order->delete();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
