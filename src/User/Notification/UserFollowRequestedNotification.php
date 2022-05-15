<?php

namespace App\User\Notification;

use App\Notification\Enumeration\NotificationChannel;
use App\Notification\Notification\AbstractNotification;
use App\User\Entity\UserFollow;

class UserFollowRequestedNotification extends AbstractNotification
{
    public const TYPE = 'user_follow.requested';

    public function __construct(UserFollow $userFollow)
    {
        parent::__construct([
            'id' => $userFollow->getId(),
            'followerId' => $userFollow->getFollowerId(),
            'followingId' => $userFollow->getFollowingId(),
            'followerUsername' => $userFollow->getFollower()->getUsername(),
            'approve_follow_back_url' => 'https://momemnts.itinair.com',
            'approve_only_url' => 'https://momemnts.itinair.com',
            'disregard_url' => 'https://momemnts.itinair.com',
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
