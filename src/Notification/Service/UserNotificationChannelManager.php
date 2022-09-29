<?php

namespace App\Notification\Service;

use App\Core\Validation\EntityValidator;
use App\Notification\Entity\UserNotificationChannel;
use App\Notification\Enumeration\NotificationChannel;
use App\Notification\Provider\UserNotificationChannelProvider;
use App\Notification\Repository\UserNotificationChannelRepository;
use App\User\Entity\User;

class UserNotificationChannelManager
{
    public function __construct(
        private readonly UserNotificationChannelRepository $userNotificationChannelRepository,
        private readonly UserNotificationChannelProvider $userNotificationChannelProvider,
        private readonly EntityValidator $validator
    ) {
    }

    public function create(UserNotificationChannel $userNotificationChannel): void
    {
        $this->deleteEntriesOfSameType($userNotificationChannel);

        $this->save($userNotificationChannel);
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

    private function deleteEntriesOfSameType(UserNotificationChannel $userNotificationChannel): void
    {
        $otherUserNotificationChannels = $this->userNotificationChannelProvider->findBy([
            'userId' => $userNotificationChannel->getUserId(),
            'channel' => $userNotificationChannel->getChannel(),
            'deviceType' => $userNotificationChannel->getDeviceType(),
            'isActive' => true,
        ]);

        /** @var UserNotificationChannel $otherUserNotificationChannel */
        foreach ($otherUserNotificationChannels as $otherUserNotificationChannel) {
            $this->delete($otherUserNotificationChannel);
        }
    }
}
