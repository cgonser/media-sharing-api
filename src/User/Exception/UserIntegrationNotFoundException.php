<?php

namespace App\User\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserIntegrationNotFoundException extends NotFoundHttpException
{
    protected $message = 'User integration not found';

    public function __construct(string $message = null, \Throwable $previous = null, int $code = 0, array $headers = [])
    {
        parent::__construct($message ?? $this->message, $previous, $code, $headers);
    }
}
