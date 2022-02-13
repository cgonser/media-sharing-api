<?php

namespace App\Core\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class InvalidImageException extends BadRequestHttpException
{
    protected $message = 'You have to upload a valid image';

    public function __construct()
    {
        parent::__construct($this->message);
    }
}
