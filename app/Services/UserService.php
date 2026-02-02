<?php

namespace App\Services;

use App\Constants\Constant;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
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
        $columns = ['id', 'name', 'phone', 'email', 'created_at'];

        /** BASE QUERY */
        $baseQuery = User::whereHas('roles', function ($q) {
            $q->where('name', $this->defaultRole);
        });

        /** TOTAL RECORDS (WITHOUT FILTER) */
        $recordsTotal = (clone $baseQuery)->count();

        /** FILTERED QUERY */
        $filteredQuery = clone $baseQuery;

        /** SEARCH */
        if ($search = $request->input('search.value')) {
            $filteredQuery->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%")
                    ->orWhereRaw(
                        "CONCAT(phone_code, phone) LIKE ?",
                        ["%{$search}%"]
                    );
            });
        }

        /** DATE FILTER */
        if ($range = $request->input('date_range')) {
            [$start, $end] = array_pad(explode(' to ', $range), 2, $range);

            $filteredQuery->whereBetween('created_at', [
                "{$start} 00:00:00",
                "{$end} 23:59:59",
            ]);
        }

        /** FILTERED COUNT (BEFORE PAGINATION) */
        $recordsFiltered = (clone $filteredQuery)->count();

        /** ORDERING */
        $orderCol = $columns[$request->input('order.0.column', 0)] ?? 'id';
        $orderDir = $request->input('order.0.dir', 'desc');

        $filteredQuery->orderBy($orderCol, $orderDir);

        /** PAGINATION */
        $users = $filteredQuery
            ->offset($request->start)
            ->limit($request->length)
            ->get();

        /** DATA MAPPING */
        $data = $users->map(function ($user, $index) use ($request) {

            $canView         = checkPermission('users.view');
            $canEdit         = checkPermission('users.update');
            $canDelete       = checkPermission('users.delete');
            $canUpdateStatus = checkPermission('users.update.status');

            /** STATUS */
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
                'id'          => $request->start + $index + 1,
                'name'        => ucfirst($user->name),
                'phone'       => trim("{$user->phone_code} {$user->phone}"),
                'email'       => $user->email,
                'total_order' => 0, // later optimize with withCount()
                'status'      => $statusHtml,
                'created_at'  => $user->created_at->format('d M Y h:i a'),
                'action'      => $actions ? button_group($actions) : 'No actions',
            ];
        });

        return [
            'draw'            => (int) $request->draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ];
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
            $validated['password'] = Hash::make($validated['password']);

            $user = User::create($validated);

            $role = Role::firstOrCreate([
                'name'       => $this->defaultRole,
                'guard_name' => 'web',
            ]);

            $user->assignRole($role);

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
