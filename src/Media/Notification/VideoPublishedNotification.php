<?php

namespace App\Media\Notification;

use App\Media\Entity\Video;
use App\Notification\Enumeration\NotificationChannel;
use App\Notification\Notification\AbstractNotification;

class VideoPublishedNotification extends AbstractNotification
{
    public const TYPE = 'video.published';

    public function __construct(Video $video)
    {
        parent::__construct([
            'id' => $video->getId(),
            'userId' => $video->getUserId(),
            'image_url' => 'https://moments.itinair.com/',
            'cta_url' => 'https://moments.itinair.com/',
        ]);
    }

    public function getAvailableChannels(): array
    {
        return [
//            NotificationChannel::PUSH,
        ];
    }
}
