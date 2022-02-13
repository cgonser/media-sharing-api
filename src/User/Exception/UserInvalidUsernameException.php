<?php

namespace App\User\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UserInvalidUsernameException extends BadRequestHttpException
{
    protected $message = 'Invalid username';

    public function __construct(string $message = null, \Throwable $previous = null, int $code = 0, array $headers = [])
    {
        parent::__construct($message ?? $this->message, $previous, $code, $headers);
    }
}
