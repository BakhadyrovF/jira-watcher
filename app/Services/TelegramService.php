<?php

namespace App\Services;

use App\Bot\Commands\StartCommand;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use phpDocumentor\Reflection\DocBlock\Tags\Throws;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Response;

final class TelegramService
{
    protected string $fullUrl;

    public function __construct()
    {
        $this->fullUrl = config('credentials.bot.full_url');
    }

    /**
     * @param string $method
     * @param string $requestMethod
     * @param array $data
     * @throws BadRequestException
     * @return array
     */
    public function sendRequest(string $method, string $requestMethod = 'GET',array $data = []): array
    {
        if ($requestMethod === 'GET') {
            $response = Http::get($this->urlWithMethod($method), $data);
        } else {
            $response = Http::post($this->urlWithMethod($method), $data);
        }

        if ($response->status() !== Response::HTTP_OK) {
            throw new BadRequestException('Invalid request');
        }

        return $response->json();
    }

    public function setCurrentUser(array $data)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {

                if ($key === 'chat') {
                    $user = User::query()
                        ->firstOrCreate([
                            'telegram_chat_id' => $value['id']
                        ], [
                            'telegram_first_name' => $this->getFromValues()['first_name'],
                            'telegram_username' => $this->getFromValues()['username'] ?? null
                        ]);

                    auth()->setUser($user);
                }

                $this->setCurrentUser($value);
            }

        }
    }

    protected function getFromValues()
    {
        return request()->all()['message']['from'] ?? null;
    }

    private function urlWithMethod(string $method)
    {
        return $this->fullUrl . $method;
    }
}
