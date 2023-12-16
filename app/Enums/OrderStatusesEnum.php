<?php

namespace App\Enums;

enum OrderStatusesEnum: int
{
    case CREATED = 1;
    case PAID = 2;
    case IN_PROCESSING = 3;
    case IN_DELIVERY = 4;
    case DELIVERED = 5;
    case CANCELLED = 6;
    case REFUND = 7;

    public function description(): string
    {
        return match ($this) {
            self::CREATED => 'Создан, но не оплачен',
            self::PAID => 'Оплачен',
            self::IN_PROCESSING => 'В обработке',
            self::IN_DELIVERY => 'Передан в доставку',
            self::DELIVERED => 'Доставлен',
            self::CANCELLED => 'Платеж отменен',
            self::REFUND => 'Произведен возврат денежных средств клиенту'
        };
    }

    public static function valuesWithDescription(): array
    {
        return array_reduce(self::cases(), function ($result, OrderStatusesEnum $item) {
            $result[$item->value] = $item->description();
            return $result;
        }, []);
    }

}
