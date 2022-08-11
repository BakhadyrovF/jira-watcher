<?php

namespace App\Actions;

use App\Models\User;
use App\Services\JiraService;
use App\Services\TelegramService;
use Symfony\Component\HttpFoundation\Response;

final class SendNewIssuesToUsers
{
    public function __invoke(JiraService $jiraService, TelegramService $telegramService)
    {
        foreach (User::query()->orderBy('id')->where('is_valid', '=', true)->cursor() as $user) {
            $response = $jiraService->sendRequest('search', [
                'email' => $user->atlassian_email,
                'token' => $user->atlassian_api_token
            ], [
                'jql' => 'assignee=' . "'{$user->atlassian_email}'" . 'AND created >= -2d ORDER BY CREATED ASC'
            ]);

            if ($response->status() !== Response::HTTP_OK) {
                continue;
            }


            $newIssues = array_reduce($response['issues'], function ($result, $issue) use ($jiraService, $user)
            {
                if (!$user->issues()->where('issue_id', '=', $issue['id'])->first()) {
                    $result[] = $user->issues()->create([
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
                    'chat_id' => $user->telegram_chat_id,
                    'text' => $body,
                    'parse_mode' => 'HTML',
                    'disable_web_page_preview' => true
                ]);
            }
        }

    }
}
