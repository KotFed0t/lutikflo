<?php

namespace App\Contracts;

use App\Models\Order;
use App\Models\User;

interface PaymentInterface
{
    public function createPayment(array $orderData, Order $order, User $user) : array;
}
