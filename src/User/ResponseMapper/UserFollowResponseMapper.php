<?php

namespace App\User\ResponseMapper;

use App\User\Dto\UserFollowDto;
use App\User\Entity\UserFollow;

class UserFollowResponseMapper
{
    public function __construct(
        private UserResponseMapper $userResponseMapper,
    ) {
    }

    public function map(UserFollow $userFollow): UserFollowDto
    {
        $userFollowDto = new UserFollowDto();
        $userFollowDto->id = $userFollow->getId()->toString();
        $userFollowDto->followerId = $userFollow->getFollowerId()->toString();
        $userFollowDto->followingId = $userFollow->getFollowingId()->toString();
        $userFollowDto->following = $this->userResponseMapper->mapPublic($userFollow->getFollowing());
        $userFollowDto->isApproved = $userFollow->isApproved();
        $userFollowDto->createdAt = $userFollow->getCreatedAt()->format(\DateTimeInterface::ATOM);
        $userFollowDto->updatedAt = $userFollow->getUpdatedAt()->format(\DateTimeInterface::ATOM);

        return $userFollowDto;
    }

    public function mapMultiple(array $userFollows): array
    {
        $userFollowDtos = [];

        foreach ($userFollows as $userFollow) {
            $userFollowDtos[] = $this->map($userFollow);
        }

        return $userFollowDtos;
    }
}
