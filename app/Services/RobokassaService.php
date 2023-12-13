<?php

namespace App\Services;

use App\Contracts\PaymentInterface;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;


class RobokassaService extends PaymentService implements PaymentInterface
{
    public function createPayment(array $orderData, Order $order, User $user): array
    {
        return [];
    }

    public function handleCallback(Request $request): void
    {
        return;
    }
}
