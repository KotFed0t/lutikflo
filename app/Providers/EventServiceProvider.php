<?php

namespace App\Providers;

use App\Events\CanceledPaymentCallbackReceived;
use App\Events\OrderStatusUpdated;
use App\Events\SucceededPaymentCallbackReceived;
use App\Listeners\CanceledPaymentEmailNotification;
use App\Listeners\OrderStatusUpdatedEmailNotification;
use App\Listeners\SucceededPaymentEmailNotification;
use App\Listeners\SucceededPaymentVoiceNotification;
use App\Listeners\SucceededPaymmentTelegramNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        SucceededPaymentCallbackReceived::class => [
            SucceededPaymentEmailNotification::class,
            SucceededPaymmentTelegramNotification::class,
//            SucceededPaymentVoiceNotification::class,
        ],
        CanceledPaymentCallbackReceived::class => [
            CanceledPaymentEmailNotification::class
        ],
        OrderStatusUpdated::class => [
            OrderStatusUpdatedEmailNotification::class
        ]
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
