<?php

namespace App\Services;

use App\Enums\OrderStatusesEnum;
use App\Exceptions\CartValidationException;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class OrderService
{
    private Collection $products;
    private array $cart;

    /**
     * @throws CartValidationException
     * @throws ModelNotFoundException
     */
    public function __construct(array $cart)
    {
        $productsId = array_unique(array_column($cart, 'product_id'));
        $this->products = Product::with('flowers')->findOrFail($productsId);
        $this->cart = $this->validateCart($cart);
    }

    private function validateCart(array $cart): array
    {
        $inactiveProducts = $this->products->where('is_active', 0);
        if ($inactiveProducts->count() > 0) {
            throw new CartValidationException(
                message: 'В корзине есть товары, которых уже нет в наличии',
                data: ['inactive_products' => $inactiveProducts]
            );
        }

        foreach ($cart as $cartItem) {
            $product = $this->products->where('id', $cartItem['product_id'])->first();
            $changebleFlower = $product->getChangeableFlower();
            if ($changebleFlower !== null && !empty($cartItem['flower_count'])) {
                if ($changebleFlower->pivot->count > $cartItem['flower_count']) {
                    throw new CartValidationException(
                        message: 'количество цветов в букете не может быть меньше ' . $changebleFlower->pivot->count,
                        data: ['cart_item' => $cartItem]
                    );
                }
            } elseif (!empty($cartItem['flower_count']) && $changebleFlower === null) {
                throw new CartValidationException(
                    message: 'Кажется товар успел измениться. Теперь у данного товара нельзя менять кол-во цветов',
                    data: ['cart_item' => $cartItem, 'product' => $product]
                );
            } elseif (empty($cartItem['flower_count']) && $changebleFlower !== null) {
                throw new CartValidationException(
                    message: 'Кажется товар успел измениться. Теперь необходимо выбрать количество цветов в букете',
                    data: ['cart_item' => $cartItem, 'product' => $product]
                );
            }
        }
        return $cart;
    }

    public function getOrderData(int $deliveryPrice): array
    {
        $orderPrice = 0;
        $productsWithPrice = [];
        $yookassaItems = [];
        foreach ($this->cart as $cartItem) {
            $product = $this->products->where('id', $cartItem['product_id'])->first();
            if (!empty($cartItem['flower_count'])) {
                $changeableFlower = $product->getChangeableFlower();
                $additionalFlowersPrice = ($cartItem['flower_count'] - $changeableFlower->pivot->count) * $changeableFlower->price;
                $priceByProduct = ($product->price + $additionalFlowersPrice);
                $priceByCount = $priceByProduct * $cartItem['count'];
                $orderPrice += $priceByCount;
                $productsWithPrice[] = [
                    'product_id' => $product->id,
                    'product_count' => $cartItem['count'],
                    'flower_count' => $cartItem['flower_count'],
                    'price_by_one_product' => $priceByProduct,
                    'price_by_count_product' => $priceByCount
                ];

                $yookassaItems[] = [
                    'description' => $product->name . ' (' . $cartItem['flower_count'] . ' шт.)',
                    'quantity' => $cartItem['count'],
                    'amount' => [
                        'value' => $priceByProduct,
                        'currency' => 'RUB'
                    ],
                    'vat_code' => '2',
                ];
            } else {
                $priceByCount = $product->price * $cartItem['count'];
                $orderPrice += $priceByCount;
                $productsWithPrice[] = [
                    'product_id' => $product->id,
                    'product_count' => $cartItem['count'],
                    'flower_count' => null,
                    'price_by_one_product' => $product->price,
                    'price_by_count_product' => $priceByCount,
                ];

                $yookassaItems[] = [
                    'description' => $product->name,
                    'quantity' => $cartItem['count'],
                    'amount' => [
                        'value' => $product->price,
                        'currency' => 'RUB'
                    ],
                    'vat_code' => '2',
                ];
            }
        }
        $yookassaItems[] = [
            'description' => 'Цена доставки',
            'quantity' => 1,
            'amount' => [
                'value' => $deliveryPrice,
                'currency' => 'RUB'
            ],
            'vat_code' => '2',
        ];

        return [
            'orderPrice' => $orderPrice,
            'deliveryPrice' => $deliveryPrice,
            'productsWithPrice' => $productsWithPrice,
            'yookassaItems' => $yookassaItems
        ];
    }

    public function createOrder($orderData, $formData, $userId): Order
    {
        $order = Order::query()->create([
            'user_id' => $userId,
            'is_anonymous_sender' => $formData['is_anonymous_sender'] ?? false,
            'is_recipient_current_user' => $formData['is_recipient_current_user'],
            'previously_call_to_recipient' => $formData['previously_call_to_recipient'] ?? null,
            'recipient_name' => $formData['recipient_name'] ?? null,
            'recipient_phone' => $formData['recipient_phone'] ?? null,
            'delivery_address' => $formData['delivery_address'],
            'entrance' => $formData['entrance'] ?? null,
            'floor' => $formData['floor'] ?? null,
            'apartment_number' => $formData['apartment_number'] ?? null,
            'comment_for_courier' => $formData['comment_for_courier'] ?? null,
            'delivery_date_time' => $formData['delivery_date_time'],
            'client_wishes' => $formData['client_wishes'] ?? null,
            'order_price' => $orderData['orderPrice'],
            'delivery_price' => $orderData['deliveryPrice'],
            'total_price' => $orderData['orderPrice'] + $orderData['deliveryPrice'],
            'status' => OrderStatusesEnum::CREATED
        ]);

        $this->createOrderProductRecords($order->id, $orderData);
        return $order;
    }

    private function createOrderProductRecords(int $orderId, array $orderData): void
    {
        $orderProduct = array_map(function ($item) use ($orderId) {
            $item['order_id'] = $orderId;
            $item['created_at'] = now();
            $item['updated_at'] = now();
            return $item;
        }, $orderData['productsWithPrice']);
        DB::table('order_product')->insert($orderProduct);
    }
}
