<?php

namespace App\Notification\Exception;

use App\Core\Exception\ResourceNotFoundException;

class NotificationSettingsNotFoundException extends ResourceNotFoundException
{
    protected $message = 'notification.settings_not_found';
}
