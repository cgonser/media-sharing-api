<?php

namespace App\Media\Notification;

use App\Media\Entity\Moment;
use App\Notification\Enumeration\NotificationChannel;
use Symfony\Component\Notifier\Notification\Notification;

class MomentPublishedNotification extends Notification
{
    public function __construct(
        private readonly Moment $moment,
    ) {
        parent::__construct(
            Moment::class,
            [
                NotificationChannel::EMAIL->value,
            ]
        );
    }

    public function getContent(): string
    {
        return 'Moment published';
    }
}