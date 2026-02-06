<?php

namespace App\Services;

use App\Constants\Constant;
use App\Models\PermissionGroup;
use App\Models\Review;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class RoleService
{


    public function getRoles(): Collection
    {
        return Role::query()
            ->whereNotIn('id', [1, 2])
            ->orderBy('name', 'ASC')
            ->get();
    }

    /**
     * DataTable data for Roles
     */
    public function getDataForDataTable(Request $request): array
    {
        /**
         * Column mapping must match DataTable columns order
         */
        $columns = [
            'id',
            'display_name',
            'name',
            'permissions_count',
            'users_count',        // ✅ Associated users
            'updated_at',
        ];

        $query = Role::whereNotIn('id', [1, 2])
            ->withCount(['permissions', 'users']); // ✅ add users count

        /* 🔍 Global Search */
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('display_name', 'like', "%{$search}%");
            });
        }


        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }



        /* ↕ Ordering */
        $orderIndex = $request->input('order.0.column', 0);
        $orderBy    = $columns[$orderIndex] ?? 'id';
        $orderDir   = $request->input('order.0.dir', 'desc');

        $query->orderBy($orderBy, $orderDir);

        /* 📊 Counts */
        $recordsTotal    = Role::whereNotIn('id', [1, 2])->count();
        $recordsFiltered = $query->count();

        /* 📥 Pagination */
        $roles = $query
            ->skip($request->start)
            ->take($request->length)
            ->get();

        /* 🧱 Data Formatting */
        $data = $roles->map(function ($role, $index) use ($request) {
            return [
                'id'               => $request->start + $index + 1,
                'display_name'     => ucfirst($role->display_name),
                'name'             => $role->name,
                'total_permission' => $role->permissions_count,
                'associated_users' => redirect_to_link(route('staff.index'), $role->users_count),
                'updated_at'       => $role->updated_at->format('d M Y'),
                'status' => status_dropdown($role->status, [
                    'id'  => $role->id,
                    'url' => route('roles.status', encrypt($role->id)),
                ]),


                'action' => button_group([
                    btn_view(route('roles.show', encrypt($role->id))),
                    btn_edit(route('roles.edit', encrypt($role->id))),
                    btn_delete(route('roles.delete', encrypt($role->id))),
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

    /**
     * Create Role
     */
    public function create(array $data): Role
    {
        $role = Role::create([
            'display_name' => $data['name'],
            'name' => Str::slug($data['name'], '_'),
            'guard_name' => $data['guard_name'] ?? 'admin',
        ]);

        if (!empty($data['permissions'])) {
            $permissionNames = Permission::whereIn('id', $data['permissions'])
                ->pluck('name')
                ->toArray();
            $role->syncPermissions($permissionNames);
        }

        return $role;
    }

    /**
     * Update Role
     */
    public function update(string $encryptedId, array $data): Role
    {
        $role = $this->findByEncryptedId($encryptedId);

        // Update role name
        $role->update([
            'display_name' => $data['name'],
            'name' => Str::slug($data['name'], '_'),
            'guard_name' => $data['guard_name'] ?? 'admin',
        ]);

        if (!empty($data['permissions'])) {
            $permissionNames = Permission::whereIn('id', $data['permissions'])
                ->pluck('name')
                ->toArray();
            $role->syncPermissions($permissionNames);
        }

        return $role->fresh(); // Return fresh instance with updated relationships
    }

    /**
     * Delete Role
     */
    public function delete(string $encryptedId): void
    {
        $this->findByEncryptedId($encryptedId)->delete();
    }

    /**
     * Find Role by encrypted ID
     */
    public function findByEncryptedId(string $encryptedId): Role
    {
        return Role::with('permissions')
            ->findOrFail(decrypt($encryptedId));
    }

    /**
     * Get role with related data for viewing
     */
    public function getRoleWithDetails(string $encryptedId): array
    {
        $role = $this->findByEncryptedId($encryptedId);

        return [
            'role' => $role,
            'rolePermissions' => $role->permissions,
            'groups' => PermissionGroup::with(['permissions' => function ($query) {
                $query->orderBy('display_name');
            }])->orderBy('display_name')->get(),
            'totalPermissions' => Permission::count(),
        ];
    }

    /**
     * Get permission groups with permissions
     */
    public function getPermissionsGroup(): Collection
    {
        return PermissionGroup::with('permissions')
            ->where('status', Constant::ACTIVE)
            ->orderBy('display_name')
            ->get();
    }



    public function updateStatus(int $id, bool $status)
    {
        DB::beginTransaction();
        try {
            $role = Role::findOrFail($id);
            $role->update(['status' => $status]);
            if ($status == Constant::IN_ACTIVE) {
                $userIds = User::role($role->name)->pluck('id');
                if ($userIds->isNotEmpty()) {
                    User::whereIn('id', $userIds)
                        ->update(['status' => false]);
                    DB::table('sessions')
                        ->whereIn('user_id', $userIds)
                        ->delete();
                }
            }
            DB::commit();
            return $role;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Role status update failed. ID {$id}: {$e->getMessage()}");
            throw $e;
        }
    }
}
