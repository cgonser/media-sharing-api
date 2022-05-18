<?php

namespace App\Notification\Service;

use App\Core\Validation\EntityValidator;
use App\Notification\Entity\UserNotificationChannel;
use App\Notification\Repository\UserNotificationChannelRepository;

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
}
