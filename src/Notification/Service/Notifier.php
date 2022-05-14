<?php

namespace App\Notification\Service;

use App\User\Entity\User;
use Symfony\Component\Notifier\Notification\Notification;

class Notifier
{
    public function notify(User $user): void
    {
    }
}