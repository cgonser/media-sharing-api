<?php

namespace App\Core\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ResourceNotFoundException extends NotFoundHttpException
{
    protected $message = "Resource not found";

    public function __construct(string $message = null, \Throwable $previous = null, int $code = 0, array $headers = [])
    {
        parent::__construct($message ?? $this->message, $previous, $code, $headers);
    }
}
