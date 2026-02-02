<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionsService
{
    protected string $module = 'transactions';

    /**********************************************
     * DATATABLE LIST with Permission-Based UI
     **********************************************/
    public function getDataForDataTable(Request $request): array
    {
        $columns = [
            'id',
            'user_id',
            'order_id',
            'transaction_id',
            'amount',
            'currency_code',
            'status',
            'updated_at',
        ];

        $query = Transaction::with(['user', 'order']);


        if ($user_id = $request->input('user_id')) {
            $query->where('user_id', $user_id);
        }





        /* ---------------- SEARCH ---------------- */
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                    ->orWhere('gateway_transaction_id', 'like', "%{$search}%")
                    ->orWhere('payment_gateway', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('order', function ($oq) use ($search) {
                        $oq->where('order_number', 'like', "%{$search}%");
                    });
            });
        }

        /* ---------------- DATE FILTER ---------------- */
        if ($range = $request->input('date_range')) {
            [$start, $end] = array_pad(explode(' to ', $range), 2, $range);

            $query->whereBetween('created_at', [
                "{$start} 00:00:00",
                "{$end} 23:59:59",
            ]);
        }

        /* ---------------- FILTERS ---------------- */
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_gateway', $request->payment_method);
        }

        if ($request->filled('min_amount')) {
            $query->where('amount', '>=', $request->min_amount);
        }

        if ($request->filled('max_amount')) {
            $query->where('amount', '<=', $request->max_amount);
        }

        /* ---------------- ORDERING ---------------- */
        $orderColIndex = (int) $request->input('order.0.column', 0);
        $orderCol = $columns[$orderColIndex] ?? 'id';
        $orderDir = $request->input('order.0.dir', 'desc');

        $query->orderBy($orderCol, $orderDir);

        /* ---------------- COUNTS (CLONE QUERIES) ---------------- */
        $recordsFiltered = (clone $query)->count();
        // $recordsTotal    = Transaction::count();


        $totalQuery = Transaction::query();
        if ($request->filled('user_id')) {
            $totalQuery->where('user_id', $request->user_id);
        }
        $recordsTotal = $totalQuery->count();




        /* ---------------- PAGINATION (FIXED) ---------------- */
        $start  = max((int) $request->input('start', 0), 0);
        $length = (int) $request->input('length', 10);

        // DataTables sends -1 when "Show all"
        if ($length === -1) {
            $length = 1000; // or any safe upper limit
        }

        $transactions = $query
            ->limit($length)
            ->offset($start)
            ->get();

        /* ---------------- DATA FORMAT ---------------- */
        $data = $transactions->values()->map(function ($transaction, $index) use ($request, $start) {

            $canView         = checkPermission('transactions.view');
            $canViewUsers    = checkPermission('users.view');
            $canViewOrders   = checkPermission('orders.view');
            $canUpdateStatus = checkPermission('transactions.update.status');

            $userHtml = $transaction->user?->name ?? 'N/A';
            if ($canViewUsers && $transaction->user) {
                $userHtml = redirect_to_link(
                    route('users.show', encrypt($transaction->user_id)),
                    $transaction->user->name
                );
            }

            $orderHtml = $transaction->order?->order_number ?? 'N/A';
            if ($canViewOrders && $transaction->order) {
                $orderHtml = redirect_to_link(
                    route('orders.show', encrypt($transaction->order_id)),
                    $transaction->order->order_number
                );
            }

            $statusHtml = view_payment_status($transaction->status);

            if ($canUpdateStatus && in_array($transaction->status, ['pending', 'processing'])) {
                $statusHtml .= '
                <div class="mt-1">
                    <select class="form-select form-select-sm update-status"
                        data-id="' . encrypt($transaction->id) . '">
                        <option value="">Change Status</option>
                        <option value="completed">Completed</option>
                        <option value="failed">Failed</option>
                        <option value="refunded">Refunded</option>
                    </select>
                </div>';
            }

            $amountHtml = '
            <div class="d-flex align-items-center gap-2">
                <span class="fw-bold">' . number_format($transaction->amount, 2) . '</span>
                <span class="badge bg-label-secondary">' . strtoupper($transaction->currency_code) . '</span>
            </div>';

            $methodHtml = '<span class="badge bg-label-info">' . ucfirst($transaction->payment_method) . '</span>';

            $actions = [];
            if ($canView) {
                $actions[] = btn_view(route($this->module . '.show', encrypt($transaction->id)), true);
            }

            return [
                'id'             => $start + $index + 1,
                'user'           => $userHtml,
                'order_id'       => $orderHtml,
                'transaction_id' => '<code>' . e($transaction->transaction_id) . '</code>',
                'payment_method' => $methodHtml,
                'amount'         => $amountHtml,
                'status'         => $statusHtml,
                'created_at'     => $transaction->created_at->format('d M Y'),
                'action'         => $actions ? button_group($actions) : 'No actions',
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
     * FIND TRANSACTION
     **********************************************/
    public function findById(int $id): Transaction
    {
        return Transaction::with(['user', 'order', 'order.items'])->findOrFail($id);
    }

    /**********************************************
     * CREATE TRANSACTION
     **********************************************/
    public function create(array $validated): Transaction
    {
        return DB::transaction(function () use ($validated) {
            return Transaction::create($validated);
        });
    }

    /**********************************************
     * UPDATE STATUS
     **********************************************/
    public function updateStatusById(int $id, string $status): Transaction
    {
        return DB::transaction(function () use ($id, $status) {
            $transaction = Transaction::findOrFail($id);

            $data = ['status' => $status];

            if ($status === 'refunded') {
                $data['refunded_at'] = now();
            }

            if ($status === 'completed') {
                $data['completed_at'] = now();
            }

            $transaction->update($data);

            return $transaction;
        });
    }

    /**********************************************
     * REFUND TRANSACTION
     **********************************************/
    public function refundTransaction(int $id): Transaction
    {
        return DB::transaction(function () use ($id) {
            $transaction = Transaction::findOrFail($id);

            // Check if transaction can be refunded
            if ($transaction->status !== 'completed') {
                throw new \Exception('Only completed transactions can be refunded.');
            }

            if ($transaction->refunded_at) {
                throw new \Exception('Transaction already refunded.');
            }

            $transaction->update([
                'status' => 'refunded',
                'refunded_at' => now(),
            ]);

            // Create refund record (optional)
            // DB::table('refunds')->insert([...]);

            return $transaction;
        });
    }

    /**********************************************
     * GET TRANSACTION STATISTICS
     **********************************************/
    public function getStatistics(): array
    {
        $total = Transaction::count();
        $totalAmount = Transaction::where('status', 'completed')->sum('amount');
        $pending = Transaction::where('status', 'pending')->count();
        $completed = Transaction::where('status', 'completed')->count();
        $failed = Transaction::where('status', 'failed')->count();
        $refunded = Transaction::where('status', 'refunded')->count();

        // Today's statistics
        $todayTotal = Transaction::whereDate('created_at', today())->count();
        $todayAmount = Transaction::whereDate('created_at', today())
            ->where('status', 'completed')
            ->sum('amount');

        // Monthly statistics
        $monthlyTotal = Transaction::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $monthlyAmount = Transaction::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status', 'completed')
            ->sum('amount');

        return [
            'total' => $total,
            'total_amount' => $totalAmount,
            'pending' => $pending,
            'completed' => $completed,
            'failed' => $failed,
            'refunded' => $refunded,
            'today_total' => $todayTotal,
            'today_amount' => $todayAmount,
            'monthly_total' => $monthlyTotal,
            'monthly_amount' => $monthlyAmount,
            'success_rate' => $total > 0 ? round(($completed / $total) * 100, 2) : 0,
        ];
    }

    /**********************************************
     * GET TRANSACTIONS FOR EXPORT
     **********************************************/
    public function getTransactionsForExport(Request $request)
    {
        $query = Transaction::with(['user', 'order']);

        // Apply filters if any
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_range')) {
            [$start, $end] = array_pad(explode(' to ', $request->date_range), 2, $request->date_range);
            $query->whereBetween('created_at', [
                "$start 00:00:00",
                "$end 23:59:59",
            ]);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**********************************************
     * GET PAYMENT METHODS STATISTICS
     **********************************************/
    public function getPaymentMethodsStats(): array
    {
        $methods = Transaction::select('payment_method')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('SUM(CASE WHEN status = "completed" THEN amount ELSE 0 END) as total_amount')
            ->groupBy('payment_method')
            ->orderByDesc('count')
            ->get();

        return $methods->toArray();
    }
}
