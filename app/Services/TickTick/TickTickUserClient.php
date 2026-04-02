<?php

namespace App\Services\TickTick;

use App\Exceptions\TickTickApiException;
use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class TickTickUserClient
{
    private string $baseUrl;

    private CookieJar $cookieJar;

    private array $headers = [
        'Accept' => '*/*',
        'Accept-Encoding' => 'gzip, deflate, br, zstd',
        'Accept-Language' => 'en-US,en;q=0.9',
        'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',
        'Content-Type' => 'application/json',
        'X-Csrftoken' => '',
        'X-Device' => '{"platform":"web","os":"Linux x86_64","device":"Chrome 146.0.0.0","name":"","version":8046,"id":"69ce729b49981a1f690ba086","channel":"website","campaign":"","websocket":""}',
    ];

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.ticktick.base_url'), '/');
        $this->cookieJar = new CookieJar;

        // Generate X-Device header
        $this->headers['Traceid'] = $this->mongoObjectId(time());
        $this->headers['X-Device'] = json_encode([
            'platform' => 'web',
            'os' => 'Linux x86_64',
            'device' => 'Chrome 146.0.0.0',
            'name' => '',
            'version' => '8046',
            'id' => $this->headers['Traceid'],
            'channel' => 'website',
            'campaign' => '',
            'websocket' => '',
        ], JSON_THROW_ON_ERROR);

        // Get the TickTick CSRF cookies

        $formResp = Http::withOptions([
            'cookies' => $this->cookieJar,
            'headers' => $this->headers,
        ])
            ->get('https://ticktick.com/signin');
        $this->throwIfFailed($formResp);

        // Save CSRF cookies
        foreach ($formResp->cookies() as $cookie) {
            $this->cookieJar->setCookie($cookie);
        }

        // Authenticate to TickTick
        $authBody = [
            'password' => config('services.ticktick.password'),
            'username' => config('services.ticktick.username'),
        ];

        $auth = Http::withOptions([
            'cookies' => $this->cookieJar,
            'headers' => [
                ...$this->headers,
                'Accept' => 'application/json',
            ],
        ])
            ->post('https://api.ticktick.com/api/v2/user/signon?wc=true&remember=true', $authBody);
        $this->throwIfFailed($auth);

        // Save auth cookies and any new CSRF data
        foreach ($auth->cookies() as $cookie) {
            $this->cookieJar->setCookie($cookie);
        }
    }

    public function get(string $endpoint, array $query = [], array $headers = []): array
    {
        $response = Http::retry(3, 500, fn ($exception, $request) => $this->shouldRetry($exception))
            ->withOptions([
                'cookies' => $this->cookieJar,
                'headers' => [
                    ...$this->headers,
                    ...$headers,
                ],
            ])
            ->get($this->baseUrl.$endpoint, $query);

        $this->throwIfFailed($response);

        return $response->json() ?? [];
    }

    public function post(string $endpoint, array $data = [], array $headers = []): array
    {
        $response = Http::retry(3, 500, fn ($exception, $request) => $this->shouldRetry($exception))
            ->withOptions([
                'cookies' => $this->cookieJar,
                'headers' => [
                    ...$this->headers,
                    ...$headers,
                ],
            ])
            ->post($this->baseUrl.$endpoint, $data);

        $this->throwIfFailed($response);

        return $response->json() ?? [];
    }

    public function delete(string $endpoint, array $headers = []): void
    {
        $response = Http::retry(3, 500, fn ($exception, $request) => $this->shouldRetry($exception))
            ->withOptions([
                'cookies' => $this->cookieJar,
                'headers' => [
                    ...$this->headers,
                    ...$headers,
                ],
            ])
            ->delete($this->baseUrl.$endpoint);

        $this->throwIfFailed($response);
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

    private function mongoObjectId($timestamp): string
    {
        $hostname = 'dashboard';
        $processId = 700 .random_int(100, 999);
        $id = 0;
        // var_dump($timestamp, $hostname, $processId, $id);exit;
        // Building binary data.
        $bin = sprintf(
            '%s%s%s%s',
            pack('N', $timestamp),
            substr(md5($hostname), 0, 3),
            pack('n', $processId),
            substr(pack('N', $id), 1, 3)
        );

        // Convert binary to hex.
        $result = '';
        for ($i = 0; $i < 12; $i++) {
            $result .= sprintf('%02x', ord($bin[$i]));
        }

        return $result;
    }
}
