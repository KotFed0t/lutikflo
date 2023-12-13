<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class PaymentService
{
    protected const PENDING = 'PENDING';
    protected const SUCCEEDED = 'SUCCEEDED';
    protected const CANCELED = 'CANCELED';

    protected function createTransaction(int $orderId): Model|Builder
    {
        return Transaction::query()->create([
            'order_id' => $orderId,
            'status' => self::PENDING
        ]);
    }
}
