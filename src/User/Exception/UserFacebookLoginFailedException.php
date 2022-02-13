<?php

namespace App\User\Exception;

use App\Core\Exception\ApiJsonException;
use Symfony\Component\HttpFoundation\Response;

class UserFacebookLoginFailedException extends ApiJsonException
{
    protected $message = 'Unable to authenticate user with facebook access token';

    public function __construct()
    {
        parent::__construct(Response::HTTP_UNAUTHORIZED, $this->message);
    }
}
