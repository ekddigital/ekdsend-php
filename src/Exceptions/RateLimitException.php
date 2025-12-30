<?php

declare(strict_types=1);

namespace EKDSend\Exceptions;

/**
 * Exception for rate limit errors (429)
 */
class RateLimitException extends EKDSendException
{
    private int $retryAfter;

    public function __construct(string $message, int $retryAfter = 60, ?string $requestId = null)
    {
        parent::__construct($message, 429, 'RATE_LIMIT_EXCEEDED', $requestId);
        $this->retryAfter = $retryAfter;
    }

    /**
     * Get the number of seconds to wait before retrying
     */
    public function getRetryAfter(): int
    {
        return $this->retryAfter;
    }
}
