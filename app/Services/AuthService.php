<?php

namespace App\Services;

use App\Constants\Constant;
use App\Models\User;
use App\Models\Otp;
use App\Jobs\SendOtpJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\{DB, Redis, Log, Hash};
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthService
{
    private const OTP_PREFIX = 'otp:';
    private const COOLDOWN_PREFIX = 'ratelimit:otp:';
    private const BLOCKED_PREFIX = 'blocked:phone:';

    private const MAX_OTP_ATTEMPTS = 5;
    private const DEFAULT_OTP_TTL = 300;
    private const DEFAULT_COOLDOWN = 60;
    private const DEFAULT_BLOCK_DURATION = 3600;

    /**
     * Send OTP to phone number
     */
    public function sendOtp(array $data): array
    {
        $phone = $this->formatPhone($data['phonecode'], $data['phone']);


        $this->checkIfBlocked($phone);
        $this->checkCooldown($phone);

        $otp = $this->generateOtp();
        $token = (string) Str::uuid();

        $this->storeOtp($token, $data, $otp);
        $this->setCooldown($phone);

        dispatch(new SendOtpJob($phone, $otp, $data['type'] ?? 'login'));

        Log::info('OTP sent', ['phone' => $phone, 'token' => $token]);

        return [
            'token' => $token,
            'expires_in' => config('otp.ttl', self::DEFAULT_OTP_TTL)
        ];
    }

    /**
     * Verify OTP and authenticate user
     */
    public function verifyOtp(array $data, string $ip, ?string $userAgent): array
    {
        $otpData = $this->validateOtp($data);


        $phone = $this->formatPhone($otpData['phonecode'], $otpData['phone']);

        return DB::transaction(function () use ($otpData, $phone, $ip, $userAgent) {
            $user = User::where('phonecode', $otpData['phonecode'])
                ->where('phone', $otpData['phone'])
                ->first();

            $isNewUser = !$user;

            if ($isNewUser) {
                $user = $this->createUser($otpData);
            } else {
                $this->verifyUserPhone($user);
            }

            $this->updateUserLoginData($user, $otpData, $ip, $userAgent);
            $this->clearBlock($phone);

            $token = $user->createToken(
                'auth_token',
                ['*'],
                now()->addMinutes(config('sanctum.expiration', 525600))
            );

            Log::info('User authenticated', ['user_id' => $user->id, 'new' => $isNewUser]);

            return [
                'message' => $isNewUser ? 'Registration successful' : 'Login successful',
                'user' => $user->only(['id', 'name', 'phone', 'phonecode', 'timezone', 'status']),
                'access_token' => $token->plainTextToken,
                'is_new_user' => $isNewUser
            ];
        });
    }

    /**
     * Resend OTP
     */
    public function resendOtp(string $token): array
    {
        $key = self::OTP_PREFIX . $token;
        $data = Redis::hgetall($key);

        if (!$data) {
            throw ValidationException::withMessages([
                'token' => ['OTP session expired. Please request a new OTP.']
            ]);
        }

        Redis::del($key);
        Otp::where('token', $token)->delete();

        return $this->sendOtp([
            'phonecode' => $data['phonecode'],
            'phone' => $data['phone'],
            'type' => $data['type'] ?? 'login',
            'name' => $data['name'] ?? null,
            'timezone' => $data['timezone'] ?? 'UTC',
            'device_type' => $data['device_type'] ?? null,
            'device_token' => $data['device_token'] ?? null
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
    private function validateOtp(array $requestData): array
    {
        $key = self::OTP_PREFIX . $requestData['token'];
        $data = Redis::hgetall($key);

        if (!$data) {
            throw ValidationException::withMessages([
                'otp' => ['OTP has expired. Please request a new one.']
            ]);
        }

        $attempts = (int) ($data['attempts'] ?? 0);

        if ($attempts >= self::MAX_OTP_ATTEMPTS) {
            Redis::del($key);
            $this->blockPhone($this->formatPhone($data['phonecode'], $data['phone']));

            throw ValidationException::withMessages([
                'otp' => ['Maximum attempts exceeded. Phone temporarily blocked.']
            ]);
        }

        if (!Hash::check($requestData['otp'], $data['otp'])) {
            $remaining = self::MAX_OTP_ATTEMPTS - Redis::hincrby($key, 'attempts', 1);

            throw ValidationException::withMessages([
                'otp' => ["Invalid OTP. {$remaining} attempts remaining."]
            ]);
        }

        Redis::del($key);

        Otp::where('token', $requestData['token'])
            ->update([
                'is_verified' => true,
                'verified_at' => now(),
                'attempts' => DB::raw('attempts + 1')
            ]);

        return array_merge($data, [
            'device_type' => $requestData['device_type'] ?? null,
            'device_token' => $requestData['device_token'] ?? null,
            'timezone' => $requestData['timezone'] ?? null
        ]);
    }

    /**
     * Store OTP in Redis and Database
     */
    private function storeOtp(string $token, array $data, string $otp): void
    {
        $key = self::OTP_PREFIX . $token;
        $ttl = config('otp.ttl', self::DEFAULT_OTP_TTL);
        $hashedOtp = Hash::make($otp);

        DB::transaction(function () use ($key, $data, $hashedOtp, $ttl, $token) {
            Redis::hMSet($key, [
                'phonecode' => $data['phonecode'],
                'phone' => $data['phone'],
                'full_phone' => $data['phonecode'] . $data['phone'],
                'otp' => $hashedOtp,
                'type' => $data['type'] ?? 'login',
                'name' => $data['name'] ?? '',
                'attempts' => 0,
                'created_at' => now()->toDateTimeString(),
            ]);

            Redis::expire($key, $ttl);

            Otp::create([
                'phonecode' => $data['phonecode'],
                'phone' => $data['phone'],
                'otp' => $hashedOtp,
                'type' => $data['type'] ?? 'login',
                'expires_at' => now()->addSeconds($ttl),
                'token' => $token,
                'full_phone' => $data['phonecode'] . $data['phone'],
                'is_verified' => false,
                'attempts' => 0
            ]);
        });
    }

    /**
     * Create new user
     */
    private function createUser(array $data): User
    {
        return User::create([
            'name' => $data['name'] ?? 'User',
            'phonecode' => $data['phonecode'],
            'phone' => $data['phone'],
            'phone_verified_at' => now(),
            'status' => Constant::ACTIVE,
            'timezone' => $data['timezone'] ?? 'UTC'
        ]);
    }

    /**
     * Verify user phone if not verified
     */
    private function verifyUserPhone(User $user): void
    {
        if (!$user->phone_verified_at) {
            $user->phone_verified_at = now();
            $user->save();
        }
    }

    /**
     * Update user login data
     */
    private function updateUserLoginData(User $user, array $data, string $ip, ?string $userAgent): void
    {
        $user->update([
            'last_login_at' => now(),
            'ip_address' => $ip,
            'timezone' => $data['timezone'] ?? $user->timezone ?? 'UTC',
            'device_type' => $data['device_type'] ?? null,
            'device_token' => $data['device_token'] ?? null,
            'user_agent' => $userAgent
        ]);
    }

    /**
     * Format phone number
     */
    private function formatPhone(string $code, string $number): string
    {
        return $code . $number;
    }

    /**
     * Check if phone is in cooldown
     */
    private function checkCooldown(string $phone): void
    {
        $key = self::COOLDOWN_PREFIX . $phone;
        $ttl = Redis::ttl($key);

        if ($ttl > 0) {
            throw ValidationException::withMessages([
                'phone' => ["Please wait {$ttl} seconds before requesting another OTP."]
            ]);
        }
    }

    /**
     * Set cooldown period
     */
    private function setCooldown(string $phone): void
    {
        Redis::setex(
            self::COOLDOWN_PREFIX . $phone,
            config('otp.cooldown', self::DEFAULT_COOLDOWN),
            1
        );
    }

    /**
     * Check if phone is blocked
     */
    private function checkIfBlocked(string $phone): void
    {
        $key = self::BLOCKED_PREFIX . $phone;
        $ttl = Redis::ttl($key);

        if ($ttl > 0) {
            throw ValidationException::withMessages([
                'phone' => ['Phone is blocked. Try again in ' . ceil($ttl / 60) . ' minutes.']
            ]);
        }
    }

    /**
     * Block phone number
     */
    private function blockPhone(string $phone): void
    {
        Redis::setex(
            self::BLOCKED_PREFIX . $phone,
            config('otp.block_duration', self::DEFAULT_BLOCK_DURATION),
            1
        );

        Log::warning('Phone blocked', ['phone' => $phone]);
    }

    /**
     * Clear phone block
     */
    private function clearBlock(string $phone): void
    {
        Redis::del(self::BLOCKED_PREFIX . $phone);
    }

    /**
     * Generate OTP
     */
    private function generateOtp(): string
    {
        if (app()->environment('local', 'testing')) {
            return config('otp.test_otp', '123456');
        }

        $length = config('otp.length', 6);

        return str_pad(
            (string) random_int(0, (int) str_repeat('9', $length)),
            $length,
            '0',
            STR_PAD_LEFT
        );
    }
}
