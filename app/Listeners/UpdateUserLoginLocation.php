<?php

namespace App\Listeners;

use App\Events\UserLoggedIn;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateUserLoginLocation implements ShouldQueue
{
    public function handle(UserLoggedIn $event): void
    {
        $user = $event->user;
        $ip = $event->ip;

        Log::info('Login location update started', ['user_id' => $user->id, 'ip' => $ip]);

        // Skip if IP hasn't changed
        if ($user->ip_address === $ip) {
            Log::info('IP unchanged, skipping update', ['user_id' => $user->id]);
            return;
        }

        try {
            $geoData = null;
            $source = null;

            // Try primary API
            try {
                Log::info('Calling primary API', ['api' => 'geoapi.info']);
                $response = Http::timeout(5)->get("https://geoapi.info/api/geo?ip={$ip}");

                if ($response->successful() && is_array($response->json())) {
                    Log::info('Primary API success');
                    $geoData = $response->json();
                    $source = 'primary';
                }
            } catch (\Throwable $e) {
                Log::warning('Primary API failed', ['error' => $e->getMessage()]);
            }

            // Try fallback API if primary failed
            if (!$geoData) {
                try {
                    Log::info('Calling fallback API', ['api' => 'ipapi.co']);
                    $response = Http::timeout(5)->get("https://ipapi.co/{$ip}/json/");

                    if ($response->successful() && is_array($response->json())) {
                        Log::info('Fallback API success');
                        $geoData = $response->json();
                        $source = 'fallback';
                    }
                } catch (\Throwable $e) {
                    Log::warning('Fallback API failed', ['error' => $e->getMessage()]);
                }
            }

            // Prepare update data
            if (!$geoData) {
                Log::info('Using default values');
                $updateData = [
                    'ip_address' => $ip,
                    'country' => 'LOCAL',
                    'country_name' => 'Localhost',
                    'city' => 'Localhost',
                    'timezone' => 'UTC',
                    'latitude' => 0.0,
                    'longitude' => 0.0,
                    'currency' => 'USD',
                    'last_login_at' => now(),
                ];
            } elseif ($source === 'primary') {
                Log::info('Parsing primary API data');
                $location = $geoData['location'] ?? $geoData;
                $coords = $location['coordinates'] ?? [];
                $country = $geoData['countryInfo'] ?? [];

                $updateData = [
                    'ip_address' => $ip,
                    'country' => $location['country'] ?? 'LOCAL',
                    'country_name' => $location['countryName'] ?? 'Unknown',
                    'city' => $location['city'] ?? 'Unknown',
                    'timezone' => $location['timezone'] ?? 'UTC',
                    'latitude' => (float)($coords['latitude'] ?? 0.0),
                    'longitude' => (float)($coords['longitude'] ?? 0.0),
                    'currency' => $country['currencyCode'] ?? 'USD',
                    'last_login_at' => now(),
                ];
            } else {
                Log::info('Parsing fallback API data');
                $updateData = [
                    'ip_address' => $ip,
                    'country' => $geoData['country_code'] ?? 'LOCAL',
                    'country_name' => $geoData['country_name'] ?? 'Unknown',
                    'city' => $geoData['city'] ?? 'Unknown',
                    'timezone' => $geoData['timezone'] ?? 'UTC',
                    'latitude' => (float)($geoData['latitude'] ?? 0.0),
                    'longitude' => (float)($geoData['longitude'] ?? 0.0),
                    'currency' => $geoData['currency'] ?? 'USD',
                    'last_login_at' => now(),
                ];
            }

            // Update user
            $user->update($updateData);

            Log::info('User updated successfully', [
                'user_id' => $user->id,
                'location' => $updateData['city'] . ', ' . $updateData['country']
            ]);
        } catch (\Throwable $e) {
            Log::error('Update failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            // Don't throw - just log error for sync execution
        }
    }
}
