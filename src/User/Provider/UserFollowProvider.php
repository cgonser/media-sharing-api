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

    public function getByFollowerAndFollowing(
        UuidInterface $followerId,
        UuidInterface $followingId,
        ?bool $isApproved = true,
    ): UserFollow {
        /** @var UserFollow|null $userFollow */
        $userFollow = $this->findOneByFollowerAndFollowing($followerId, $followingId, $isApproved);

        if (null === $userFollow) {
            $this->throwNotFoundException();
        }

        return $userFollow;
    }

    public function findByFollowerId(UuidInterface $followerId): array
    {
        return $this->repository->findBy([
            'followerId' => $followerId,
        ]);
    }

    public function findOneByFollowerAndFollowing(
        UuidInterface $followerId,
        UuidInterface $followingId,
        ?bool $isApproved = true,
    ): ?UserFollow {
        $criteria = [
            'followerId' => $followerId,
            'followingId' => $followingId,
        ];

        if (null !== $isApproved) {
            $criteria['isApproved'] = $isApproved;
        }

        return $this->repository->findOneBy($criteria);
    }

    public function countByFollowerId(UuidInterface $followerId): int
    {
        return $this->repository->count([
            'followerId' => $followerId,
            'isApproved' => true,
        ]);
    }

    public function countByFollowingId(UuidInterface $followingId): int
    {
        return $this->repository->count([
            'followingId' => $followingId,
            'isApproved' => true,
        ]);
    }

    public function isFollowing(
        UuidInterface $followerId,
        UuidInterface $followingId,
        ?bool $isApproved = true,
    ): bool {
        return null !== $this->findOneByFollowerAndFollowing($followerId, $followingId, $isApproved);
    }

    protected function addFilters(QueryBuilder $queryBuilder, array $filters): void
    {
        if (isset($filters['root.isPending'])) {
            match ($filters['root.isPending']) {
                true => $queryBuilder->andWhere('root.isApproved IS NULL'),
                false => $queryBuilder->andWhere('root.isApproved IS NOT NULL'),
            };


            if (true === $filters['root.isPending']) {
                unset($filters['root.isApproved']);
            }

            unset($filters['root.isPending']);
        }

        parent::addFilters($queryBuilder, $filters);
    }

    protected function buildQueryBuilder(): QueryBuilder
    {
        return $this->repository->createQueryBuilder('root')
            ->innerJoin('root.follower', 'follower')
            ->innerJoin('root.following', 'following');
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

    protected function getSearchableFields(): array
    {
        return [
            'follower.username' => 'text',
            'follower.displayName' => 'text',
            'following.username' => 'text',
            'following.displayName' => 'text',
        ];
    }

    protected function throwNotFoundException()
    {
        throw new UserFollowNotFoundException();
    }
}
