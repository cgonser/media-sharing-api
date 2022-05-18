<?php

namespace App\Notification\Provider;

use App\Core\Provider\AbstractProvider;
use App\Notification\Entity\UserNotificationChannel;
use App\Notification\Enumeration\NotificationChannel;
use App\Notification\Repository\UserNotificationChannelRepository;
use Ramsey\Uuid\UuidInterface;

class UserNotificationChannelProvider extends AbstractProvider
{
    public function __construct(
        UserNotificationChannelRepository $repository,
    ) {
        $this->repository = $repository;
    }

    public function findOneByUserAndChannel(
        UuidInterface $userId,
        NotificationChannel $channel,
    ): ?UserNotificationChannel {
        return $this->repository->findOneBy([
            'userId' => $userId,
            'channel' => $channel,
        ], [
            'createdAt' => 'DESC',
        ]);
    }
}