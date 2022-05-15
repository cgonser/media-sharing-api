<?php

namespace App\Media\Notification;

use App\Media\Entity\VideoComment;
use App\Notification\Enumeration\NotificationChannel;
use App\Notification\Notification\AbstractNotification;

class VideoCommentedNotification extends AbstractNotification
{
    public const TYPE = 'video.commented';

    public function __construct(VideoComment $videoComment)
    {
        parent::__construct([
            'id' => $videoComment->getId(),
            'videoId' => $videoComment->getVideoId(),
            'videoUserId' => $videoComment->getVideo()->getUserId(),
            'videoCommentUserId' => $videoComment->getUserId(),
            'videoCommentUsername' => $videoComment->getUser()->getUsername(),
            'image_url' => 'https://moments.itinair.com',
            'cta_url' => 'https://moments.itinair.com',
        ]);
    }

    public function getAvailableChannels(): array
    {
        return [
            NotificationChannel::EMAIL,
//            NotificationChannel::PUSH,
        ];
    }
}
