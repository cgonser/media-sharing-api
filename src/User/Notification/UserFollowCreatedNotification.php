<?php

namespace App\User\Notification;

use App\Notification\Enumeration\NotificationChannel;
use App\Notification\Notification\AbstractNotification;
use App\User\Entity\UserFollow;

class UserFollowCreatedNotification extends AbstractNotification
{
    public const TYPE = 'user_follow.created';

    public function __construct(UserFollow $userFollow)
    {
        parent::__construct([
            'userFollowId' => $userFollow->getId(),
            'followerId' => $userFollow->getFollowerId(),
            'followingId' => $userFollow->getFollowingId(),
            'followerUsername' => $userFollow->getFollower()->getUsername(),
            'follow_back_url' => 'itinair://moments.itinair.com/users/follow/'.$userFollow->getFollowerId(),
            'follower_profile_url' => 'itinair://moments.itinair.com/user/'.$userFollow->getFollowerId(),
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
