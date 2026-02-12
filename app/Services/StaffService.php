<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class StaffService
{

    /**********************************************
     * DATATABLE STAFF LIST
     **********************************************/
    public function getUsersForDataTable(Request $request): array
    {
        $columns = ['id', 'name', 'phone', 'email', 'created_at'];

        /** BASE QUERY (Exclude role IDs 1 & 2) */
        $baseQuery = User::query()
            ->with('roles')
            ->whereHas('roles', function ($q) {
                $q->whereNotIn('id', [1, 2]);
            });

        /** ROLE FILTER */
        if ($request->filled('role_id')) {
            $roleId = $request->role_id;

            $baseQuery->whereHas('roles', function ($q) use ($roleId) {
                $q->where('id', $roleId);
            });
        }

        /** CLONE FOR FILTERED QUERY */
        $filteredQuery = clone $baseQuery;

        /** SEARCH */
        if ($search = $request->input('search.value')) {
            $filteredQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhereRaw(
                        "CONCAT(COALESCE(phonecode,''), COALESCE(phone,'')) LIKE ?",
                        ["%{$search}%"]
                    );
            });
        }

        /** DATE RANGE FILTER */
        if ($range = $request->input('date_range')) {
            [$start, $end] = array_pad(explode(' to ', $range), 2, null);

            if ($start && $end) {
                $filteredQuery->whereBetween('created_at', [
                    "{$start} 00:00:00",
                    "{$end} 23:59:59",
                ]);
            }
        }

        /** RECORD COUNTS (before pagination) */
        $recordsTotal    = $baseQuery->count();
        $recordsFiltered = $filteredQuery->count();

        /** ORDERING */
        $orderIndex     = (int) $request->input('order.0.column', 0);
        $orderColumn    = $columns[$orderIndex] ?? 'id';
        $orderDirection = $request->input('order.0.dir', 'desc');

        $filteredQuery->orderBy($orderColumn, $orderDirection);

        /** PAGINATION */
        $users = $filteredQuery
            ->skip((int) $request->start)
            ->take((int) $request->length)
            ->get();

        /** DATA MAPPING */
        $data = $users->map(function (User $user, int $index) use ($request) {
            $role = $user->roles->first();

            return [
                'id'         => $request->start + $index + 1,
                'name'       => ucfirst($user->name),
                'phone'      => trim("{$user->phonecode} {$user->phone}"),
                'email'      => $user->email,

                'role' => $role
                    ? redirect_to_link(
                        route('roles.show', encrypt($role->id)),
                        $user->roles->pluck('display_name')->implode(', ')
                    )
                    : '-',

                'status' => status_dropdown($user->status, [
                    'id'     => $user->id,
                    'url'    => route('staff.status', encrypt($user->id)),
                    'method' => 'PUT',
                ]),

                'created_at' => $user->created_at->format('d M Y h:i A'),

                'action' => button_group([
                    btn_view(route('staff.show', encrypt($user->id)), false),
                    btn_edit(route('staff.edit', encrypt($user->id)), true),
                ]),
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
     * FIND USER
     **********************************************/
    public function findById(int $id): User
    {
        return User::findOrFail($id);
    }



    /**********************************************
     * CREATE STAFF
     **********************************************/
    public function createUser(array $validated): User
    {
        return DB::transaction(function () use ($validated) {
            $validated['password'] = Hash::make($validated['password']);

            $user = User::create($validated);

            $role = Role::where('id', $validated['role'])->first();
            $user->syncRoles([$role->name]);

            return $user;
        });
    }

    /**********************************************
     * UPDATE USER
     **********************************************/
    public function updateUserById(int $id, array $validated): User
    {
        return DB::transaction(function () use ($id, $validated) {

            $user = User::findOrFail($id);
            $user->update($validated);

            return $user;
        });
    }

    /**********************************************
     * UPDATE PASSWORD
     **********************************************/
    public function updatePasswordById(int $id, string $password): User
    {
        return DB::transaction(function () use ($id, $password) {

            $user = User::findOrFail($id);

            $user->update([
                'password' => Hash::make($password),
            ]);

            /** LOGOUT FROM ALL DEVICES */
            DB::table('sessions')->where('user_id', $user->id)->delete();

            return $user;
        });
    }

    /**********************************************
     * UPDATE STATUS
     **********************************************/
    public function updateStatusById(int $id, int $status): User
    {
        return DB::transaction(function () use ($id, $status) {

            $user = User::findOrFail($id);
            $user->update(['status' => $status]);

            return $user;
        });
    }

    /**********************************************
     * DELETE USER
     **********************************************/
    public function deleteUserById(int $id): bool
    {
        return DB::transaction(function () use ($id) {

            if (auth()->id() === $id) {
                throw new Exception('You cannot delete your own account.');
            }

            $user = User::findOrFail($id);
            $user->delete();
            return true;
        });
    }
}
