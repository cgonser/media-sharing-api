<?php

namespace App\Notification\Security;

use App\Notification\Entity\UserNotificationChannel;
use App\User\Security\AbstractUserAuthorizationVoter;

class UserNotificationChannelVoter extends AbstractUserAuthorizationVoter
{
    public function isSubjectSupported($subject): bool
    {
        return $subject instanceof UserNotificationChannel || $subject === UserNotificationChannel::class;
    }
}
