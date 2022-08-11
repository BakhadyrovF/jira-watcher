<?php

namespace App\Bot\Actions;

use App\Models\User;
use App\Services\TelegramService;

final class FallbackAction
{
    public function handle(TelegramService $telegramService)
    {
        if (auth()->user()->isValid === false) {
            $telegramService->sendRequest('sendMessage', 'POST', [
                'text' => 'Вы не прошли полный цикл, отправьте /start чтобы продолжить.',
                'chat_id' => auth()->user()->telegram_chat_id,
                'parse_mode' => 'HTML'
            ]);
        }

        if (array_key_exists('new_chat_member', request()->all()['my_chat_member'] ?? '')) {

            if (request()->all()['my_chat_member']['new_chat_member']['status'] === 'kicked') {
                User::query()
                    ->where('telegram_chat_id', '=', request()->all()['my_chat_member']['chat']['id'])
                    ->delete();
            }

        }

    }
}
