<?php

namespace App\User\Exception;

use App\Core\Exception\ResourceNotFoundException;

class UserRoleNotFoundException extends ResourceNotFoundException
{
    protected $message = 'User role not found';
}
