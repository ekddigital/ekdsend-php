<?php

declare(strict_types=1);

namespace EKDSend\Exceptions;

/**
 * Exception for validation errors (400)
 */
class ValidationException extends EKDSendException
{
    /** @var array<string, mixed> */
    private array $errors;

    /**
     * @param string $message Error message
     * @param array<string, mixed> $errors Validation errors
     * @param string|null $requestId Request ID
     */
    public function __construct(string $message, array $errors = [], ?string $requestId = null)
    {
        parent::__construct($message, 400, 'VALIDATION_ERROR', $requestId);
        $this->errors = $errors;
    }

    /**
     * Get the validation errors
     * 
     * @return array<string, mixed>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
