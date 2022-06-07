<?php

namespace App\Notification\Notification;

use App\Notification\Enumeration\NotificationChannel;

class CustomPushNotification extends AbstractNotification
{
    public const TYPE = 'custom.push';

    public function getAvailableChannels(): array
    {
        return [
            NotificationChannel::CHAT,
        ];
    }
}
