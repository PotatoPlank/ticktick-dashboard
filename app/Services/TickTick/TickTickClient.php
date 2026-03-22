<?php

namespace App\Services\TickTick;

use App\Exceptions\TickTickApiException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class TickTickClient
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.ticktick.base_url'), '/');
    }

    public function get(string $endpoint, array $query = []): array
    {
        $response = Http::retry(3, 500, fn ($exception, $request) => $this->shouldRetry($exception))
            ->withToken($this->token())
            ->get($this->baseUrl.$endpoint, $query);

        $this->throwIfFailed($response);

        return $response->json() ?? [];
    }

    public function post(string $endpoint, array $data = []): array
    {
        $response = Http::retry(3, 500, fn ($exception, $request) => $this->shouldRetry($exception))
            ->withToken($this->token())
            ->post($this->baseUrl.$endpoint, $data);

        $this->throwIfFailed($response);

        return $response->json() ?? [];
    }

    public function delete(string $endpoint): void
    {
        $response = Http::retry(3, 500, fn ($exception, $request) => $this->shouldRetry($exception))
            ->withToken($this->token())
            ->delete($this->baseUrl.$endpoint);

        $this->throwIfFailed($response);
    }

    private function token(): string
    {
        return config('services.ticktick.token') ?? '';
    }

    private function shouldRetry(\Throwable $exception): bool
    {
        return $exception instanceof TickTickApiException && $exception->statusCode === 429;
    }

    private function throwIfFailed(Response $response): void
    {
        if ($response->failed()) {
            throw TickTickApiException::fromResponse($response);
        }
    }
}
