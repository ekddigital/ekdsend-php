<?php

declare(strict_types=1);

namespace EKDSend\Exceptions;

/**
 * Exception for not found errors (404)
 */
class NotFoundException extends EKDSendException
{
    public function __construct(string $message, string $errorCode = 'NOT_FOUND', ?string $requestId = null)
    {
        parent::__construct($message, 404, $errorCode, $requestId);
    }
}
