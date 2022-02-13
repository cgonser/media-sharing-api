<?php

namespace App\User\Exception;

use App\Core\Exception\InvalidInputException;

class UserInvalidRoleException extends InvalidInputException
{
    protected $message = 'Invalid role';
}
