<?php

namespace App\Http\Controllers\Api;

use App\Events\UserLoggedIn;
use App\Http\Controllers\ResponseController;
use App\Http\Requests\Api\{OtpRequest, VerifyOtpRequest, ResendOtpRequest, UpdateProfileRequest};
use App\Models\User;
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
                'phone_masked' => $this->maskPhone($request->input('phonecode') . $request->input('phone')),
                'type' => $request->input('type', 'login'),
                'ip' => $request->ip()
            ]);

            return $this->successResponse(
                data: [
                    'token' => $result['token'],
                    'expires_in' => $result['expires_in'],
                    'can_resend_in' => $result['can_resend_in'] ?? 60
                ],
                message: 'OTP sent successfully to your phone number',
                redirect_url: null,
                statusCode: Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('Failed to send OTP', [
                'error' => $e->getMessage(),
                'phone_masked' => $this->maskPhone($request->input('phonecode') . $request->input('phone')),
                'ip' => $request->ip(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                message: 'Failed to send OTP. Please try again.',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
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

            if (isset($result['forget_password'])) {
                return $this->successResponse(
                    data: $result,
                    message: $result['message'] ?? 'OTP verified successfully',
                    statusCode: Response::HTTP_OK
                );
            }



            Log::info('OTP verified successfully', [
                'user_id' => $result['user']['id'] ?? null,
                'is_new_user' => $result['is_new_user'] ?? false,
                'ip' => $request->ip()
            ]);


            if (isset($result['user']['id'])) {
                $user = User::find($result['user']['id']);
                event(new UserLoggedIn($user, $request->ip()));
                Log::info('UserLoggedIn event dispatched', ['user_id' => $user->id]);
            }

            return $this->successResponse(
                data: [
                    'user' => $result['user'],
                    'access_token' => $result['access_token'],
                    'token_type' => 'Bearer',
                    'is_new_user' => $result['is_new_user'] ?? false,
                ],
                message: $result['message'] ?? 'OTP verified successfully',
                redirect_url: null,
                statusCode: Response::HTTP_OK
            );
        } catch (ValidationException $e) {
            return $this->errorResponse(
                message: 'Verification failed',
                statusCode: Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        } catch (\Exception $e) {
            Log::error('Failed to verify OTP', [
                'error' => $e->getMessage(),
                'token' => substr($request->input('token'), 0, 8) . '...',
                'ip' => $request->ip()
            ]);

            return $this->errorResponse(
                message: 'OTP verification failed',
                statusCode: Response::HTTP_UNPROCESSABLE_ENTITY,
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
                'token' => substr($request->input('token'), 0, 8) . '...',
                'ip' => $request->ip()
            ]);

            return $this->successResponse(
                data: [
                    'token' => $result['token'],
                    'expires_in' => $result['expires_in'],
                    'can_resend_in' => $result['can_resend_in'] ?? 60
                ],
                message: 'OTP has been resent successfully',
                redirect_url: null,
                statusCode: Response::HTTP_OK
            );
        } catch (ValidationException $e) {
            return $this->errorResponse(
                message: $e->getMessage(),
                statusCode: Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        } catch (\Exception $e) {
            Log::error('Failed to resend OTP', [
                'error' => $e->getMessage(),
                'token' => substr($request->input('token'), 0, 8) . '...',
                'ip' => $request->ip()
            ]);

            return $this->errorResponse(
                message: 'Failed to resend OTP',
                statusCode: Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }
    }

    /**
     * Logout user and revoke current token (Sanctum)
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        try {

            if (!$user) {
                return $this->errorResponse(
                    message: 'Unauthenticated.',
                    statusCode: Response::HTTP_UNAUTHORIZED
                );
            }

            // Revoke only the current token
            $request->user()->tokens()->delete();


            return $this->successResponse(
                data: [],
                message: 'Logged out successfully',
                redirect_url: null,
                statusCode: Response::HTTP_OK
            );
        } catch (\Throwable $e) {
            Log::error('Logout failed', [
                'error'   => $e->getMessage(),
                'user_id' => $user?->id,
                'ip'      => $request->ip()
            ]);

            return $this->errorResponse(
                message: 'Logout failed',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
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

            // Delete current token
            $request->user()->currentAccessToken()->delete();

            // Create new token
            $newToken = $user->createToken(
                'auth_token',
                ['*'],
                now()->addMinutes(config('sanctum.expiration', 525600))
            );

            Log::info('Token refreshed successfully', [
                'user_id' => $user->id,
                'ip' => $request->ip()
            ]);

            return $this->successResponse(
                data: [
                    'access_token' => $newToken->plainTextToken,
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
            );
        }
    }



    /**
     * Mask phone number for security
     */
    private function maskPhone(string $phone): string
    {
        if (strlen($phone) <= 4) {
            return str_repeat('*', strlen($phone));
        }

        return substr($phone, 0, 3) . str_repeat('*', strlen($phone) - 6) . substr($phone, -3);
    }





    /**
     * Send OTP to phone number
     */
    // public function resetPassword(OtpRequest $request): JsonResponse
    // {
    //     try {
    //         $result = $this->authService->sendOtp($request->validated());
    //         Log::info('OTP sent successfully', [
    //             'phone_masked' => $this->maskPhone($request->input('phonecode') . $request->input('phone')),
    //             'type' => $request->input('type', 'forget_password'),
    //             'ip' => $request->ip()
    //         ]);

    //         return $this->successResponse(
    //             data: [
    //                 'token' => $result['token'],
    //                 'expires_in' => $result['expires_in'],
    //                 'can_resend_in' => $result['can_resend_in'] ?? 60
    //             ],
    //             message: 'OTP sent successfully to your phone number',
    //             redirect_url: null,
    //             statusCode: Response::HTTP_OK
    //         );
    //     } catch (\Exception $e) {
    //         Log::error('Failed to send OTP', [
    //             'error' => $e->getMessage(),
    //             'phone_masked' => $this->maskPhone($request->input('phonecode') . $request->input('phone')),
    //             'ip' => $request->ip(),
    //             'trace' => $e->getTraceAsString()
    //         ]);

    //         return $this->errorResponse(
    //             message: 'Failed to send OTP. Please try again.',
    //             statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
    //             errors: ['general' => ['An error occurred while sending OTP']]
    //         );
    //     }
    // }
}
