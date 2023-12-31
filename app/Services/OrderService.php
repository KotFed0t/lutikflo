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

    public function getProducts()
    {
        return $this->products;
    }

    private function validateCart(array $cart): array
    {
        $invalidProductsId = [];
        $inactiveProductsId = $this->products->where('is_active', false)->pluck('id');
        if ($inactiveProductsId->count() > 0) {
            //товары уже не в наличии
            array_push($invalidProductsId, ...$inactiveProductsId);
        }

        foreach ($cart as $cartItem) {
            $product = $this->products->where('id', $cartItem['product_id'])->first();
            $changebleFlower = $product->getChangeableFlower();
            if ($changebleFlower !== null && !empty($cartItem['flower_count'])) {
                if ($changebleFlower->pivot->count > $cartItem['flower_count']) {
                    //количество цветов в букете не может быть меньше заданного
                    $invalidProductsId[] = $cartItem['product_id'];
                }
            } elseif (!empty($cartItem['flower_count']) && $changebleFlower === null) {
                //Теперь у данного товара нельзя менять кол-во цветов
                $invalidProductsId[] = $cartItem['product_id'];
            } elseif (empty($cartItem['flower_count']) && $changebleFlower !== null) {
                //Теперь необходимо выбрать количество цветов в букете
                $invalidProductsId[] = $cartItem['product_id'];
            }
        }

        if (!empty($invalidProductsId)) {
            throw new CartValidationException(
                message: 'некоторые товары в корзине успели измениться',
                data: array_unique($invalidProductsId)
            );
        }
        return $cart;
    }

    public function getOrderData(int $deliveryPrice): array
    {
        $orderPrice = 0;
        $productsWithPrice = [];
        $yookassaItems = [];
        $robokassaItems = [];
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

                $robokassaItems[] = [
                    'name' => $product->name . ' (' . $cartItem['flower_count'] . ' шт.)',
                    'quantity' => $cartItem['count'],
                    'cost' => $priceByProduct,
                    'sum' => $priceByCount,
                    'payment_method' => 'full_payment',
                    'tax' => 'none'
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

                $robokassaItems[] = [
                    'name' => $product->name,
                    'quantity' => $cartItem['count'],
                    'cost' => $product->price,
                    'sum' => $priceByCount,
                    'payment_method' => 'full_payment',
                    'tax' => 'none'
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

        $robokassaItems[] = [
            'name' => 'Цена доставки',
            'quantity' => 1,
            'sum' => $deliveryPrice,
            'payment_method' => 'full_payment',
            'tax' => 'none'
        ];

        return [
            'orderPrice' => $orderPrice,
            'deliveryPrice' => $deliveryPrice,
            'productsWithPrice' => $productsWithPrice,
            'yookassaItems' => $yookassaItems,
            'robokassaItems'=>  $robokassaItems

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

    public function getCartFullData(): array
    {
        $products = $this->products;
        return array_map(function ($cartItem) use ($products) {
            $product = $products->find($cartItem['product_id']);
            $changeableFlower = $product->getChangeableFlower();
            $cartItem['product'] = [
                'name' => $product->name,
                'slug' => $product->slug,
                'category_id' => $product->category_id,
                'price' => $product->price,
                'main_img' => $product->main_img
            ];
            if ($changeableFlower !== null) {
                $cartItem['flower'] = [
                    'price' => $changeableFlower->price,
                    'min_count' => $changeableFlower->pivot->count
                ];
            }
            return $cartItem;
        }, $this->cart);
    }
}
