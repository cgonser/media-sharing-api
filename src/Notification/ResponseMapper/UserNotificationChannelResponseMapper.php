<?php

namespace App\Notification\ResponseMapper;

use App\Notification\Dto\NotificationDto;
use App\Notification\Dto\UserNotificationChannelDto;
use App\Notification\Entity\Notification;
use App\Notification\Entity\UserNotificationChannel;
use DateTimeInterface;

class UserNotificationChannelResponseMapper
{
    public function map(UserNotificationChannel $userNotificationChannel): UserNotificationChannelDto
    {
        $userNotificationChannelDto = new UserNotificationChannelDto();
        $userNotificationChannelDto->id = $userNotificationChannel->getId()->toString();
        $userNotificationChannelDto->userId = $userNotificationChannel->getUserId()->toString();
        $userNotificationChannelDto->channel = $userNotificationChannel->getChannel()->value;
        $userNotificationChannelDto->deviceType = $userNotificationChannel->getDeviceType();
        $userNotificationChannelDto->externalId = $userNotificationChannel->getExternalId();
        $userNotificationChannelDto->token = $userNotificationChannel->getToken();
        $userNotificationChannelDto->expiresAt = $userNotificationChannel->getExpiresAt()?->format(DateTimeInterface::ATOM);
        $userNotificationChannelDto->createdAt = $userNotificationChannel->getCreatedAt()?->format(DateTimeInterface::ATOM);

        return $userNotificationChannelDto;
    }

    public function mapMultiple(array $userNotificationChannels): array
    {
        $userNotificationChannelDtos = [];

        foreach ($userNotificationChannels as $userNotificationChannel) {
            $userNotificationChannelDtos[] = $this->map($userNotificationChannel);
        }

        return $userNotificationChannelDtos;
    }
}
