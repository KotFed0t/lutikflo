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
        $result = $voicePasswordService->sendCodeAndSaveInDb($phone);
        if ($result['status'] === 'success') {
            return response()->json($result);
        }
        return response()->json($result, 400);
    }

    public function checkVoicePasswordCode(PhoneVerirficationCodeCheckRequest $request, VoicePasswordService $voicePasswordService, AuthService $authService): JsonResponse
    {
        $data = $request->validated();
        try {
            if ($voicePasswordService->checkCode($data['phone'], $data['code'])) {
                $authService->loginOrRegister($data['phone']);
                return response()->json(['status' => 'success']);
            }
            return response()->json(['status' => 'error', 'error' => 'code_expired','message' => 'срок жизни кода истек'], 400);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['status' => 'error', 'error' => 'incorrect_code','message' => 'введен неверный код'], 400);
        }
    }

    public function logout(AuthService $authService): JsonResponse
    {
        $authService->logout();
        return response()->json(['status' => 'success', 'message' => 'logout success']);
    }
}
