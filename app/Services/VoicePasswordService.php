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

    public function getInfo(string $uid): ?Response
    {
        $response = Http::get("$this->url/info/$uid");
        if ($response->successful() && $response['status'] != 'error') {
            Log::info('successful response from voice password', $response->json());
            return $response;
        }

        Log::error('error while calling voice passwrod', $response->json());
        return null;
    }

    public function sendCodeAndSaveInDb($phone): ?array
    {
        $lastSentCode = PhoneVerificationCode::query()->where('phone', $phone)->first();
        if ($lastSentCode !== null) {
            $diff = now()->diffInSeconds($lastSentCode->created_at);
            if ($diff < 60) {
                return ['status' => 'error', 'error' => 'send_code_rate_limit', 'error_message' => 'прошло менее минуты с прошлого звонка', 'seconds_remaining' => 60 - $diff];
            }
        }

//        $response = Http::get("$this->url/voice/$this->apiKey/$phone");
//        if ($response->successful() && $response['status'] != 'error') {
//            $lastSentCode?->delete();
//            PhoneVerificationCode::create([
//                'phone' => $phone,
//                'code' => $response['code'],
//                'expires_at' => now()->addSeconds(180)
//            ]);
//            return ['status' => 'success', 'incoming_call_from' => $response['prefix']];
//        }
        $lastSentCode?->delete();
        PhoneVerificationCode::create([
            'phone' => $phone,
            'code' => 337,
            'expires_at' => now()->addSeconds(180)
        ]);
        return ['status' => 'success', 'incoming_call_from' => '+79148807740'];

//        Log::error('error while calling voice password', ['phone' => $phone, 'response' => $response->json()]);
//        return ['status' => 'error', 'error' => 'unknown_error', 'error_message' => 'Что-то пошло не так, попробуйте еще раз или повторите попытку позже.'];
    }

    public function checkCode($phone, $code): bool
    {
        $phoneVerificationCode = PhoneVerificationCode::where('phone', $phone)->where('code', $code)->firstOrFail();
        if ($phoneVerificationCode->expires_at >= now()) {
            return true;
        }
        return false;
    }

    public function makeCallToManager(): void
    {
        //TODO заменить телефон
        $response = Http::get("$this->url/voice/$this->apiKey/79137098882");
        if ($response->successful() && $response['status'] != 'error') {
            Log::info('successful voice call to manager about new order');
            return;
        }
        Log::error('New order notification. Error while calling voice password', ['manager_phone' => 'phone...', 'response' => $response->json()]);
    }
}
