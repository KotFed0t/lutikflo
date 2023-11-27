<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatusesEnum;
use App\Exceptions\CartValidationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderCreateRequest;
use App\Models\DeliveryPriceSetting;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use App\Services\TomTomApiService;
use App\Services\YookassaService;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function getDeliveryPrice(Request $request, TomTomApiService $tomTomApiService)
    {
        $deliveryPrice = $tomTomApiService->getDeliveryPrice($request->get('latitude'), $request->get('longitude'));
        if ($deliveryPrice === null) {
            return response()->json(['error_message' => 'Не удалось вычислить цену доставки, попробуйте ввести адрес еще раз.'], 500);
        }
        return response()->json(['price' => $deliveryPrice]);
    }

    public function getUserOrders()
    {
        //TODO создать Resource
        return auth()->user()->orders;
    }

    public function getUserOrderDetails($id)
    {
        //TODO создать Resource
        $order = auth()->user()->orders->find($id);
        if ($order !== null) {
            return $order->products;
        }
        return response()->json(['error_message' => 'Заказ не найден'], 404);
    }

    public function createOrder(OrderCreateRequest $request, TomTomApiService $tomTomApiService, YookassaService $yookassaService)
    {
        $cartData = $request->validated()['cart'];
        $formData = $request->validated()['form_data'];
        $user = auth()->user();
        $orderService = null;
        $orderData = null;
        $paymentUrl = null;

        Log::info('getting delivery price in createOrder', [
            'user' => $user,
            'delivery_address' => $formData['delivery_address'],
            'delivery_address_latitude' => $formData['delivery_address_latitude'],
            'delivery_address_longitude' => $formData['delivery_address_longitude']
        ]);

        $deliveryPrice = $tomTomApiService->getDeliveryPrice(
            $formData['delivery_address_latitude'],
            $formData['delivery_address_longitude']
        );
        if ($deliveryPrice === null) {
            return response()->json([
                'error' => 'deliveryPriceError',
                'error_message' => 'Не удалось вычислить цену доставки, попробуйте ввести адрес еще раз.'
            ], 500);
        }

        try {
            $orderService = new OrderService($cartData);
            $orderData = $orderService->getOrderData($deliveryPrice);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'productNotFoundError',
                'error_message' => 'Не удалось найти некоторые товары',
                'invalid_products_id' => array_values($e->getIds())
            ], 400);
        } catch (CartValidationException $e) {
            return response()->json([
                'error' => 'cartValidationError',
                'error_message' => $e->getMessage(),
                'invalid_products_id' => $e->getOptions()
            ], 400);
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['user' => $user, 'location' => 'createOrder->while getting order data from OrderService']);
            return response()->json([
                'error' => 'unknownError',
                'error_message' => 'Что-то пошло пошло не так...'
            ], 500);
        }

        DB::beginTransaction();
        try {
            if (isset($formData['name']))
                $user->name = $formData['name'];

            if (isset($formData['email']))
                $user->email = $formData['email'];

            $user->save();

            $order = $orderService->createOrder($orderData, $formData, $user->id);
            $paymentUrl = $yookassaService->createPayment($orderData, $order, $user);
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            Log::error($e->getMessage(), ['user' => $user, 'location' => 'createOrder->DB::BeginTransaction']);
            return response()->json([
                'error' => 'unknownError',
                'error_message' => 'Что-то пошло пошло не так...'
            ], 500);
        }

        return response()->json(['payment_url' => $paymentUrl]);
    }
}
