<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ResponseController;
use App\Http\Requests\Api\UpdateProfileRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class UserController extends ResponseController
{

    public function __construct(
        protected UserService $userService
    ) {}


    /**
     * Get authenticated user profile
     */
    public function getUserDetails(Request $request): JsonResponse
    {
        try {

            $user_id = $request->user()->id;

            $data =  $this->userService->getUserDetails($user_id);

            return $this->successResponse(
                $data,
                'Profile retrieved successfully',
                redirect_url: null,
                statusCode: Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('Failed to retrieve profile', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id ?? null
            ]);

            return $this->errorResponse(
                message: 'Failed to retrieve profile',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Update user profile
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        try {
            $user = $this->userService->updateUserById(
                $request->user()->id,
                $request->validated()
            );

            $user->refresh();

            return $this->successResponse(
                $user,
                'Profile updated successfully',
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('Failed to update profile', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id ?? null
            ]);

            return $this->errorResponse(
                'Failed to update profile',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
