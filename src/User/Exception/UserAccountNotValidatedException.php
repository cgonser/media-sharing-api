<?php

namespace App\User\Exception;

use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;

class UserAccountNotValidatedException extends PreconditionFailedHttpException
{
    protected $message = 'user.account_not_validated';

    public function __construct(string $message = null, \Throwable $previous = null, int $code = 0, array $headers = [])
    {
        parent::__construct($message ?? $this->message, $previous, $code, $headers);
    }
}