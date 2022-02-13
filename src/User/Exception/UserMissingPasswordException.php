<?php

namespace App\User\Exception;

use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;

class UserMissingPasswordException extends PreconditionFailedHttpException
{
    protected $message = 'user.missing_password';

    public function __construct(string $message = null, \Throwable $previous = null, int $code = 0, array $headers = [])
    {
        parent::__construct($message ?? $this->message, $previous, $code, $headers);
    }
}