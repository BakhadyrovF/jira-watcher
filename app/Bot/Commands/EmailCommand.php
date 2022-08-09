<?php

namespace App\Bot\Commands;

use Illuminate\Support\Facades\Validator;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Helpers\Emojify;

class EmailCommand extends Command
{

    protected $name = 'email';

    protected $description = 'Добавить или обновить почту!';


    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        $update = $this->getUpdate();
        info($update->message->text);
        $validator = Validator::make([
            'email' => str_replace('/email ','', $update->message->text)
        ], [
            'email' => ['filled', 'string', 'email:filter']
        ]);

        $text = Emojify::text(':x:') . 'Неправильный формат почты, попробуйте еще раз!' . PHP_EOL . PHP_EOL;
        $text .= toBold('Пример:') . PHP_EOL;
        $text .= '/email fooBarBaz@gmail.com';
        if ($validator->fails()) {
            return $this->replyWithMessage([
                'text' => $text,
                'parse_mode' => 'HTML'
            ]);
        }

        $this->replyWithMessage([
            'text' => 'CORRECT'
        ]);

        // Callback

    }
}
