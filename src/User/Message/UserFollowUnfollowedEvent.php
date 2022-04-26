<?php

namespace App\User\Message;

use Ramsey\Uuid\UuidInterface;

class UserFollowUnfollowedEvent
{
    /**
     * @var string
     */
    public const NAME = 'user_follow.unfollowed';

    public function __construct(
        private UuidInterface $followerId,
        private UuidInterface $followingId,
    ) {
    }

    public function getFollowerId(): ?UuidInterface
    {
        return $this->followerId;
    }

    public function getFollowingId(): ?UuidInterface
    {
        return $this->followingId;
    }
}
