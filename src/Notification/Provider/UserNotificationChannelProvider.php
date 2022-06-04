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

    public function getByUserAndId(UuidInterface $userId, UuidInterface $momentId): UserNotificationChannel
    {
        /** @var UserNotificationChannel|null $userNotificationChannel */
        $userNotificationChannel = $this->repository->findOneBy([
            'id' => $momentId,
            'userId' => $userId,
        ]);

        if (!$userNotificationChannel) {
            $this->throwNotFoundException();
        }

        return $userNotificationChannel;
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

    public function findActiveByUser(UuidInterface $userId): array
    {
        return $this->repository->findBy([
            'userId' => $userId,
            'isActive' => true,
        ]);
    }

    protected function getFilterableFields(): array
    {
        return [
            'userId',
            'channel',
            'device',
            'externalId',
        ];
    }
}