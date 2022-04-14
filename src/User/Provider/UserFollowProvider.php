<?php

namespace App\User\Provider;

use App\Core\Provider\AbstractProvider;
use App\User\Entity\User;
use App\User\Entity\UserFollow;
use App\User\Exception\UserFollowNotFoundException;
use App\User\Repository\UserFollowRepository;
use Ramsey\Uuid\UuidInterface;

class UserFollowProvider extends AbstractProvider
{
    public function __construct(UserFollowRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getByUserAndId(UuidInterface $followerId, UuidInterface $id): UserFollow
    {
        /** @var UserFollow|null $userFollow */
        $userFollow = $this->findOneByUserAndId($followerId, $id);

        if (null === $userFollow) {
            $this->throwNotFoundException();
        }

        return $userFollow;
    }

    public function findOneByUserAndId(UuidInterface $followerId, UuidInterface $id): ?UserFollow
    {
        return $this->repository->findOneBy([
            'id' => $id,
            'followerId' => $followerId,
        ]);
    }

    public function getByFollowerAndFollowing(UuidInterface $followerId, UuidInterface $followingId): UserFollow
    {
        /** @var UserFollow|null $userFollow */
        $userFollow = $this->repository->findOneBy([
            'followerId' => $followerId,
            'followingId' => $followingId,
        ]);

        if (null === $userFollow) {
            $this->throwNotFoundException();
        }

        return $userFollow;
    }

    public function isFollowing(User $follower, User $following): bool
    {
        return (bool) $this->findOneBy([
            'follower' => $follower,
            'following' => $following
        ]);
    }

    protected function throwNotFoundException()
    {
        throw new UserFollowNotFoundException();
    }
}
