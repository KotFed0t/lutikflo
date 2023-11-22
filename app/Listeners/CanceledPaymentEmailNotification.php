<?php

namespace App\Listeners;

use App\Events\CanceledPaymentCallbackReceived;
use App\Mail\CanceledPaymentClientEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class CanceledPaymentEmailNotification
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
    public function handle(CanceledPaymentCallbackReceived $event): void
    {
        Mail::to($event->order->user->email)->send(new CanceledPaymentClientEmail($event->order));
    }
}
