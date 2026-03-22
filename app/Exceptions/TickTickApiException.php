<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Client\Response;

class TickTickApiException extends Exception
{
    public function __construct(
        string $message,
        public readonly int $statusCode = 0,
    ) {
        parent::__construct($message, $statusCode);
    }

    public static function fromResponse(Response $response): static
    {
        $body = $response->json();
        $message = $body['message'] ?? $body['errorMessage'] ?? "TickTick API error ({$response->status()})";

        return new static($message, $response->status());
    }
}
