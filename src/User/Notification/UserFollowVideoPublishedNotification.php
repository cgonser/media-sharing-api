<?php

namespace App\User\Notification;

use App\Media\Entity\Video;
use App\Notification\Enumeration\NotificationChannel;
use App\Notification\Notification\AbstractNotification;
use App\User\Entity\User;

class UserFollowVideoPublishedNotification extends AbstractNotification
{
    public const TYPE = 'user_follow.video_published';

    public function __construct(User $user, Video $video)
    {
        parent::__construct([
            'userId' => $user->getId(),
            'videoId' => $video->getId(),
            'name' => $user->getName(),
            'followingUsername' => $video->getUser()->getUsername(),
            'cta_url' => 'https://momemnts.itinair.com',
        ]);
    }

    public function getAvailableChannels(): array
    {
        return [
//            NotificationChannel::PUSH,
        ];
    }
}
