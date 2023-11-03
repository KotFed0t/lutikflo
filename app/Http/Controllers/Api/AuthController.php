<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PhoneVerirficationCodeCheckRequest;
use App\Http\Requests\PhoneVerirficationCodeSendRequest;
use App\Models\PhoneVerificationCode;
use App\Models\User;
use App\Services\AuthService;
use App\Services\VoicePasswordService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AuthController extends Controller
{
    public function sendVoicePasswordCode(PhoneVerirficationCodeSendRequest $request, VoicePasswordService $voicePasswordService): JsonResponse
    {
        $phone = $request->validated()['phone'];
//        $result = $voicePasswordService->sendCodeAndSaveInDb($phone);
//        if ($result != null) {
//            return response()->json(['status' => 'success', 'incoming_call_from' => $result['prefix']]);
//        }
//        return response()->json(['status' => 'error', 'message' => 'Что-то пошло не так, попробуйте еще раз или повторите попытку позже.']);

        //для тестов
        PhoneVerificationCode::where('phone', $phone)->delete();
        PhoneVerificationCode::create([
            'phone' => $phone,
            'code' => 337,
            'expires_at' => now()->addSeconds(180)
        ]);

        return response()->json(['status' => 'success', 'incoming_call_from' => '79996658789']);
    }

    public function checkVoicePasswordCode(PhoneVerirficationCodeCheckRequest $request, VoicePasswordService $voicePasswordService, AuthService $authService): JsonResponse
    {
        $data = $request->validated();
        try {
            if ($voicePasswordService->checkCode($data['phone'], $data['code'])) {
                $authService->loginOrRegister($data['phone']);
                return response()->json(['status' => 'success']);
            }
            return response()->json(['status' => 'error', 'message' => 'срок жизни кода истек'], 400);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['status' => 'error', 'message' => 'введен неверный код'], 400);
        }
    }

    public function test()
    {
        $user = User::where('phone', '79137098882')->first();
        Auth::login($user);
        return response()->json(['status' => 'success', 'user' => $user]);
    }

    public function logout(Request $request)
    {
        //TODO вынести в сервис auth и дополнить его реквестом чтобы перегенерировать сессию при логине
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['status' => 'success', 'message' => 'logout success']);
    }
}
