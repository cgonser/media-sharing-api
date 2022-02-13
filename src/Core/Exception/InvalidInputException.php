<?php

namespace App\Core\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class InvalidInputException extends BadRequestHttpException
{
    protected $message = 'Invalid input';

    public function __construct(string $message = null, \Throwable $previous = null, int $code = 0, array $headers = [])
    {
        parent::__construct($message ?? $this->message, $previous, $code, $headers);
    }
}
