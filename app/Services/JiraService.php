<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Response;

final class JiraService
{
    public const BASE_URL = 'https://abdigitalco.atlassian.net';
    public const API_PATH = 'rest/api/2';

    /**
     * @param string $path
     * @param array $query
     * @throws BadRequestException
     * @return array
     */
    public function sendRequest(string $path, array $query = []): array
    {
        $response = Http::withBasicAuth(config('credentials.jira.email'), config('credentials.jira.token'))
            ->get($this->urlWithEndpoint($path), $query);

        if ($response->status() !== Response::HTTP_OK) {
            throw new BadRequestException('Invalid request');
        }

        return $response->json();

    }

    /**
     * @param string $issueKey
     * @return string
     */
    public function generateLinkToIssue(string $issueKey): string
    {
        return self::BASE_URL . '/browse/' . $issueKey;
    }

    /**
     * @param string $endpoint
     * @return string
     */
    private function urlWithEndpoint(string $path): string
    {
        return self::BASE_URL . '/' . self::API_PATH . '/' . $path;
    }
}
