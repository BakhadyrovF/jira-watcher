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
     * @return \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response
     * @throws BadRequestException
     */
    public function sendRequest(string $path, array $credentials, array $query = []): \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response
    {
        $response = Http::withBasicAuth($credentials['email'], $credentials['token'])
            ->get($this->urlWithEndpoint($path), $query);

        return $response;

    }

    /**
     * @return bool
     */
    public function isValidCredentials(): bool
    {
        $response = $this->sendRequest('myself', [
            'email' => auth()->user()->atlassian_email ?? '',
            'token' => auth()->user()->atlassian_api_token ?? ''
        ]);

        if ($response->status() !== Response::HTTP_OK) {
            return false;
        }

        return true;
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
