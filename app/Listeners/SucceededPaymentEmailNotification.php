<?php

namespace App\Listeners;

use App\Events\SucceededPaymentCallbackReceived;
use App\Mail\SucceededPaymentClientEmail;
use App\Mail\SucceededPaymentManagerEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SucceededPaymentEmailNotification
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
        //TODO заменить почту менеджера
        Mail::to('julijasss.93@mail.ru')->send(new SucceededPaymentManagerEmail($event->order));

        Mail::to($event->order->user->email)->send(new SucceededPaymentClientEmail($event->order));
    }
}
