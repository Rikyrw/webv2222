<?php

namespace App\Services;

use RuntimeException;
use Throwable;

class GroqChatbotException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly int $statusCode,
        public readonly ?int $retryAfterSeconds = null,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $statusCode, $previous);
    }

    public function isRateLimit(): bool
    {
        $lowerMessage = mb_strtolower($this->getMessage());

        return $this->statusCode === 429
            || str_contains($lowerMessage, 'rate limit')
            || str_contains($lowerMessage, 'too many requests')
            || str_contains($lowerMessage, 'quota')
            || str_contains($lowerMessage, 'limit exceeded');
    }
}
