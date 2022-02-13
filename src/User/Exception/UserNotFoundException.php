<?php

namespace App\User\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserNotFoundException extends NotFoundHttpException
{
    protected $message = 'User not found';

    public function __construct(string $message = null, \Throwable $previous = null, int $code = 0, array $headers = [])
    {
        parent::__construct($message ?? $this->message, $previous, $code, $headers);
    }
}
