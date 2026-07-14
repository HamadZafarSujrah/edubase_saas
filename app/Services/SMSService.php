<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send an SMS to a phone number.
     * 
     * @param string $phone
     * @param string $message
     * @return bool
     */
    public function send($phone, $message)
    {
        // Basic cleaning of phone number
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Logging the attempt for now (Mock mode)
        Log::info("SMS Attempt to {$phone}: {$message}");

        // Example integration for a generic API (e.g., Twilio or local provider)
        /*
        try {
            $response = Http::post('https://api.sms-provider.com/send', [
                'api_key' => config('services.sms.key'),
                'to' => $phone,
                'message' => $message,
            ]);
            return $response->successful();
        } catch (\Exception $e) {
            Log::error("SMS Failure: " . $e->getMessage());
            return false;
        }
        */

        return true; // Return true as mock for now
    }
}
