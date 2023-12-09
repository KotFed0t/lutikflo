<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramBotService
{
    private string $botApiToken;
    private string $chatId;

    private const TELEGRAM_API_URL = 'https://api.telegram.org/bot';

    public function __construct()
    {
        $this->botApiToken = config('services.telegram.botApiToken');
        $this->chatId = config('services.telegram.chatId');
    }

    public function sendMessage(string $message): void
    {
        try {
            $response = Http::post(self::TELEGRAM_API_URL . "{$this->botApiToken}/sendMessage", [
                'chat_id' => $this->chatId,
                'text' => $message,
            ]);

            if ($response->failed()) {
                Log::error('failed request to Telegram api', ['response from telegram' => $response]);
            }
        } catch (Exception $e) {
            Log::error('error while sending telegram message', ['error_message' => $e->getMessage()]);
        }
    }

}
