<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\YookassaService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function handleCallback(Request $request, YookassaService $yookassaService): JsonResponse
    {
        $yookassaService->handleCallback($request);
        return response()->json();
    }
}
