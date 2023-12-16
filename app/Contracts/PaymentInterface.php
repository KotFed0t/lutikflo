<?php

namespace App\Contracts;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

interface PaymentInterface
{
    public function createPayment(array $orderData, Order $order, User $user) : array;
    public function handleCallback(Request $request) : string;
}
