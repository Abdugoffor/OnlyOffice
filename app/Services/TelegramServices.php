<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramServices
{
    /**
     * Create a new class instance.
     */
    const token = 'https://api.telegram.org/bot6263518001:AAEevMJVknsVxMA-JSd6dTcUO4N1Lc3aiOY/';

    public function send($message)
    {
        $response = Http::post(self::token . 'sendMessage', [
            'chat_id' => env('TELEGRAM_CHAT_ID'),
            'parse_mode' => 'HTML',
            'text' => $message,
        ]);

        $response = Http::post(self::token . 'sendMessage', [
            'chat_id' => env('TELEGRAM_CHAT_ADMIN'),
            'parse_mode' => 'HTML',
            'text' => $message,
        ]);
    }
}
