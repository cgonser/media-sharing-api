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
            'videoId' => $video->getId(),
            'userId' => $video->getUserId(),
            'image_url' => 'https://moments.itinair.com/',
            'cta_url' => 'itinair://moments.itinair.com/video/'.$video->getId()->toString(),
        ]);
    }

    public function getAvailableChannels(): array
    {
        return [
            NotificationChannel::EMAIL,
            NotificationChannel::CHAT,
        ];
    }
}
