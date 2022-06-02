<?php

namespace App\Notification\Exception;

use App\Core\Exception\ResourceNotFoundException;

class PushSettingsNotFoundException extends ResourceNotFoundException
{
    protected $message = 'notification.push_settings_not_found';
}
