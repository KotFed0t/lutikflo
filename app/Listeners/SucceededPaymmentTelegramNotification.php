<?php

namespace App\Listeners;

use App\Events\SucceededPaymentCallbackReceived;
use App\Services\TelegramBotService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SucceededPaymmentTelegramNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(SucceededPaymentCallbackReceived $event): void
    {
        $telegramBotService = new TelegramBotService();
        $products = '';
        foreach ($event->order->products as $product) {
            $product_name = $product->pivot->flower_count !== null ? "{$product->name} ({$product->pivot->flower_count} шт.)" : $product->name;
            $products .= "- $product_name x {$product->pivot->product_count} шт.\n";
        }

        $delivery_date_time = $event->order->delivery_date_time !==null ? $event->order->delivery_date_time->format('d.m.Y') . ' c ' . $event->order->delivery_date_time->format('H:i') . ' до ' . $event->order->delivery_date_time->addMinutes(30)->format('H:i') : 'уточнить у получателя';
        $previously_call_to_recipient = $event->order->previously_call_to_recipient ? 'да' : 'нет';

        $telegramBotService->sendMessage("Новый заказ на сайте. \n\nid заказа: {$event->order->id} \n\nДата и время доставки: $delivery_date_time \n\nПредварительно позвонить получателю: $previously_call_to_recipient \n\nСостав заказа:\n{$products}");
    }
}
