<?php

namespace App\Services;

use App\Contracts\PaymentInterface;
use App\Enums\OrderStatusesEnum;
use App\Events\CanceledPaymentCallbackReceived;
use App\Events\SucceededPaymentCallbackReceived;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
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
use YooKassa\Model\Notification\NotificationEventType;
use YooKassa\Model\Notification\NotificationFactory;

class YookassaService extends PaymentService implements PaymentInterface
{
    private function getClient(): Client
    {
        $client = new Client();
        $client->setAuth(config('services.yookassa.shopId'), config('services.yookassa.apiKey'));
        return $client;
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
    public function createPayment(array $orderData, Order $order, User $user): array
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
                    'return_url' => env('APP_URL') . '/orders',
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
        return ['service' => 'yookassa', 'payment_url' => $response->getConfirmation()->getConfirmationUrl()];
    }

    public function handleCallback(Request $request): string
    {
        try {
            $data = $request->json()->all();
            Log::info('received callback from yookassa', ['data' => $data, 'location' => 'YookassaService->handleCallback']);

            $factory = new NotificationFactory();
            $notificationObject = $factory->factory($data);
            $responseObject = $notificationObject->getObject();

            $client = $this->getClient();

            if (!$client->isNotificationIPTrusted($request->ip())) {
                Log::error('ip is not trusted', ['ip' => $request->ip(), 'location' => 'YookassaService->handleCallback']);
                return 'ERROR';
            }

            if ($notificationObject->getEvent() === NotificationEventType::PAYMENT_SUCCEEDED) {
                $orderId = $responseObject->getMetadata()['order_id'];
                $order = Order::findOrFail($orderId);
                $order->update(['status' => OrderStatusesEnum::PAID]);
                Transaction::query()->create([
                    'order_id' => $orderId,
                    'status' => self::SUCCEEDED,
                    'payment_method' => $responseObject->getPaymentMethod()->getType(),
                    'payment_id' => $responseObject->getId(),
                    'rrn' => $responseObject->getAuthorizationDetails()->getRrn()
                ]);
                event(new SucceededPaymentCallbackReceived($order));
            } elseif ($notificationObject->getEvent() === NotificationEventType::PAYMENT_CANCELED) {
                $orderId = $responseObject->getMetadata()['order_id'];
                $order = Order::findOrFail($orderId);
                $order->update(['status' => OrderStatusesEnum::CANCELLED]);
                Transaction::query()->create([
                    'order_id' => $orderId,
                    'status' => self::CANCELED,
                    'payment_method' => $responseObject->getPaymentMethod()->getType(),
                    'payment_id' => $responseObject->getId(),
                    'description' => $responseObject->getCancellationDetails()->getReason()
                ]);
                event(new CanceledPaymentCallbackReceived($order));
            } else {
                Log::error('payment status not in (succeeded, canceled)', ['location' => 'YookassaService->handleCallback']);
                return 'ERROR';
            }
            Log::info('payment callback successfully handled', ['order_id' => $responseObject->getMetadata()['order_id'], 'location' => 'YookassaService->handleCallback']);
            return 'OK';
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['location' => 'YookassaService->handleCallback', 'data' => $data ?? '']);
            return 'ERROR';
        }
    }
}
