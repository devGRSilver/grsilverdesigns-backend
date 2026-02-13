<?php

namespace App\Services;

use App\Constants\Constant;
use App\Models\User;
use App\Models\Otp;
use App\Jobs\SendOtpJob;
use Illuminate\Support\Facades\{DB, Cache, Log, Hash};
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthService
{


    public function sendOtp(array $data): array
    {
        $phone = $this->formatPhone($data['phonecode'], $data['phone']);

        // Check if phone is blocked
        $this->checkIfBlocked($phone);

        // Generate OTP and token
        $otp = $this->generateOtp();
        $token = (string) Str::uuid();

        // Store OTP
        $this->storeOtp($token, $data, $otp);

        // Send OTP asynchronously
        dispatch(new SendOtpJob($phone, $otp, $data['type'] ?? 'login'))->onQueue('high');

        Log::info('OTP sent', ['phone' => $this->maskPhone($phone)]);

        return [
            'token' => $token,
            'expires_in' => Constant::OTP_TTL
        ];
    }

    /**
     * Verify OTP and authenticate user
     */
    public function verifyOtp(array $data, string $ip, ?string $userAgent): array
    {
        $otpData = $this->validateOtp($data, $ip);
        $phone = $this->formatPhone($otpData['phonecode'], $otpData['phone']);

        return DB::transaction(function () use ($otpData, $phone, $ip, $userAgent) {
            $user = User::where('phone', $otpData['phone'])
                ->lockForUpdate()
                ->first();

            $isNewUser = !$user;

            if ($isNewUser) {
                $user = $this->createUser($otpData);
            } else {
                $this->checkUserStatus($user);
                $this->verifyUserPhone($user);
            }

            $this->updateUserLoginData($user, $otpData, $ip, $userAgent);
            $this->clearBlock($phone);

            // Create access token
            $token = $user->createToken('auth_token')->plainTextToken;


            Log::info('User authenticated', ['user_id' => $user->id, 'is_new' => $isNewUser]);

            return [
                'message' => $isNewUser ? 'Registration successful' : 'Login successful',
                'user' => $user->only(['id', 'name', 'phone', 'phonecode', 'timezone', 'status']),
                'access_token' => $token,
                'is_new_user' => $isNewUser
            ];
        });
    }

    /**
     * Resend OTP
     */
    public function resendOtp(string $token): array
    {
        $key = Constant::OTP_PREFIX . $token;
        $data = Cache::get($key);

        if (!$data) {
            throw ValidationException::withMessages([
                'token' => ['OTP session expired. Please request a new OTP.']
            ]);
        }

        $resendCount = (int) ($data['resend_count'] ?? 0);
        if ($resendCount >= 3) {
            throw ValidationException::withMessages([
                'token' => ['Maximum resend attempts exceeded.']
            ]);
        }

        Cache::forget($key);
        Otp::where('token', $token)->delete();

        return $this->sendOtp([
            'phonecode' => $data['phonecode'],
            'phone' => $data['phone'],
            'type' => $data['type'] ?? 'login',
            'name' => $data['name'] ?? null,
            'timezone' => $data['timezone'] ?? 'UTC',
            'resend_count' => $resendCount + 1
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(User $user, array $data): User
    {
        unset($data['phone'], $data['phonecode'], $data['status'], $data['phone_verified_at']);

        $user->update($data);

        Log::info('Profile updated', ['user_id' => $user->id]);

        return $user->fresh();
    }

    /**
     * Validate OTP
     */
    private function validateOtp(array $requestData, string $ip): array
    {
        $key = Constant::OTP_PREFIX . $requestData['token'];
        $data = Cache::get($key);

        if (!$data) {
            throw ValidationException::withMessages([
                'otp' => ['OTP has expired. Please request a new one.']
            ]);
        }

        $attempts = (int) ($data['attempts'] ?? 0);

        // Check max attempts
        if ($attempts >= Constant::MAX_OTP_ATTEMPTS) {
            Cache::forget($key);
            $this->blockPhone($this->formatPhone($data['phonecode'], $data['phone']));

            throw ValidationException::withMessages([
                'otp' => ['Maximum attempts exceeded. Phone blocked for 1 hour.']
            ]);
        }

        // Verify OTP
        if (!Hash::check($requestData['otp'], $data['otp'])) {
            $data['attempts'] = $attempts + 1;
            Cache::put($key, $data, Constant::OTP_TTL);

            $remaining = Constant::MAX_OTP_ATTEMPTS - $data['attempts'];
            throw ValidationException::withMessages([
                'otp' => ["Invalid OTP. {$remaining} attempt(s) remaining."]
            ]);
        }

        // Success - clear cache
        Cache::forget($key);

        // Update database
        Otp::where('token', $requestData['token'])->update([
            'is_verified' => true,
            'verified_at' => now(),
            'attempts' => $attempts + 1
        ]);

        return array_merge($data, [
            'device_type' => $requestData['device_type'] ?? null,
            'device_token' => $requestData['device_token'] ?? null,
            'timezone' => $requestData['timezone'] ?? $data['timezone'] ?? 'UTC'
        ]);
    }

    /**
     * Store OTP in cache and database
     */
    private function storeOtp(string $token, array $data, string $otp): void
    {
        $hashedOtp = Hash::make($otp);
        $expiresAt = now()->addSeconds(Constant::OTP_TTL);

        // Store in cache
        Cache::put(
            Constant::OTP_PREFIX . $token,
            [
                'phonecode' => $data['phonecode'],
                'phone' => $data['phone'],
                'full_phone' => $data['phonecode'] . $data['phone'],
                'otp' => $hashedOtp,
                'type' => $data['type'] ?? 'login',
                'name' => $data['name'] ?? '',
                'timezone' => $data['timezone'] ?? 'UTC',
                'attempts' => 0,
                'resend_count' => $data['resend_count'] ?? 0,
                'created_at' => now()->toDateTimeString(),
            ],
            Constant::OTP_TTL
        );

        // Store in database
        Otp::create([
            'phonecode' => $data['phonecode'],
            'phone' => $data['phone'],
            'otp' => $hashedOtp,
            'type' => $data['type'] ?? 'login',
            'expires_at' => $expiresAt,
            'token' => $token,
            'full_phone' => $data['phonecode'] . $data['phone'],
            'is_verified' => false,
            'attempts' => 0
        ]);
    }

    /**
     * Create new user
     */
    private function createUser(array $data): User
    {
        return User::create([
            'name' => 'GR User',
            'phonecode' => $data['phonecode'],
            'phone' => $data['phone'],
            'phone_verified_at' => now(),
            'status' => Constant::ACTIVE,
        ]);
    }

    /**
     * Verify user phone
     */
    private function verifyUserPhone(User $user): void
    {
        if (!$user->phone_verified_at) {
            $user->update(['phone_verified_at' => now()]);
        }
    }

    /**
     * Update user login data
     */
    private function updateUserLoginData(User $user, array $data, string $ip, ?string $userAgent): void
    {
        $user->update([
            'last_login_at' => now(),
            'timezone' => $data['timezone'] ?? $user->timezone ?? 'UTC',
            'device_type' => $data['device_type'] ?? null,
            'device_token' => $data['device_token'] ?? null,
            'user_agent' => $userAgent
        ]);
    }

    /**
     * Check if phone is blocked
     */
    private function checkIfBlocked(string $phone): void
    {
        $key = Constant::BLOCKED_PREFIX . $phone;

        if (Cache::has($key)) {
            $ttl = Cache::get($key . ':ttl', 60);
            throw ValidationException::withMessages([
                'phone' => ['Phone blocked. Try again in ' . ceil($ttl / 60) . ' minute(s).']
            ]);
        }
    }

    /**
     * Block phone number
     */
    private function blockPhone(string $phone): void
    {
        $key = Constant::BLOCKED_PREFIX . $phone;

        Cache::put($key, true, Constant::BLOCK_DURATION);
        Cache::put($key . ':ttl', Constant::BLOCK_DURATION, Constant::BLOCK_DURATION);

        Log::warning('Phone blocked', ['phone' => $this->maskPhone($phone)]);
    }

    /**
     * Clear phone block
     */
    private function clearBlock(string $phone): void
    {
        Cache::forget(Constant::BLOCKED_PREFIX . $phone);
    }

    /**
     * Check user status
     */
    private function checkUserStatus(User $user): void
    {
        if (in_array($user->status, [Constant::IN_ACTIVE])) {
            throw ValidationException::withMessages([
                'phone' => ['Account suspended. Contact support.']
            ]);
        }
    }

    /**
     * Format phone number (numeric only, no +)
     */
    private function formatPhone(string $code, string $number): string
    {
        // Remove all non-digits
        $code   = preg_replace('/\D/', '', $code);
        $number = preg_replace('/\D/', '', $number);

        // Remove leading zero from number
        $number = ltrim($number, '0');

        $phone = $code . $number;

        // Validate length (E.164 max 15 digits)
        if (!preg_match('/^\d{8,15}$/', $phone)) {
            throw new \InvalidArgumentException('Invalid phone number format.');
        }

        return $phone;
    }


    /**
     * Generate OTP
     */
    private function generateOtp(): string
    {
        if (app()->environment('local', 'testing')) {
            return config('otp.test_otp', '123456');
        }

        $max = (int) str_repeat('9', Constant::OTP_LENGTH);
        return str_pad(
            (string) random_int(0, $max),
            Constant::OTP_LENGTH,
            '0',
            STR_PAD_LEFT
        );
    }

    /**
     * Mask phone number for logging
     */
    private function maskPhone(string $phone): string
    {
        if (strlen($phone) <= 4) {
            return str_repeat('*', strlen($phone));
        }

        return substr($phone, 0, 3) . str_repeat('*', strlen($phone) - 6) . substr($phone, -3);
    }
}
