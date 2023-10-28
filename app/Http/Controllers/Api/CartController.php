<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\CartValidationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\CartGetRequest;
use App\Services\OrderService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function getCartData(CartGetRequest $request)
    {
        //TODO добавить логирование
        $cart = $request->validated()['cart'];
        $products = null;
        $cartFullData = null;

        try {
            $orderService = new OrderService($cart);
            $cartFullData = $orderService->getCartFullData();
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error_message' => 'Не удалось найти некоторые товары',
                'products_id' => array_values($e->getIds())
            ], 400);
        } catch (CartValidationException $e) {
            return response()->json([
                'error_message' => $e->getMessage(),
                'options' => $e->getOptions()
            ], 400);
        } catch (Exception $e) {
            return response()->json(['error_message' => 'Что-то пошло пошло не так...'], 500);
        }

        return response()->json(['data' => $cartFullData]);
    }
}
