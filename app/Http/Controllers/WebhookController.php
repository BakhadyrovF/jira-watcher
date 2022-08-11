<?php

namespace App\Http\Controllers;

use App\Bot\Actions\FallbackAction;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Telegram\Bot\Api;
use Telegram\Bot\Helpers\Emojify;

class WebhookController extends Controller
{
    public function webhook(Request $request, TelegramService $telegramService, Api $bot, FallbackAction $fallbackAction)
    {
        try {
            $telegramService->setCurrentUser($request->all());
            $bot->commandsHandler(true);

            if (!in_array(currentMessage(), config('telegram.command_names'))) {

                if (!in_array(substr(currentMessage(), 0, strpos(currentMessage(), ' ')), config('telegram.command_names'))) {
                    $fallbackAction->handle($telegramService);
                }
            }
        } catch (\Exception $exception) {
            info($exception->getMessage());
        }



    }
}
