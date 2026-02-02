<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ResponseController;
use App\Http\Requests\Admin\{
    StaffStoreRequest,
    StatusUpdateRequest,
    UserPasswordRequest,
    UserStoreRequest
};
use App\Services\RoleService;
use App\Services\StaffService;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class StaffController extends ResponseController
{
    protected string $resource = 'staff';
    protected string $resourceName = 'Staff';

    public function __construct(
        protected StaffService $staffService,
        protected RoleService $roleService,
    ) {}

    /**********************************************
     * LIST
     **********************************************/
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return response()->json(
                $this->staffService->getUsersForDataTable($request)
            );
        }

        return view("admin.{$this->resource}.index", [
            'title' => "{$this->resourceName} List",
            'roles'     => $this->roleService->getRoles(),
        ]);
    }

    /**********************************************
     * CREATE
     **********************************************/
    public function create()
    {

        return view("admin.{$this->resource}.add", [
            'title' => "Add {$this->resourceName}",
            'roles' => $this->roleService->getRoles()
        ]);
    }

    public function store(StaffStoreRequest $request)
    {
        try {
            $this->staffService->createUser($request->validated());
            return $this->successResponse(
                [],
                "{$this->resourceName} created successfully."
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                "Failed to create {$this->resourceName}.",
                500
            );
        }
    }

    /**********************************************
     * VIEW
     **********************************************/
    public function show(string $encryptedId)
    {
        try {
            $userId = decrypt($encryptedId);
            $user   = $this->staffService->findById($userId);

            abort_if(!$user, Response::HTTP_NOT_FOUND);

            return view("admin.{$this->resource}.show", [
                'title' => "{$this->resourceName} Details",
                'data'  => $user,
            ]);
        } catch (DecryptException $e) {

            Log::warning('Invalid encrypted user id', [
                'encrypted_id' => $encryptedId,
            ]);

            return redirect()
                ->route('staff.index')
                ->with('error', 'Invalid user reference.');
        }
    }
    /**********************************************
     * EDIT
     **********************************************/
    /**********************************************
     * EDIT
     **********************************************/
    public function edit(string $encryptedId)
    {
        try {
            $user = $this->staffService->findById(decrypt($encryptedId));

            return view("admin.{$this->resource}.edit", [
                'title' => "Edit {$this->resourceName}",
                'data'  => $user,
                'roles' => $this->roleService->getRoles(),
            ]);
        } catch (DecryptException $e) {

            return redirect()
                ->route('staff.index')
                ->with('error', 'Invalid staff reference.');
        } catch (\Exception $e) {

            return redirect()
                ->route('staff.index')
                ->with('error', 'Something went wrong.');
        }
    }

    public function update(StaffStoreRequest $request, string $encryptedId)
    {
        try {
            $this->staffService->updateUserById(
                decrypt($encryptedId),
                $request->validated()
            );

            return $this->successResponse(
                [],
                "{$this->resourceName} details updated successfully."
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                "Failed to update {$this->resourceName}.",
                500
            );
        }
    }

    /**********************************************
     * PASSWORD
     **********************************************/
    public function editPassword(string $encryptedId)
    {
        return view("admin.{$this->resource}.change_password", [
            'title'   => 'Change Password',
            'user_id' => $encryptedId
        ]);
    }

    public function updatePassword(UserPasswordRequest $request, string $encryptedId)
    {
        try {
            $this->staffService->updatePasswordById(
                decrypt($encryptedId),
                $request->password
            );

            return $this->successResponse([], "Password updated successfully.");
        } catch (\Exception $e) {
            return $this->errorResponse("Failed to update password.", 500);
        }
    }

    /**********************************************
     * STATUS
     **********************************************/
    public function updateStatus(StatusUpdateRequest $request, string $encryptedId)
    {
        try {
            $this->staffService->updateStatusById(
                decrypt($encryptedId),
                $request->status
            );
            return $this->successResponse([], "Status updated successfully.");
        } catch (\Exception $e) {
            return $this->errorResponse("Failed to update status.", 500);
        }
    }

    /**********************************************
     * DELETE
     **********************************************/
    public function delete(string $encryptedId)
    {
        try {
            $this->staffService->deleteUserById(decrypt($encryptedId));

            return $this->successResponse([], "User deleted successfully.");
        } catch (\Exception $e) {
            return $this->errorResponse(
                "Failed to delete {$this->resourceName}.",
                500
            );
        }
    }
}
