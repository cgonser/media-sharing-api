<?php

namespace App\Media\Notification;

use App\Media\Entity\VideoLike;
use App\Notification\Enumeration\NotificationChannel;
use App\Notification\Notification\AbstractNotification;

class VideoLikedNotification extends AbstractNotification
{
    public const TYPE = 'video.liked';

    public function __construct(VideoLike $videoLike)
    {
        parent::__construct([
            'videoId' => $videoLike->getVideoId(),
            'videoUserId' => $videoLike->getVideo()->getUserId(),
            'videoUsername' => $videoLike->getVideo()->getUser()->getUsername(),
            'videoLikeId' => $videoLike->getId(),
            'videoLikeUserId' => $videoLike->getUserId(),
            'videoLikeUsername' => $videoLike->getUser()->getUsername(),
            'image_url' => 'https://moments.itinair.com',
            'cta_url' => 'itinair://moments.itinair.com/video/'.$videoLike->getVideoId()->toString(),
        ]);
    }

    public function getAvailableChannels(): array
    {
        return [
            NotificationChannel::EMAIL,
//            NotificationChannel::CHAT,
        ];
    }
}
