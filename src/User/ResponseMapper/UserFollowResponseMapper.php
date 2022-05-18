<?php

namespace App\User\ResponseMapper;

use App\User\Dto\UserFollowDto;
use App\User\Entity\UserFollow;

class UserFollowResponseMapper
{
    public function __construct(
        private readonly UserResponseMapper $userResponseMapper,
    ) {
    }

    public function map(UserFollow $userFollow, bool $mapFollowing = true, bool $mapFollower = false): UserFollowDto
    {
        $userFollowDto = new UserFollowDto();
        $userFollowDto->id = $userFollow->getId()->toString();
        $userFollowDto->followerId = $userFollow->getFollowerId()->toString();
        $userFollowDto->followingId = $userFollow->getFollowingId()->toString();
        $userFollowDto->isApproved = $userFollow->isApproved();
        $userFollowDto->createdAt = $userFollow->getCreatedAt()->format(\DateTimeInterface::ATOM);
        $userFollowDto->updatedAt = $userFollow->getUpdatedAt()->format(\DateTimeInterface::ATOM);

        if ($mapFollowing) {
            $userFollowDto->following = $this->userResponseMapper->mapPublic($userFollow->getFollowing());
        }

        if ($mapFollower) {
            $userFollowDto->follower = $this->userResponseMapper->mapPublic($userFollow->getFollower());
        }

        return $userFollowDto;
    }

    public function mapMultiple(array $userFollows, bool $mapFollowing = true, bool $mapFollower = false): array
    {
        $userFollowDtos = [];

        foreach ($userFollows as $userFollow) {
            $userFollowDtos[] = $this->map($userFollow, $mapFollowing, $mapFollower);
        }

        return $userFollowDtos;
    }
}
