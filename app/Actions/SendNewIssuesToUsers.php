<?php

namespace App\Actions;

use App\Services\JiraService;
use App\Services\TelegramService;

final class SendNewIssuesToUsers
{
    public function __invoke(JiraService $jiraService, TelegramService $telegramService): void
    {
        $response = $jiraService->sendRequest('search', [
            'jql' => 'assignee="firuzbekbakhadirov@77projects.com" AND created >= -2d ORDER BY created ASC'
        ]);


        $newIssues = array_reduce($response['issues'], function ($result, $issue) use ($jiraService)
        {
            if (!\App\Models\Issue::query()->where('issue_id', '=', $issue['id'])->first()) {
                $result[] = \App\Models\Issue::query()->create([
                    'issue_id' => $issue['id'],
                    'key' => $issue['key'],
                    'link' => $jiraService->generateLinkToIssue($issue['key']),
                    'summary' => $issue['fields']['summary'],
                    'description' => $issue['fields']['description'],
                    'issue_created_at' => \Illuminate\Support\Carbon::parse($issue['fields']['created'])->toDateTimeString()
                ]);
            }

            return $result;

        }, []);
//        unset($newIssues[0]);


        if (!empty($newIssues)) {

            $body = '';
            if (count($newIssues) === 1) {
                $body .= toBold('У вас новая задача!') . PHP_EOL . PHP_EOL;
            } else {
                $body .= toBold('У вас новые задачи!') . PHP_EOL . PHP_EOL;
            }

            $counter = 1;

            foreach ($newIssues as $issue) {
                $body .= toItalic(toBold('Задача - ' . $counter . ':')) . PHP_EOL;
                $body .= $issue->summary . PHP_EOL;
                $body .= $issue->link . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL;
                $counter++;
            }

            $telegramService->sendRequest('sendMessage', 'POST', [
                'chat_id' => config('credentials.bot.chat_id'),
                'text' => $body,
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true
            ]);
        }










    }
}
