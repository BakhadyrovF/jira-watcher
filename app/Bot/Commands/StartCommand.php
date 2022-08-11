<?php

namespace App\Bot\Commands;

use App\Models\User;
use App\Services\JiraService;
use App\Services\TelegramService;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Helpers\Emojify;

class StartCommand extends Command
{
    protected $name = 'start';

    protected $description = 'Начинаем!';


    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        if (!auth()->user()->is_valid) {
            $this->replyWithMessage(['text' => toBold(auth()->user()->telegram_first_name) . ' пройдите все шаги, что бы я мог работать правильно.', 'parse_mode' => 'HTML']);
        }

        if (is_null(auth()->user()->atlassian_email)) {
            $text = toBold('Шаг 1:') . PHP_EOL . PHP_EOL;
            $text .= Emojify::text(':warning:') . 'Пожалуйста отправьте свою рабочую почту с которого вы заходите в систему '. toBold('Atlassian JIRA') .', иначе мы не сможем продолжить.' . PHP_EOL . PHP_EOL;
            $text .= toBold('Формат:') . PHP_EOL;
            $text .= '/email {Ваша почта}' . PHP_EOL . PHP_EOL;
            $text .= toBold('Пример:') . PHP_EOL;
            $text .= '/email fooBarBaz@gmail.com';

            $this->replyWithMessage([
                'text' => $text,
                'parse_mode' => 'HTML'
            ]);

            return true;
        }

        if (is_null(auth()->user()->atlassian_api_token)) {
            $text = toBold('Шаг 2:') . PHP_EOL . PHP_EOL;
            $text .= Emojify::text(':warning:') . 'Пожалуйста отправьте свой API токен.' . PHP_EOL . PHP_EOL;
            $text .= toBold('Инструкция по созданию токена:') . PHP_EOL;
            $text .= '1.Перейдите по ссылке - https://id.atlassian.com/manage-profile/security/api-tokens' . PHP_EOL;
            $text .= '2.Нажмите на ' . toItalic('Создать токен.') . PHP_EOL;
            $text .= '3. Скопируйте его, так как больше не будет возможности.' . PHP_EOL . PHP_EOL;
            $text .= toBold('Пример:') . PHP_EOL;
            $text .= '/token some-api-token';

            $this->replyWithMessage([
                'text' => $text,
                'parse_mode' => 'HTML'
            ]);

            return true;
        }

        if (!auth()->user()->is_valid) {
            $text = toBold('Шаг 3:') . PHP_EOL . PHP_EOL;
            $text .= 'Идет проверка введеннных вами данных на валидность.' . PHP_EOL;
            $text .= 'Примерное время проверки - 3 секунды.';

            $this->replyWithMessage([
               'text' => $text,
                'parse_mode' => 'HTML'
            ]);

            if ((new JiraService())->isValidCredentials()) {
               auth()->user()->update([
                   'is_valid' => true
               ]);

               $text = Emojify::text(':white_check_mark:') . 'Проверка прошла успешно!' . PHP_EOL . PHP_EOL;
               $text .= 'Включите уведомления бота, чтобы не пропускать обновления!' . PHP_EOL;
               $text .= 'Спасибо вам за поддержку!';

               $this->replyWithMessage([
                   'text' => $text
               ]);

               return true;
            }

            $text = Emojify::text(':x:') . 'Вы ввели невалидные данные!' . PHP_EOL . PHP_EOL;
            $text .= toBold('Сбросить данные:') . PHP_EOL;
            $text .= '/reset' . PHP_EOL . PHP_EOL;
            $text .= toBold('Обновить данные:') . PHP_EOL;
            $text .= '/email {Ваша Почта}' . PHP_EOL;
            $text .= '/token {Ваш токен}';

            $this->replyWithMessage([
                'text' => $text,
                'parse_mode' => 'HTML'
            ]);

            return true;
        }

        $this->replyWithMessage([
            'text' => 'Вы уже прошли весь цикл!'. PHP_EOL .'Включите уведомления чтобы не пропускать обновления!'
        ]);

        return true;
    }
}
