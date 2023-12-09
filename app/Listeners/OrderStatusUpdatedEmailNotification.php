<?php

namespace App\Listeners;

use App\Enums\OrderStatusesEnum;
use App\Events\OrderStatusUpdated;
use App\Mail\OrderStatusUpdatedClientEmail;
use App\Services\TelegramBotService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class OrderStatusUpdatedEmailNotification
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
    public function handle(OrderStatusUpdated $event): void
    {
        if (in_array($event->order->status, [OrderStatusesEnum::IN_PROCESSING, OrderStatusesEnum::IN_DELIVERY, OrderStatusesEnum::DELIVERED])) {
            Mail::to($event->order->user->email)->send(new OrderStatusUpdatedClientEmail($event->order));
        }

    }
}
