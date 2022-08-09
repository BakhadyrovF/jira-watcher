<?php

namespace App\Bot\Commands;

use App\Models\User;
use App\Services\TelegramService;
use Telegram\Bot\Commands\Command;

class StartCommand extends Command
{
    protected $name = 'start';

    protected $description = 'Начинаем!';


    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        $this->replyWithMessage(['text' => toBold(auth()->user()->telegram_first_name) . ' пожалуйста пройдите все шаги, что бы я мог работать правильно.', 'parse_mode' => 'HTML']);



        if (is_null(auth()->user()->atlassian_email)) {
            $text = toBold('Шаг 1:') . PHP_EOL . PHP_EOL;
            $text .= 'Пожалуйста отправьте свою рабочую почту с которого вы заходите в систему '. toBold('Atlassian JIRA') .', иначе мы не сможем продолжить.' . PHP_EOL . PHP_EOL;
            $text .= toBold('Формат:') . PHP_EOL;
            $text .= '/email {Ваша почта}' . PHP_EOL . PHP_EOL;
            $text .= toBold('Пример:') . PHP_EOL;
            $text .= '/email fooBarBaz@gmail.com';

            $this->replyWithMessage([
                'text' => $text,
                'parse_mode' => 'HTML'
            ]);
        }
    }
}
