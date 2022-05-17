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
            'approve_follow_back_url' => 'itinair://moments.itinair.com/users/follow/'.$userFollow->getFollowerId(),
            'approve_only_url' => 'itinair://moments.itinair.com/users/follow-approve/'.$userFollow->getId(),
            'disregard_url' => 'itinair://moments.itinair.com/users/follow-reject/'.$userFollow->getId(),
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
