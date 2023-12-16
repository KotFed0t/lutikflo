<?php

namespace App\Services;

use App\Contracts\PaymentInterface;
use App\Enums\OrderStatusesEnum;
use App\Events\SucceededPaymentCallbackReceived;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class RobokassaService extends PaymentService implements PaymentInterface
{
    public function createPayment(array $orderData, Order $order, User $user): array
    {
        Log::info('start creating payment', ['orderData' => $orderData, 'user' => $user]);
        $transaction = $this->createTransaction($order->id);
        $mrh_login = config('services.robokassa.shop_id');
        $mrh_pass1 = config('services.robokassa.pswd_1');
        $inv_id = $order->id;
        $inv_desc = "Оплата заказа";
        $out_sum = $order->total_price;
        $email = $user->email;
        $expirationDate = str_replace(' ', 'T', now()->addMinutes(11)->format('Y-m-d H:i'));
        $receipt = rawurlencode(json_encode([
            'sno' => 'patent',
            'items' => $orderData['robokassaItems']
        ]));
        $signatureValue = md5("$mrh_login:$out_sum:$inv_id:$receipt:$mrh_pass1");

        $paymentData = [
            'mrh_login' => $mrh_login,
            'inv_id' => $inv_id,
            'inv_desc' => $inv_desc,
            'out_sum' => $out_sum,
            'receipt' => $receipt,
            'signatureValue' => $signatureValue,
            'email' => $email,
            'expirationDate' => $expirationDate
        ];

        Log::info('payment data created successfully', ['paymentData' => $paymentData]);

        return [
            'service' => 'robokassa',
            'payment_data' => $paymentData
        ];
    }

    public function handleCallback(Request $request): string
    {
        try {
            Log::info('received callback from robokassa', ['data' => $request->all(), 'location' => 'RobokassaService->handleCallback']);
            // your registration data
            $mrh_pass2 = config('services.robokassa.pswd_2');   // merchant pass2 here

            // HTTP parameters:
            $out_summ = $request->get("OutSum");
            $inv_id = $request->get("InvId");
            $crc = strtoupper($request->get("SignatureValue"));

            // build own CRC
            $my_crc = strtoupper(md5("$out_summ:$inv_id:$mrh_pass2"));

            if ($my_crc !== $crc) {
                Log::error('signature value doesnt match', ['location' => 'RobokassaService->handleCallback', 'data' => $request->all()]);
                return 'ERROR';
            }

            $order = Order::findOrFail($inv_id);
            $order->update(['status' => OrderStatusesEnum::PAID]);
            Transaction::query()->create([
                'order_id' => $inv_id,
                'status' => self::SUCCEEDED,
//                'payment_method' => $responseObject->getPaymentMethod()->getType(),
//                'payment_id' => $responseObject->getId(),
//                'rrn' => $responseObject->getAuthorizationDetails()->getRrn()
            ]);
            event(new SucceededPaymentCallbackReceived($order));

            return "OK$inv_id";
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['location' => 'RobokassaService->handleCallback', 'data' => $request->all()]);
            return 'ERROR';
        }
    }
}
