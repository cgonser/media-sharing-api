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
            'videoId' => $videoComment->getVideoId(),
            'videoUserId' => $videoComment->getVideo()->getUserId(),
            'videoUsername' => $videoComment->getVideo()->getUser()->getUsername(),
            'videoCommentId' => $videoComment->getId(),
            'videoCommentUserId' => $videoComment->getUserId(),
            'videoCommentUsername' => $videoComment->getUser()->getUsername(),
            'videoCommentComment' => $videoComment->getComment(),
            'image_url' => 'https://moments.itinair.com',
            'cta_url' => 'itinair://moments.itinair.com/video/'.$videoComment->getVideoId()->toString(),
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
