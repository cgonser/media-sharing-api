<?php

namespace App\User\Provider;

use App\Core\Provider\AbstractProvider;
use App\User\Entity\User;
use App\User\Entity\UserFollow;
use App\User\Exception\UserFollowNotFoundException;
use App\User\Repository\UserFollowRepository;
use Doctrine\ORM\QueryBuilder;
use Ramsey\Uuid\UuidInterface;

class UserFollowProvider extends AbstractProvider
{
    public function __construct(UserFollowRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getByFollowingAndId(UuidInterface $followerId, UuidInterface $id): UserFollow
    {
        /** @var UserFollow|null $userFollow */
        $userFollow = $this->findOneByFollowingAndId($followerId, $id);

        if (null === $userFollow) {
            $this->throwNotFoundException();
        }

        return $userFollow;
    }

    public function findOneByFollowingAndId(UuidInterface $followingId, UuidInterface $id): ?UserFollow
    {
        return $this->repository->findOneBy([
            'id' => $id,
            'followingId' => $followingId,
        ]);
    }

    public function getByFollowerAndId(UuidInterface $followerId, UuidInterface $id): UserFollow
    {
        /** @var UserFollow|null $userFollow */
        $userFollow = $this->findOneByFollowerAndId($followerId, $id);

        if (null === $userFollow) {
            $this->throwNotFoundException();
        }

        return $userFollow;
    }

    public function findOneByFollowerAndId(UuidInterface $followerId, UuidInterface $id): ?UserFollow
    {
        return $this->repository->findOneBy([
            'id' => $id,
            'followerId' => $followerId,
        ]);
    }

    public function getByFollowerAndFollowing(UuidInterface $followerId, UuidInterface $followingId): UserFollow
    {
        /** @var UserFollow|null $userFollow */
        $userFollow = $this->findOneByFollowerAndFollowing($followerId, $followingId);

        if (null === $userFollow) {
            $this->throwNotFoundException();
        }

        return $userFollow;
    }

    public function findOneByFollowerAndFollowing(UuidInterface $followerId, UuidInterface $followingId): ?UserFollow
    {
        return $this->repository->findOneBy([
            'followerId' => $followerId,
            'followingId' => $followingId,
            'isApproved' => true,
        ]);
    }

    public function isFollowing(UuidInterface $followerId, UuidInterface $followingId): bool
    {
        return null !== $this->findOneByFollowerAndFollowing($followerId, $followingId);
    }

    protected function addFilters(QueryBuilder $queryBuilder, array $filters): void
    {
        if (isset($filters['root.isPending'])) {
            match ($filters['root.isPending']) {
                true => $queryBuilder->andWhere('root.isApproved IS NULL'),
                false => $queryBuilder->andWhere('root.isApproved IS NOT NULL'),
            };

            unset($filters['root.isPending']);
        }

        parent::addFilters($queryBuilder, $filters);
    }

    protected function getFilterableFields(): array
    {
        return [
            'followerId',
            'followingId',
            'isApproved',
            'isPending',
        ];
    }

    protected function throwNotFoundException()
    {
        throw new UserFollowNotFoundException();
    }
}
