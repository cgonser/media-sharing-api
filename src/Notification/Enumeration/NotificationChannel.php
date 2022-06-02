<?php

namespace App\Notification\Enumeration;

enum NotificationChannel: string
{
    case EMAIL = 'email';
    case SMS = 'sms';
    case PUSH = 'push';
    case BROWSER = 'browser';
    case CHAT = 'chat';
}
