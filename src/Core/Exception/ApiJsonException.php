<?php

namespace App\Core\Exception;

class ApiJsonException extends \JsonException
{
    public function __construct(
        private ?int $statusCode,
        ?string $message = '',
        private array $errors = [],
        \Throwable $previous = null
    ) {
        parent::__construct($message ?? '', $statusCode, $previous);
    }

    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
