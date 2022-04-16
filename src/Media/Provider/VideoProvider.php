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
        $queryBuilder->innerJoin('root.User', 'videoOwner');

        if (isset($filters['root.followerId'])) {
            $queryBuilder->leftJoin('videoOwner.followers', 'follower', 'WITH', 'follower.isApproved = TRUE')
                ->andWhere('videoOwner.isProfilePrivate = FALSE OR follower.followerId = ?', ':followerId');

            unset($filters['root.followerId']);
        } else {
            $queryBuilder->andWhere('videoOwner.isProfilePrivate = FALSE');
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
            'followerId',
            'location',
            'mood',
        ];
    }
}
