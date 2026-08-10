<?php

namespace App\Services;

use App\Models\Communication\SMSLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send an SMS to a phone number.
     *
     * MOCK MODE: no real SMS gateway is wired up yet -- this logs the
     * attempt and always reports success. Swap the commented-out HTTP call
     * below for a real provider when credentials are available; every
     * caller goes through this one method, so that's the only place that
     * needs to change.
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

    /**
     * Send (via the mock above) and persist an SMSLog row in one call, so
     * every SMS-sending feature (Blaster, attendance/result notifications)
     * shows up consistently in SMS Report / Family SMS Report.
     *
     * @param array $data tenant_id, phone, message, type (fee|attendance|result|blast),
     *                     student_id (nullable), recipient_name (nullable), sent_by (nullable)
     */
    public function sendAndLog(array $data): SMSLog
    {
        $sent = $this->send($data['phone'], $data['message']);

        return SMSLog::create([
            'tenant_id'      => $data['tenant_id'],
            'student_id'     => $data['student_id'] ?? null,
            'phone'          => preg_replace('/[^0-9]/', '', $data['phone']),
            'recipient_name' => $data['recipient_name'] ?? null,
            'message'        => $data['message'],
            'type'           => $data['type'],
            'status'         => $sent ? 'sent' : 'failed',
            'gateway'        => 'mock',
            'sent_by'        => $data['sent_by'] ?? null,
            'sent_at'        => now(),
        ]);
    }
}
