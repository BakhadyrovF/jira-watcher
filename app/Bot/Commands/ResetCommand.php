<?php

namespace App\Bot\Commands;

use Telegram\Bot\Commands\Command;
use Telegram\Bot\Helpers\Emojify;

class ResetCommand extends Command
{

    protected $name = 'reset';

    protected $description = 'Сбросить свои данные!';

    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        auth()->user()->update([
            'atlassian_email' => null,
            'atlassian_api_token' => null,
            'is_valid' => false
        ]);

        $this->replyWithMessage([
            'text' => Emojify::text(':white_check_mark:') . 'Данные были сброшены, можете пройти цикл заново - /start'
        ]);

        return true;
    }
}
