<?php

namespace App\Jobs;

use App\Models\OTP;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;

class StoreOTPLogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $queue = 'otp';
    public int $tries = 3;
    public int $timeout = 30;

    public function __construct(
        public array $otpData
    ) {}

    public function handle(): void
    {
        OTP::create([
            'phonecode'  => Arr::get($this->otpData, 'phonecode'),
            'phone'      => Arr::get($this->otpData, 'phone'),
            'otp'        => Arr::get($this->otpData, 'otp'), // hashed OTP
            'token'      => Arr::get($this->otpData, 'token'),
            'type'       => Arr::get($this->otpData, 'type', 'login'),
            'expires_at' => now()->addSeconds(
                Arr::get($this->otpData, 'ttl', config('otp.ttl', 300))
            ),
        ]);

        Log::info('OTP stored in database', [
            'phone' => Arr::get($this->otpData, 'phone'),
            'token' => Arr::get($this->otpData, 'token'),
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::critical('StoreOTPLogJob permanently failed', [
            'phone' => Arr::get($this->otpData, 'phone', 'unknown'),
            'error' => $exception->getMessage(),
        ]);
    }

    public function backoff(): array
    {
        return [10, 30, 60];
    }
}
