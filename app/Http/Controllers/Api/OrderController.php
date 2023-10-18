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
        $distance = $tomTomApiService->getRouteDistanceKm($request->get('latitude'), $request->get('longitude'));
        if ($distance !== null) {
            $deliveryPriceSettings = DeliveryPriceSetting::first();
            $deliveryPrice = $deliveryPriceSettings->getPriceByDistance($distance);
            return response()->json(['price' => $deliveryPrice, 'distance' => $distance]);
        }
        return response()->json(['error_message' => 'Что-то пошло не так...'], 500);
    }

    public function getTest()
    {
        $order = Order::create([
            'user_id' => 1,
            'delivery_address' => 'Пушкина, дом Щшотарки 5',
            'delivery_date_time' => '2023-10-12 11:12:00',
            'total_price' => 1000,
            'delivery_price' => 500,
            'status' => 3
        ]);
//        $order = Order::find(4);
//        return $order->delivery_date_time;
//        return $order;
    }

    public function createOrder(OrderCreateRequest $request, TomTomApiService $tomTomApiService, YookassaService $yookassaService)
    {
        //TODO подчистить метод контроллера getDeliveryPrice
        //TODO TomTomApiService бросать exception или так и отдавать null?
        //TODO настроить логирование с привязкой к номеру или id какому-нибудь
        $cart_data = $request->validated()['cart'];
        $formData = $request->validated()['form_data'];
        $user = auth()->user();
        $orderService = null;
        $orderData = null;
        $paymentUrl = null;

        $deliveryPrice = $tomTomApiService->getDeliveryPrice(
            $formData['delivery_address_latitude'],
            $formData['delivery_address_longitude']
        );
        if ($deliveryPrice === null) {
            return response()->json(['error_message' => 'Не удалось вычислить цену доставки, попробуйте ввести адрес еще раз.'], 500);
        }

        try {
            $orderService = new OrderService($cart_data);
            $orderData = $orderService->getOrderData($deliveryPrice);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error_message' => 'Не удалось найти некоторые товары',
                'products_id' => array_values($e->getIds())
            ], 400);
            //добавить логировние
        } catch (CartValidationException $e) {
            return response()->json([
                'error_message' => $e->getMessage(),
                'options' => $e->getOptions()
            ], 400);
            //добавить логирование
        } catch (Exception $e) {
            return response()->json(['error_message' => 'Что-то пошло пошло не так...'], 500);
        }

        DB::beginTransaction();
        try {
            $order = $orderService->createOrder($orderData, $formData, $user->id);
            $paymentUrl = $yookassaService->createPayment($orderData, $order, $user);
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            Log::error($e->getMessage(), ['user' => $user]);
            return response()->json(['error_message' => 'Что-то пошло пошло не так...'], 500);
        }

        return $paymentUrl;
    }
}
