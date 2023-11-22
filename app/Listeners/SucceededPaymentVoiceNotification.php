<?php

namespace App\Listeners;

use App\Events\SucceededPaymentCallbackReceived;
use App\Services\VoicePasswordService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SucceededPaymentVoiceNotification
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
        $voicePasswordService = new VoicePasswordService();
        $voicePasswordService->makeCallToManager();
    }
}
