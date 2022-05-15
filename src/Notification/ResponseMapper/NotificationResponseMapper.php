<?php

namespace App\Notification\ResponseMapper;

use App\Notification\Dto\NotificationDto;
use App\Notification\Entity\Notification;
use DateTimeInterface;

class NotificationResponseMapper
{
    public function map(Notification $notification): NotificationDto
    {
        $notificationDto = new NotificationDto();
        $notificationDto->id = $notification->getId()->toString();
        $notificationDto->userId = $notification->getUserId()->toString();
        $notificationDto->type = $notification->getType();
        $notificationDto->content = $notification->getContent();
        $notificationDto->context = $notification->getContext();
        $notificationDto->readAt = $notification->getReadAt()?->format(DateTimeInterface::ATOM);
        $notificationDto->createdAt = $notification->getCreatedAt()->format(DateTimeInterface::ATOM);

        return $notificationDto;
    }

    public function mapMultiple(array $notifications): array
    {
        $notificationDtos = [];

        foreach ($notifications as $notification) {
            $notificationDtos[] = $this->map($notification);
        }

        return $notificationDtos;
    }
}
