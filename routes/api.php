<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\FilterController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

//просто вывод категорий для меню
Route::get('categories', [CategoryController::class, 'index']);

// это для главной вывод категорий вместе с товарами. Можно указать параметр (?product_limit=10)
Route::get('categories/products', [CategoryController::class, 'getCategoriesWithProducts']);

//категория по slug
Route::get('categories/{category}', [CategoryController::class, 'getCategory']);

// в get параметрах принимает category_id, и фильтры
Route::get('products', [ProductController::class, 'index']);

//товар детально
Route::get('products/{product}', [ProductController::class, 'getProduct']);

//выдает список фильтров. На вход может принимать параметр category_id
Route::get('filters', [FilterController::class, 'getFiltersList']);

//отправить код авторизации в звонке
Route::post('voice-password/send/{phone}', [AuthController::class, 'sendVoicePasswordCode'])
    ->middleware('throttle:voice-password-send');

//проверить код и залогинить/зарегать
Route::post('voice-password/check/{phone}/{code}', [AuthController::class, 'checkVoicePasswordCode'])
    ->middleware('throttle:voice-password-check');

//высчитать стоимость доставки по длине маршрута. В параметрах принимает {latitude} и {longitude} точки назначения
Route::middleware('auth:sanctum')->get('delivery-price', [OrderController::class, 'getDeliveryPrice']);

//создание заказа принимает в параметрах {cart} и {form_data}
Route::middleware('auth:sanctum')->post('orders', [OrderController::class, 'createOrder']);

//заказы пользователя
Route::middleware('auth:sanctum')->get('orders', [OrderController::class, 'getUserOrders']);

//заказ пользователя детально
Route::middleware('auth:sanctum')->get('orders/{id}', [OrderController::class, 'getUserOrderDetails']);

//провалидировать корзину и получить всю инфу по корзине. Принимает в параметрах {cart}
Route::post('cart', [CartController::class, 'getCartData']);

//обработка callback от yookassa или robokassa
Route::post('payment/callback', [PaymentController::class, 'handleCallback']);

