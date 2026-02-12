<?php

namespace App\Services;

use App\Constants\Constant;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class UserService
{
    protected string $module = 'users';
    protected string $defaultRole = 'user';

    /**********************************************
     * DATATABLE USERS LIST with Permission-Based Actions
     **********************************************/
    public function getUsersForDataTable($request): array
    {
        $columns = ['id', 'name', 'phonecode', 'phone', 'email', 'created_at'];

        /** BASE QUERY */
        $baseQuery = User::withCount('orders')
            ->whereHas('roles', function ($q) {
                $q->where('name', $this->defaultRole);
            });

        $recordsTotal = (clone $baseQuery)->count();

        $filteredQuery = clone $baseQuery;

        if ($search = trim($request->input('search.value'))) {

            $cleanPhoneSearch = preg_replace('/[^\d+]/', '', $search);

            $filteredQuery->where(function ($q) use ($search, $cleanPhoneSearch) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%");

                if (!empty($cleanPhoneSearch)) {
                    $dbSearch = ltrim($cleanPhoneSearch, '+');

                    $q->orWhereRaw(
                        "CONCAT(phonecode, phone) LIKE ?",
                        ["%{$dbSearch}%"]
                    );

                    if (strlen($dbSearch) > 2) {
                        $q->orWhere('phone', 'LIKE', "%{$dbSearch}%");
                    }
                }
            });
        }

        if ($range = $request->input('date_range')) {
            $dates = explode(' to ', $range);

            if (count($dates) === 2) {
                $start = trim($dates[0]);
                $end = trim($dates[1]);

                // Validate dates
                if ($this->isValidDate($start) && $this->isValidDate($end)) {
                    $filteredQuery->whereBetween('created_at', [
                        "{$start} 00:00:00",
                        "{$end} 23:59:59",
                    ]);
                }
            } elseif (count($dates) === 1) {
                $start = trim($dates[0]);
                if ($this->isValidDate($start)) {
                    $filteredQuery->whereDate('created_at', $start);
                }
            }
        }


        // Status Filter
        if ($request->filled('status')) {
            $filteredQuery->where('status', $request->status);
        }



        if ($request->filled('free_shipping')) {
            $filteredQuery->where('free_shipping', $request->free_shipping);
        }








        $recordsFiltered = (clone $filteredQuery)->count();

        $orderColIndex = $request->input('order.0.column', 0);
        $orderCol = $columns[$orderColIndex] ?? 'id';
        $orderDir = $request->input('order.0.dir', 'desc');

        if ($orderCol === 'phone') {
            $filteredQuery->orderByRaw("CONCAT(phonecode, phone) {$orderDir}");
        } else {
            $filteredQuery->orderBy($orderCol, $orderDir);
        }

        $start = max(0, (int) $request->input('start', 0));
        $length = max(1, min(100, (int) $request->input('length', 10))); // Limit to 100 records per page

        $users = $filteredQuery
            ->skip($start)
            ->take($length)
            ->get();

        $data = $users->map(function ($user, $index) use ($request, $start) {

            $canView = checkPermission('users.view');
            $canEdit = checkPermission('users.update');
            $canDelete = checkPermission('users.delete');
            $canUpdateStatus = checkPermission('users.update.status');

            if ($canUpdateStatus) {
                $statusHtml = status_dropdown($user->status, [
                    'id'     => $user->id,
                    'url'    => route('users.status', encrypt($user->id)),
                    'method' => 'PUT',
                ]);
            } else {
                $statusHtml = $user->status
                    ? '<span class="badge bg-label-success">Active</span>'
                    : '<span class="badge bg-label-danger">Inactive</span>';
            }

            /** ACTIONS */
            $actions = [];

            if ($canView) {
                $actions[] = btn_view(route('users.show', encrypt($user->id)));
            }

            if ($canEdit) {
                $actions[] = btn_edit(route('users.edit', encrypt($user->id)), true);
            }

            if ($canDelete) {
                $actions[] = btn_delete(route('users.delete', encrypt($user->id)), true);
            }

            return [
                'id'          => $start + $index + 1,
                'name'        => ucfirst($user->name),
                'phone'       => $this->formatPhoneNumber($user->phonecode, $user->phone),
                'email'       => $user->email,
                'total_order' => $user->orders_count ?? 0,
                'status'      => $statusHtml,
                'created_at'  => $user->created_at->format('d M Y h:i a'),
                'action'      => !empty($actions) ? button_group($actions) : 'No actions',
            ];
        });

        return [
            'draw'            => (int) $request->input('draw', 0),
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ];
    }

    /**
     * Helper method to validate date
     */
    private function isValidDate($date): bool
    {
        $format = 'Y-m-d';
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    /**
     * Helper method to format phone number
     */
    private function formatPhoneNumber($phoneCode, $phone): string
    {
        if (empty($phone)) {
            return 'N/A';
        }

        $formatted = trim($phoneCode . ' ' . $phone);
        return !empty($formatted) ? $formatted : 'N/A';
    }

    public function getStats(array $requestData): array
    {
        $startDate = null;
        $endDate = null;

        if (!empty($requestData['date_range'])) {
            [$start, $end] = array_pad(explode(' to ', $requestData['date_range']), 2, $requestData['date_range']);
            $startDate = Carbon::parse($start)->startOfDay();
            $endDate   = Carbon::parse($end)->endOfDay();
        }


        // Order stats with date filter
        $orderQuery = Order::where('user_id', $requestData['user_id']);
        if ($startDate && $endDate) {
            $orderQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        $orderStats = $orderQuery->selectRaw("
            COUNT(*) as total_orders,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as delivered_orders,
            SUM(CASE WHEN status IN (?, ?) THEN 1 ELSE 0 END) as processing_orders,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as cancelled_orders
        ", [
            OrderStatus::DELIVERED->value,
            OrderStatus::PAYMENT_RECEIVED->value,
            OrderStatus::CONFIRMED->value,
            OrderStatus::CANCELLED->value,
        ])->first();
        return [
            'total_orders'      => $orderStats->total_orders ?? 0,
            'processing_orders' => $orderStats->processing_orders ?? 0,
            'delivered_orders'  => $orderStats->delivered_orders ?? 0,
            'total_spend'  =>  0,
        ];
    }



    /**********************************************
     * FIND USER
     **********************************************/
    public function findById(int $id): User
    {
        return User::findOrFail($id);
    }

    /**********************************************
     * CREATE USER
     **********************************************/
    public function createUser(array $validated): User
    {
        DB::beginTransaction();

        try {
            // Check if user with same email/phone exists in soft-deleted records
            $softDeletedUser = User::withTrashed()
                ->where(function ($query) use ($validated) {
                    $query->where('email', $validated['email'])
                        ->orWhere('phone', $validated['phone']);
                })
                ->whereNotNull('deleted_at')
                ->first();

            if ($softDeletedUser) {
                // Restore and update the soft-deleted user
                $softDeletedUser->restore();

                $validated['password'] = Hash::make($validated['password']);
                $softDeletedUser->update($validated);

                $user = $softDeletedUser;
            } else {
                // Create new user
                $validated['password'] = Hash::make($validated['password']);
                $user = User::create($validated);
            }

            // Assign role (check if already has role first)
            if (!$user->hasRole($this->defaultRole)) {
                $role = Role::firstOrCreate([
                    'name'       => $this->defaultRole,
                    'guard_name' => 'web',
                ]);
                $user->assignRole($role);
            }

            DB::commit();
            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("User creation failed: {$e->getMessage()}");
            throw $e;
        }
    }

    /**********************************************
     * UPDATE USER
     **********************************************/
    public function updateUserById(int $id, array $validated): User
    {
        DB::beginTransaction();

        try {
            $user = User::findOrFail($id);
            $user->update($validated);

            DB::commit();
            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("User update failed. ID {$id}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**********************************************
     * UPDATE PASSWORD
     **********************************************/
    public function updatePasswordById(int $id, string $password): User
    {
        DB::beginTransaction();

        try {
            $user = User::findOrFail($id);
            $user->update([
                'password' => Hash::make($password),
            ]);

            // Clear all sessions for this user
            DB::table('sessions')->where('user_id', $user->id)->delete();

            DB::commit();
            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Password update failed. ID {$id}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**********************************************
     * UPDATE STATUS
     **********************************************/
    public function updateStatusById(int $id, int $status): User
    {
        DB::beginTransaction();

        try {
            $user = User::findOrFail($id);
            $user->update(['status' => $status]);

            DB::commit();
            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Status update failed. ID {$id}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**********************************************
     * DELETE USER
     **********************************************/
    public function deleteUserById(int $id): bool
    {
        DB::beginTransaction();

        try {
            // Prevent self-deletion
            if (auth('admin')->id() === $id) {
                throw new Exception('You cannot delete your own account.');
            }

            User::findOrFail($id)->delete();

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("User delete failed. ID {$id}: {$e->getMessage()}");
            throw $e;
        }
    }
}
