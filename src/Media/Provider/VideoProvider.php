<?php

namespace App\Media\Provider;

use App\Core\Provider\AbstractProvider;
use App\Media\Entity\Video;
use App\Media\Enumeration\VideoStatus;
use App\Media\Exception\VideoNotFoundException;
use App\Media\Repository\VideoRepository;
use App\User\Repository\UserFollowRepository;
use Doctrine\ORM\QueryBuilder;
use Ramsey\Uuid\UuidInterface;

class VideoProvider extends AbstractProvider
{
    public function __construct(
        VideoRepository $repository,
        private readonly UserFollowRepository $userFollowRepository,
    ) {
        $this->repository = $repository;
    }

    public function getByUserAndId(UuidInterface $userId, UuidInterface $momentId): Video
    {
        /** @var Video|null $moment */
        $moment = $this->repository->findOneBy([
            'id' => $momentId,
            'userId' => $userId,
        ]);

        if (!$moment) {
            throw new VideoNotFoundException();
        }

        return $moment;
    }

    public function countByUserId(UuidInterface $userId): int
    {
        return $this->repository->count([
            'userId' => $userId,
            'status' => VideoStatus::PUBLISHED,
        ]);
    }

    protected function throwNotFoundException(): void
    {
        throw new VideoNotFoundException();
    }

    protected function addFilters(QueryBuilder $queryBuilder, array $filters): void
    {
        $queryBuilder->innerJoin('root.user', 'videoOwner');

        if (isset($filters['root.location'])) {
            $queryBuilder->andWhere('JSONB_EXISTS(root.locations, :location) = TRUE')
                ->setParameter('location', $filters['root.location']);

            unset ($filters['root.location']);
        }

        if (isset($filters['root.moods'])) {
            foreach ($filters['root.moods'] as $key => $mood) {
                $queryBuilder->andWhere('JSONB_EXISTS(root.moods, :mood_'.$key.') = TRUE')
                    ->setParameter('mood_'.$key, $mood);
            }

            unset($filters['root.moods'], $filters['root.mood']);
        }

        if (isset($filters['root.mood'])) {
            $queryBuilder->andWhere('JSONB_EXISTS(root.moods, :mood) = TRUE')
                ->setParameter('mood', $filters['root.mood']);

            unset ($filters['root.mood']);
        }


        if (isset($filters['root.followerId'])) {
            $subQuery = ($this->userFollowRepository->createQueryBuilder('uf'))
                ->select("uf")
                ->andWhere('uf.isApproved = TRUE')
                ->andWhere('uf.followerId = :followerId')
                ->andWhere('uf.followingId = root.userId')
            ;

            $queryBuilder->setParameter('followerId', $filters['root.followerId']);

            if ($filters['root.followingOnly']) {
                $queryBuilder->andWhere($queryBuilder->expr()->exists($subQuery->getDQL()));
            } else {
                $queryBuilder->andWhere(
                    $queryBuilder->expr()->orX(
                        'videoOwner.isProfilePrivate = FALSE',
                        $queryBuilder->expr()->exists($subQuery->getDQL())
                    )
                );
            }

            unset($filters['root.followerId']);
        }

        unset($filters['root.followingOnly']);

        if (isset($filters['root.userIdExclusions'])) {
            if (!empty($filters['root.userIdExclusions'])) {
                $queryBuilder->andWhere('root.userId NOT IN (:userIdExclusions)')
                    ->setParameter('userIdExclusions', $filters['root.userIdExclusions']);
            }

            unset($filters['root.userIdExclusions']);
        }

        if (isset($filters['root.statuses']) && !empty($filters['root.statuses'])) {
            $queryBuilder->andWhere('root.status IN (:statuses)')
                ->setParameter('statuses', $filters['root.statuses']);

            unset($filters['root.statuses'], $filters['root.status']);
        }

        parent::addFilters($queryBuilder, $filters);
    }

    protected function getSearchableFields(): array
    {
        return [
            'location' => 'text',
        ];
    }

    protected function getFilterableFields(): array
    {
        return [
            'userId',
            'status',
            'statuses',
            'followerId',
            'followingOnly',
            'userIdExclusions',
            'location',
            'mood',
            'moods',
        ];
    }
}
