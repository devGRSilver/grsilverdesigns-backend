<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ResponseController;
use App\Http\Requests\Admin\RoleRequest;
use App\Http\Requests\Admin\StatusUpdateRequest;
use App\Services\RoleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Symfony\Component\HttpFoundation\Response;

class RoleController extends ResponseController
{
    protected string $resource = 'roles';
    protected string $resourceName = 'Role';

    protected RoleService $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * Role List
     */
    public function index(Request $request)
    {

        if ($request->ajax()) {
            return response()->json(
                $this->roleService->getDataForDataTable($request),
                Response::HTTP_OK
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
            'title'             => "Add {$this->resourceName}",
            'groups'            => $this->roleService->getPermissionsGroup(),
            'totalPermissions'  => Permission::count(),
        ]);
    }

    /**
     * Store Role
     */
    public function store(RoleRequest $request)
    {
        try {
            $this->roleService->create($request->validated());

            return $this->successResponse(
                [],
                "{$this->resourceName} created successfully.",
                route('roles.index')
            );
        } catch (\Throwable $e) {
            Log::error('Role store failed', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => "Unable to create {$this->resourceName}."]);
        }
    }

    /**
     * Edit Form
     */
    public function edit(string $encryptedId)
    {
        try {
            $role = $this->roleService->findByEncryptedId($encryptedId);

            abort_if(!$role, Response::HTTP_NOT_FOUND, "{$this->resourceName} not found.");

            return view("admin.{$this->resource}.edit", [
                'title'            => "Edit {$this->resourceName}",
                'role'             => $role,
                'groups'           => $this->roleService->getPermissionsGroup(),
                'rolePermissions'  => $role->permissions->pluck('id')->toArray(),
                'totalPermissions' => Permission::count(),
            ]);
        } catch (\Throwable $e) {

            Log::warning('Role edit failed', [
                'encrypted_id' => $encryptedId,
                'exception'    => $e,
            ]);

            abort(Response::HTTP_NOT_FOUND, "{$this->resourceName} not found.");
        }
    }


    /**
     * Update Role
     */
    public function update(RoleRequest $request, string $encryptedId)
    {
        try {
            $this->roleService->update($encryptedId, $request->validated());

            return $this->successResponse(
                [],
                "{$this->resourceName} updated successfully."
            );
        } catch (\Throwable $e) {
            Log::error('Role update failed', [
                'encrypted_id' => $encryptedId,
                'message'      => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => "Unable to update {$this->resourceName}."]);
        }
    }

    /**
     * Show Role Details
     */
    public function show(string $encryptedId)
    {
        try {
            $data = $this->roleService->getRoleWithDetails($encryptedId);

            return view("admin.{$this->resource}.show", array_merge(
                ['title' => "{$this->resourceName} Details"],
                $data
            ));
        } catch (\Throwable $e) {
            Log::warning('Role show not found', [
                'encrypted_id' => $encryptedId,
                'message'      => $e->getMessage(),
            ]);
            abort(Response::HTTP_NOT_FOUND, "{$this->resourceName} not found.");
        }
    }

    /**
     * Delete Role
     */
    /**
     * Delete Role (BLOCK IF USERS EXIST)
     */
    public function delete(string $encryptedId)
    {
        try {
            $role = $this->roleService->findByEncryptedId($encryptedId);

            abort_if(!$role, Response::HTTP_NOT_FOUND, "{$this->resourceName} not found.");

            // ✅ Check association
            $hasUsers = DB::table('model_has_roles')
                ->where('role_id', $role->id)
                ->where('model_type', \App\Models\User::class)
                ->exists();

            if ($hasUsers) {
                return $this->errorResponse(
                    "This {$this->resourceName} is assigned to users and cannot be deleted.",
                    Response::HTTP_CONFLICT
                );
            }

            $this->roleService->delete($role->id);

            return $this->successResponse(
                [],
                "{$this->resourceName} deleted successfully."
            );
        } catch (\Throwable $e) {
            Log::error('Role delete failed', [
                'encrypted_id' => $encryptedId,
            ]);

            return $this->errorResponse(
                "Unable to delete {$this->resourceName}.",
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }



    /**
     * Update Role Status
     */
    public function updateStatus(StatusUpdateRequest $request, string $encryptedId)
    {
        try {
            $id = decrypt($encryptedId);
            $this->roleService->updateStatus($id, $request->status);
            return $this->successResponse([], 'Status updated successfully.');
        } catch (\Exception $e) {
            Log::error('Role status update failed: ' . $e->getMessage());
            return $this->errorResponse(
                'Failed to update status.',
                500
            );
        }
    }
}
