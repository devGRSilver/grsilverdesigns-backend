<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ResponseController;
use App\Http\Requests\Api\OtpRequest;
use App\Http\Requests\Api\VerifyOtpRequest;
use App\Http\Requests\Api\ResendOtpRequest;
use App\Http\Requests\Api\UpdateProfileRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends ResponseController
{
    public function __construct(
        protected AuthService $authService
    ) {}

    /**
     * Send OTP to phone number
     */
    public function sendOtp(OtpRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->sendOtp($request->validated());

            Log::info('OTP sent successfully', [
                'phone' => $request->input('phonecode') . $request->input('phone'),
                'type' => $request->input('type', 'login')
            ]);

            return $this->successResponse(
                data: [
                    'token' => $result['token'],
                    'expires_in' => $result['expires_in']
                ],
                message: 'OTP sent successfully to your phone number',
                redirect_url: null,
                statusCode: Response::HTTP_OK
            );
        } catch (ValidationException $e) {
            return $this->errorResponse(
                message: 'Validation failed',
                statusCode: Response::HTTP_UNPROCESSABLE_ENTITY,
                errors: $e->errors()
            );
        } catch (\Exception $e) {

            dd($e);

            Log::error('Failed to send OTP', [
                'error' => $e->getMessage(),
                'phone' => $request->input('phonecode') . $request->input('phone')
            ]);

            return $this->errorResponse(
                message: 'Failed to send OTP. Please try again.',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
                errors: ['general' => [$e->getMessage()]]
            );
        }
    }

    /**
     * Verify OTP and authenticate user
     */
    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->verifyOtp(
                $request->validated(),
                $request->ip(),
                $request->userAgent()
            );



            Log::info('OTP verified successfully', [
                'user_id' => $result['user']['id'] ?? null,
                'is_new_user' => $result['is_new_user'] ?? false
            ]);

            return $this->successResponse(
                data: [
                    'user' => $result['user'],
                    'access_token' => $result['access_token'],
                    'token_type' => 'Bearer',
                    'is_new_user' => $result['is_new_user'] ?? false,
                    'expires_in' => config('sanctum.expiration', 525600) * 60
                ],
                message: $result['message'] ?? 'OTP verified successfully',
                redirect_url: null,
                statusCode: Response::HTTP_OK
            );
        } catch (ValidationException $e) {
            return $this->errorResponse(
                message: 'Verification failed',
                statusCode: Response::HTTP_UNPROCESSABLE_ENTITY,
                errors: $e->errors()
            );
        } catch (\Exception $e) {
            Log::error('Failed to verify OTP', [
                'error' => $e->getMessage(),
                'token' => $request->input('token')
            ]);

            return $this->errorResponse(
                message: 'OTP verification failed',
                statusCode: Response::HTTP_UNPROCESSABLE_ENTITY,
                errors: ['otp' => [$e->getMessage()]]
            );
        }
    }

    /**
     * Resend OTP
     */
    public function resendOtp(ResendOtpRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->resendOtp($request->validated()['token']);

            Log::info('OTP resent successfully', [
                'token' => $request->input('token')
            ]);

            return $this->successResponse(
                data: [
                    'token' => $result['token'],
                    'expires_in' => $result['expires_in']
                ],
                message: 'OTP has been resent successfully',
                redirect_url: null,
                statusCode: Response::HTTP_OK
            );
        } catch (ValidationException $e) {
            return $this->errorResponse(
                message: 'Validation failed',
                statusCode: Response::HTTP_UNPROCESSABLE_ENTITY,
                errors: $e->errors()
            );
        } catch (\Exception $e) {
            Log::error('Failed to resend OTP', [
                'error' => $e->getMessage(),
                'token' => $request->input('token')
            ]);

            return $this->errorResponse(
                message: 'Failed to resend OTP',
                statusCode: Response::HTTP_UNPROCESSABLE_ENTITY,
                errors: ['token' => [$e->getMessage()]]
            );
        }
    }

    /**
     * Logout user and revoke current token
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return $this->errorResponse(
                    message: 'Unauthenticated',
                    statusCode: Response::HTTP_UNAUTHORIZED
                );
            }

            $user->currentAccessToken()->delete();

            Log::info('User logged out successfully', [
                'user_id' => $user->id
            ]);

            return $this->successResponse(
                data: [],
                message: 'Logged out successfully',
                redirect_url: null,
                statusCode: Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('Logout failed', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id ?? null
            ]);

            return $this->errorResponse(
                message: 'Logout failed',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
                errors: ['general' => [$e->getMessage()]]
            );
        }
    }

    /**
     * Get authenticated user profile
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return $this->errorResponse(
                message: 'Unauthenticated',
                statusCode: Response::HTTP_UNAUTHORIZED
            );
        }

        return $this->successResponse(
            data: $user->only([
                'id',
                'name',
                'phone',
                'phonecode',
                'timezone',
                'status',
                'phone_verified_at',
                'last_login_at',
                'created_at'
            ]),
            message: 'Profile retrieved successfully',
            redirect_url: null,
            statusCode: Response::HTTP_OK
        );
    }

    /**
     * Update user profile
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        try {
            $user = $this->authService->updateProfile(
                $request->user(),
                $request->validated()
            );

            Log::info('Profile updated successfully', [
                'user_id' => $user->id
            ]);

            return $this->successResponse(
                data: $user->only([
                    'id',
                    'name',
                    'phone',
                    'phonecode',
                    'timezone',
                    'status'
                ]),
                message: 'Profile updated successfully',
                redirect_url: null,
                statusCode: Response::HTTP_OK
            );
        } catch (ValidationException $e) {
            return $this->errorResponse(
                message: 'Validation failed',
                statusCode: Response::HTTP_UNPROCESSABLE_ENTITY,
                errors: $e->errors()
            );
        } catch (\Exception $e) {
            Log::error('Failed to update profile', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id ?? null
            ]);

            return $this->errorResponse(
                message: 'Failed to update profile',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
                errors: ['general' => [$e->getMessage()]]
            );
        }
    }

    /**
     * Refresh access token
     */
    public function refreshToken(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return $this->errorResponse(
                    message: 'Unauthenticated',
                    statusCode: Response::HTTP_UNAUTHORIZED
                );
            }

            $user->currentAccessToken()->delete();
            $newToken = $user->createToken('auth_token')->plainTextToken;

            Log::info('Token refreshed successfully', [
                'user_id' => $user->id
            ]);

            return $this->successResponse(
                data: [
                    'access_token' => $newToken,
                    'token_type' => 'Bearer',
                    'expires_in' => config('sanctum.expiration', 525600) * 60
                ],
                message: 'Token refreshed successfully',
                redirect_url: null,
                statusCode: Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('Failed to refresh token', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id ?? null
            ]);

            return $this->errorResponse(
                message: 'Failed to refresh token',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
                errors: ['general' => [$e->getMessage()]]
            );
        }
    }

    /**
     * Revoke all user tokens
     */
    public function revokeAllTokens(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return $this->errorResponse(
                    message: 'Unauthenticated',
                    statusCode: Response::HTTP_UNAUTHORIZED
                );
            }

            $user->tokens()->delete();

            Log::info('All tokens revoked', [
                'user_id' => $user->id
            ]);

            return $this->successResponse(
                data: [],
                message: 'All tokens revoked successfully',
                redirect_url: null,
                statusCode: Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('Failed to revoke tokens', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id ?? null
            ]);

            return $this->errorResponse(
                message: 'Failed to revoke tokens',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
                errors: ['general' => [$e->getMessage()]]
            );
        }
    }
}
