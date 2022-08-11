<?php

namespace App\Bot\Commands;

use Illuminate\Support\Facades\Validator;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Helpers\Emojify;

class TokenCommand extends Command
{

    protected $name = 'token';

    protected $description = 'Добавить или обновить токен!';


    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        $update = $this->getUpdate();

        $token = str_replace('/token ', '', $update->message->text);

        $validator = Validator::make(compact('token'), [
            'token' => ['filled', 'string', 'min:10']
        ]);

        if ($validator->fails()) {
            $this->replyWithMessage([
                'text' => Emojify::text(':x:') . 'Невалидный формат API токена!'
            ]);

            return true;
        }

        if ($token === auth()->user()->atlassian_api_token) {
            $this->replyWithMessage([
                'text' => Emojify::text(':x:') . 'Новый токен должен отличаться от старого!'
            ]);

            return true;
        }

        auth()->user()->update([
            'atlassian_api_token' => $token,
            'is_valid' => false
        ]);

        $this->replyWithMessage([
            'text' => Emojify::text(':white_check_mark:') . 'API токен был обновлен!' . PHP_EOL . 'Отправьте /start чтобы продолжить!'
        ]);

        return true;

    }
}
