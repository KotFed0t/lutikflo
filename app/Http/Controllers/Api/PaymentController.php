<?php

namespace App\Http\Controllers\Api;

use App\Contracts\PaymentInterface;
use App\Http\Controllers\Controller;
use App\Services\YookassaService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function handleCallback(Request $request, PaymentInterface $paymentInterface): \Illuminate\Foundation\Application|Response|Application|ResponseFactory
    {
        $res = $paymentInterface->handleCallback($request);
        return response($res);
    }
}
