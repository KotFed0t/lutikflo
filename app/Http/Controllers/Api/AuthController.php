<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PhoneVerirficationCodeCheckRequest;
use App\Http\Requests\PhoneVerirficationCodeSendRequest;
use App\Services\AuthService;
use App\Services\VoicePasswordService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AuthController extends Controller
{
    public function sendVoicePasswordCode(PhoneVerirficationCodeSendRequest $request, VoicePasswordService $voicePasswordService)
    {
        $phone = $request->validated()['phone'];
        $result = $voicePasswordService->sendCodeAndSaveInDb($phone);
        if ($result != null) {
            return response()->json(['status' => 'success', 'incoming_call_from' => $result['prefix']]);
        }
        return response()->json(['status' => 'error', 'message' => 'Что-то пошло не так, попробуйте еще раз или повторите попытку позже.']);
    }

    public function checkVoicePasswordCode(PhoneVerirficationCodeCheckRequest $request, VoicePasswordService $voicePasswordService, AuthService $authService)
    {
        $data = $request->validated();
        try {
            if ($voicePasswordService->checkCode($data['phone'], $data['code'])) {
                $authService->loginOrRegister($data['phone']);
                return response()->json(['status' => 'success']);
            }
            return response()->json(['status' => 'error', 'message' => 'срок жизни кода истек'], 401);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['status' => 'error', 'message' => 'введен неверный код'], 401);
        }
    }
}
