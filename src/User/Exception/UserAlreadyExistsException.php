<?php

namespace App\User\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UserAlreadyExistsException extends BadRequestHttpException
{
    protected $message = 'User already exists';

    public function __construct(string $message = null, \Throwable $previous = null, int $code = 0, array $headers = [])
    {
        parent::__construct($message ?? $this->message, $previous, $code, $headers);
    }
}
