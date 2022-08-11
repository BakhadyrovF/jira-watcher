<?php

return [

    'jira' => [
        'email' => env('JIRA_USER_EMAIL'),
        'token' => env('JIRA_USER_TOKEN')
    ],

    'bot' => [
        'url' => env('BOT_URL'),
        'full_url' => env('BOT_URL') . '/bot' . env('BOT_ACCESS_TOKEN') . '/',
        'token' => env('BOT_ACCESS_TOKEN'),
        'chat_id' => env('TELEGRAM_USER_CHAT_ID'),
    ]
];
