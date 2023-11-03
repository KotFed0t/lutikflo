<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use YooKassa\Client;
use YooKassa\Common\Exceptions\ApiConnectionException;
use YooKassa\Common\Exceptions\ApiException;
use YooKassa\Common\Exceptions\AuthorizeException;
use YooKassa\Common\Exceptions\BadApiRequestException;
use YooKassa\Common\Exceptions\ExtensionNotFoundException;
use YooKassa\Common\Exceptions\ForbiddenException;
use YooKassa\Common\Exceptions\InternalServerError;
use YooKassa\Common\Exceptions\NotFoundException;
use YooKassa\Common\Exceptions\ResponseProcessingException;
use YooKassa\Common\Exceptions\TooManyRequestsException;
use YooKassa\Common\Exceptions\UnauthorizedException;

class YookassaService
{
    private const PENDING = 'PENDING';
    private const SUCCEEDED = 'SUCCEEDED';
    private const CANCELED = 'CANCELED';

    private function getClient(): Client
    {
        $client = new Client();
        $client->setAuth(config('services.yookassa.shopId'), config('services.yookassa.apiKey'));
        return $client;
    }

    private function createTransaction(int $orderId)
    {
        return Transaction::query()->create([
            'order_id' => $orderId,
            'status' => self::PENDING
        ]);
    }

    /**
     * @throws NotFoundException
     * @throws ApiException
     * @throws ResponseProcessingException
     * @throws BadApiRequestException
     * @throws ExtensionNotFoundException
     * @throws AuthorizeException
     * @throws InternalServerError
     * @throws ForbiddenException
     * @throws TooManyRequestsException
     * @throws ApiConnectionException
     * @throws UnauthorizedException
     */
    public function createPayment(array $orderData, Order $order, User $user): string
    {
        Log::info('start creating payment', ['orderData' => $orderData, 'user' => $user]);
        $client = $this->getClient();
        $idempotenceKey = uniqid('', true);
        $transaction = $this->createTransaction($order->id);
        $response = $client->createPayment(
            [
                'amount' => [
                    'value' => $order->total_price,
                    'currency' => 'RUB',
                ],
                'confirmation' => [
                    'type' => 'redirect',
                    'locale' => 'ru_RU',
                    'return_url' => 'https://merchant-site.ru/return_url',
                ],
                'capture' => true,
                'description' => 'Оплата заказа',
                'metadata' => [
                    'order_id' => $order->id
                ],
                'receipt' => [
                    'customer' => [
                        'email' => $user->email,
                        'phone' => $user->phone,
                    ],
                    'items' => $orderData['yookassaItems']
                ]
            ],
            $idempotenceKey
        );
        Log::info('payment created successfully', ['yookassaResponse' => $response]);
        return $response->getConfirmation()->getConfirmationUrl();
    }
}
