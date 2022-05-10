<?php

namespace App\Media\Provider;

use App\Core\Provider\AbstractProvider;
use App\Media\Entity\Video;
use App\Media\Exception\VideoNotFoundException;
use App\Media\Repository\VideoRepository;
use Doctrine\ORM\QueryBuilder;
use Ramsey\Uuid\UuidInterface;

class VideoProvider extends AbstractProvider
{
    public function __construct(VideoRepository $repository)
    {
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

        if (isset($filters['root.mood'])) {
            $queryBuilder->andWhere('JSONB_EXISTS(root.moods, :mood) = TRUE')
                ->setParameter('mood', $filters['root.mood']);

            unset ($filters['root.mood']);
        }

        if (isset($filters['root.followerId'])) {
            $queryBuilder
                ->leftJoin('videoOwner.followers', 'follower', 'WITH', 'follower.isApproved = TRUE')
                ->andWhere(
                    $filters['root.followingOnly']
                        ? 'follower.followerId = :followerId'
                        : 'videoOwner.isProfilePrivate = FALSE OR follower.followerId = :followerId'
                )
                ->setParameter('followerId', $filters['root.followerId'])
            ;

            unset($filters['root.followerId']);
        }
        unset($filters['root.followingOnly']);

        if (isset($filters['root.status']) && str_contains($filters['root.status'], ',')) {
            $queryBuilder->andWhere('root.status IN (:statuses)')
                ->setParameter('statuses', explode(',', $filters['root.status']));

            unset($filters['root.status']);
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
            'followerId',
            'followingOnly',
            'location',
            'mood',
        ];
    }
}
