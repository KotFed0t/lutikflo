<?php

namespace App\Services;

use App\Models\PhoneVerificationCode;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VoicePasswordService
{
    private string $url;
    private string $apiKey;

    public function __construct()
    {
        $this->url = config('services.voicePassword.url');
        $this->apiKey = config('services.voicePassword.apiKey');
    }

    public function getInfo(string $uid)
    {
        $response = Http::get("$this->url/info/$uid");
        if ($response->successful() && $response['status'] != 'error') {
            Log::info('successful response from voice password', $response->json());
            return $response;
        }

        Log::error('error while calling voice passwrod', $response->json());
        return null;
    }

    public function sendCodeAndSaveInDb($phone): ?Response
    {
        $response = Http::get("$this->url/voice/$this->apiKey/$phone");
        if ($response->successful() && $response['status'] != 'error') {
            PhoneVerificationCode::where('phone', $phone)->delete();
            PhoneVerificationCode::create([
                'phone' => $phone,
                'code' => $response['code'],
                'expires_at' => now()->addSeconds(180)
            ]);
            return $response;
        }

        Log::error('error while calling voice password', ['phone' => $phone, 'response' => $response->json()]);
        return null;
    }

    public function checkCode($phone, $code): bool
    {
        $phoneVerificationCode = PhoneVerificationCode::where('phone', $phone)->where('code', $code)->firstOrFail();
        if ($phoneVerificationCode->expires_at >= now()) {
            return true;
        }
        return false;
    }
}
