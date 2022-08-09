<?php

namespace App\Bot\Commands;

use App\Services\TelegramService;
use Telegram\Bot\Commands\Command;

class HelpCommand extends Command
{

    protected $name = 'help';

    protected $description = 'Получить информацию про бота!';


    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        $update = $this->getUpdate();
        $text = '@jirawatcher' . ' будет уведомлять вас о новых задачах которые были назначены вам в системе ' . toBold('Atlassian JIRA.') . PHP_EOL . PHP_EOL;
        $text .= toBold('Список команд:') . PHP_EOL;

        $commands = $this->telegram->getCommands();

        foreach ($commands as $name => $handler) {
            /* @var Command $handler */
            $text .= sprintf('/%s - %s' . PHP_EOL, $name, toItalic($handler->getDescription()));
        }

        $this->replyWithMessage([
            'text' => $text,
            'parse_mode' => 'HTML'
        ]);
    }
}
