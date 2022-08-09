<?php

namespace App\Http\Controllers;

use App\Services\TelegramService;
use Illuminate\Http\Request;
use Telegram\Bot\Api;

class WebhookController extends Controller
{
    public function webhook(Request $request, TelegramService $telegramService, Api $bot)
    {
        $telegramService->setCurrentUser($request->all());
        $bot->commandsHandler(true);


    }
}
