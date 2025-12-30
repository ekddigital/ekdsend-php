<?php

declare(strict_types=1);

namespace EKDSend\Exceptions;

use Exception;

/**
 * Base exception for EKDSend API errors
 */
class EKDSendException extends Exception
{
    protected int $statusCode;
    protected string $errorCode;
    protected ?string $requestId;

    public function __construct(
        string $message,
        int $statusCode = 500,
        string $errorCode = 'UNKNOWN_ERROR',
        ?string $requestId = null
    ) {
        parent::__construct($message);
        $this->statusCode = $statusCode;
        $this->errorCode = $errorCode;
        $this->requestId = $requestId;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getRequestId(): ?string
    {
        return $this->requestId;
    }
}
