<?php

declare(strict_types=1);

namespace EKDSend\Exceptions;

/**
 * Exception for authentication errors (401)
 */
class AuthenticationException extends EKDSendException
{
    public function __construct(string $message, ?string $requestId = null)
    {
        parent::__construct($message, 401, 'AUTHENTICATION_ERROR', $requestId);
    }
}
