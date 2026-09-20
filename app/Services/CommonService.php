<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CommonService
{
    protected string $pincodeApiUrl = 'https://api.postalpincode.in/pincode';

    public function lookupPincode(string $pincode): ?array
    {
        if (!preg_match('/^[1-9][0-9]{5}$/', $pincode)) {
            return null;
        }

        return Cache::remember(
            "pincode_{$pincode}",
            now()->addMonths(6),
            fn () => $this->fetchPincodeFromApi($pincode)
        );
    }

    protected function fetchPincodeFromApi(string $pincode): ?array
    {
        try {
            $response = Http::withoutVerifying()
                ->timeout(10)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                    'Accept'     => 'application/json',
                ])
                ->get("{$this->pincodeApiUrl}/{$pincode}");

            if (!$response->successful()) {
                return null;
            }
            $data = $response->json();

            if (empty($data) || !isset($data[0]['Status'])) {
                return null;
            }
            if (strcasecmp($data[0]['Status'], 'Success') !== 0) {
                return null;
            }

            $primaryOffice = $data[0]['PostOffice'][0] ?? null;
            if (!$primaryOffice) {
                return null;
            }

            return [
                'pincode'  => $primaryOffice['Pincode'] ?? $pincode,
                'circle'   => $primaryOffice['Circle'] ?? null,
                'district' => $primaryOffice['District'] ?? null,
                'region'   => $primaryOffice['Region'] ?? null,
                'block'    => $primaryOffice['Block'] ?? null,
                'state'    => $primaryOffice['State'] ?? null,
                'country'  => $primaryOffice['Country'] ?? null,
            ];
        } catch (\Throwable $e) {
            Log::error("Pincode API Exception for {$pincode}: " . $e->getMessage());
            return null;
        }
    }
}