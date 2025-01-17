<?php

namespace App\User\Notification;

use App\Notification\Enumeration\NotificationChannel;
use App\Notification\Notification\AbstractNotification;
use App\User\Entity\UserFollow;

class UserFollowApprovedNotification extends AbstractNotification
{
    public const TYPE = 'user_follow.approved';

    public function __construct(UserFollow $userFollow)
    {
        parent::__construct([
            'userFollowId' => $userFollow->getId(),
            'followerId' => $userFollow->getFollowerId(),
            'followingId' => $userFollow->getFollowingId(),
            'followingUsername' => $userFollow->getFollowing()->getUsername(),
            'following_profile_url' => 'itinair://moments.itinair.com/user/'.$userFollow->getFollowingId(),
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
