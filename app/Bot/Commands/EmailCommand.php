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
        $email = str_replace('/email ', '', $update->message?->text);

        if (empty($email)) {
            $email = str_replace('/email ', '', $update->editedMessage->text);
        }

        $validator = Validator::make(compact('email'), [
            'email' => ['filled', 'string', 'email:filter']
        ]);

        $text = Emojify::text(':x:') . 'Неправильный формат почты, попробуйте еще раз!' . PHP_EOL . PHP_EOL;
        $text .= toBold('Пример:') . PHP_EOL;
        $text .= '/email fooBarBaz@gmail.com';
        if ($validator->fails()) {
            $this->replyWithMessage([
                'text' => $text,
                'parse_mode' => 'HTML'
            ]);

            return true;
        }

        if (auth()->user()->atlassian_email === $email) {
            $this->replyWithMessage([
                'text' => Emojify::text(':x:') . 'Новая почта должна отличаться от старого!'
            ]);

            return true;
        }

        auth()->user()->update([
            'atlassian_email' => $email,
            'is_valid' => false
        ]);

        $this->replyWithMessage([
            'text' =>  Emojify::text(':white_check_mark:') . 'Ваша почта была обновлена!' . PHP_EOL . 'Отправьте /start чтобы продолжить.'
        ]);

        return true;



    }
}
