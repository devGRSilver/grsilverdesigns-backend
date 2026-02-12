<?php
// app/Jobs/SendOTPJob.php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendOTPJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 30;
    public $backoff = [60, 120, 300];

    public function __construct(
        public string $phone,
        public string $otp,
        public string $type = 'login'
    ) {}

    public function handle(): void
    {
        try {
            // TODO: Integrate with your SMS service provider
            // Example with Twilio:
            // $twilio = new Client(config('services.twilio.sid'), config('services.twilio.token'));
            // $twilio->messages->create(
            //     $this->phone,
            //     [
            //         'from' => config('services.twilio.phone'),
            //         'body' => "Your OTP for {$this->type} is: {$this->otp}. Valid for 10 minutes."
            //     ]
            // );

            // For now, log the OTP (remove in production)
            Log::info("OTP sent to {$this->phone}: {$this->otp} for {$this->type}");

            // Simulate SMS sending delay
            sleep(1);
        } catch (\Exception $e) {
            Log::error("Failed to send OTP to {$this->phone}: " . $e->getMessage());
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("SendOTPJob failed for phone {$this->phone}: " . $exception->getMessage());
    }
}
