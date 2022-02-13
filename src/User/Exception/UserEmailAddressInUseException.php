<?php

namespace App\User\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UserEmailAddressInUseException extends BadRequestHttpException
{
    protected $message = 'E-mail address already in use';

    public function __construct(string $message = null, \Throwable $previous = null, int $code = 0, array $headers = [])
    {
        parent::__construct($message ?? $this->message, $previous, $code, $headers);
    }
}
