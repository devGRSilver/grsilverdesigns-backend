<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ResponseController;
use App\Http\Requests\Admin\{
    StatusUpdateRequest,
    UserPasswordRequest,
    UserStoreRequest
};
use App\Services\OrderService;
use App\Services\TransactionsService;
use App\Services\UserService;
use Exception;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends ResponseController
{
    protected string $resource = 'users';
    protected string $resourceName = 'User';

    public function __construct(
        protected UserService $userService,
        protected OrderService $orderService,
        protected TransactionsService $transactionsService,


    ) {}

    /**********************************************
     * LIST
     **********************************************/
    public function index(Request $request)
    {
        // Permission check already handled by middleware, but optional extra check
        // if (cannot('users.view.any')) {
        //     abort(403, 'Unauthorized action.');
        // }

        if ($request->ajax()) {
            return response()->json(
                $this->userService->getUsersForDataTable($request)
            );
        }

        return view("admin.{$this->resource}.index", [
            'title' => "{$this->resourceName} List"
        ]);
    }



    public function getStats(Request $request): JsonResponse
    {
        try {
            $stats = $this->userService->getStats($request->all());
            return $this->successResponse($stats, 'Statistics loaded successfully');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }




    public function getOrders(Request $request)
    {
        if ($request->ajax()) {
            return response()->json(
                $this->orderService->getDataForDataTable($request)
            );
        }
    }



    public function getTransactions(Request $request)
    {
        if ($request->ajax()) {
            return response()->json(
                $this->transactionsService->getDataForDataTable($request)
            );
        }
    }






    /**********************************************
     * CREATE
     **********************************************/
    public function create()
    {
        return view("admin.{$this->resource}.add", [
            'title' => "Add {$this->resourceName}"
        ]);
    }

    public function store(UserStoreRequest $request)
    {
        try {
            $this->userService->createUser($request->validated());

            return $this->successResponse(
                [],
                "{$this->resourceName} created successfully."
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function show(Request $request, string $encryptedId)
    {
        try {
            $user = $this->userService->findById(decrypt($encryptedId));

            return view("admin.{$this->resource}.show", [
                'title' => "{$this->resourceName} Details",
                'data'  => $user,
            ]);
        } catch (DecryptException $e) {
            return redirect()
                ->route('users.index')
                ->with('error', 'Invalid user reference.');
        }
    }




    public function getUserOrder(Request $request, string $encryptedId)
    {
        if ($request->ajax()) {
            return response()->json(
                $this->orderService->getDataForDataTable($request)
            );
        }
    }










    /**********************************************
     * EDIT
     **********************************************/
    public function edit(string $encryptedId)
    {
        try {
            $user = $this->userService->findById(decrypt($encryptedId));

            return view("admin.{$this->resource}.edit", [
                'title' => "Edit {$this->resourceName}",
                'data'  => $user,
            ]);
        } catch (DecryptException) {
            return redirect()->route('users.index')
                ->with('error', 'Invalid user reference.');
        }
    }

    public function update(UserStoreRequest $request, string $encryptedId)
    {
        try {
            $this->userService->updateUserById(
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
        try {
            $user = $this->userService->findById(decrypt($encryptedId));

            return view("admin.{$this->resource}.change_password", [
                'title'   => 'Change Password',
                'data'    => $user,
                'user_id' => $encryptedId
            ]);
        } catch (DecryptException) {
            return redirect()->route('users.index')
                ->with('error', 'Invalid user reference.');
        }
    }

    public function updatePassword(UserPasswordRequest $request, string $encryptedId)
    {
        try {
            $this->userService->updatePasswordById(
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
            $this->userService->updateStatusById(
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
            $this->userService->deleteUserById(decrypt($encryptedId));

            return $this->successResponse([], "User deleted successfully.");
        } catch (\Exception $e) {
            return $this->errorResponse(
                "Failed to delete {$this->resourceName}.",
                500
            );
        }
    }
}
