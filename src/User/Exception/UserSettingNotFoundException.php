<?php

namespace App\User\Exception;

use App\Core\Exception\ResourceNotFoundException;

class UserSettingNotFoundException extends ResourceNotFoundException
{
    protected $message = 'User setting not found';
}
