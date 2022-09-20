<?php

namespace App\Media\Notification;

use App\Media\Entity\Moment;
use App\Notification\Enumeration\NotificationChannel;
use App\Notification\Notification\AbstractNotification;

class MomentPublishedNotification extends AbstractNotification
{
    public const TYPE = 'moment.published';

    public function __construct(Moment $moment)
    {
        parent::__construct([
            'momentId' => $moment->getId(),
            'userId' => $moment->getUserId(),
        ]);
    }

    public function getAvailableChannels(): array
    {
        return [
//            NotificationChannel::CHAT,
        ];
    }
}
