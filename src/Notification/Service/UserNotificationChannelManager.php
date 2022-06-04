<?php

namespace App\Notification\Service;

use App\Core\Validation\EntityValidator;
use App\Notification\Entity\UserNotificationChannel;
use App\Notification\Enumeration\NotificationChannel;
use App\Notification\Repository\UserNotificationChannelRepository;
use App\User\Entity\User;

class UserNotificationChannelManager
{
    public function __construct(
        private readonly UserNotificationChannelRepository $userNotificationChannelRepository,
        private readonly EntityValidator $validator
    ) {
    }

    public function save(UserNotificationChannel $userNotificationChannel): void
    {
        $this->validator->validate($userNotificationChannel);

        $this->userNotificationChannelRepository->save($userNotificationChannel);
    }

    public function delete(?UserNotificationChannel $userNotificationChannel): void
    {
        $this->userNotificationChannelRepository->delete($userNotificationChannel);
    }

    public function enableEmailNotifications(User $user): void
    {
        $userNotificationChannel = (new UserNotificationChannel())
            ->setUser($user)
            ->setChannel(NotificationChannel::EMAIL)
        ;

        $this->save($userNotificationChannel);
    }
}
