<?php

namespace App\Media\Provider;

use App\Core\Provider\AbstractProvider;
use App\Core\Request\SearchRequest;
use App\Media\Entity\Moment;
use App\Media\Enumeration\Mood;
use App\Media\Exception\MomentNotFoundException;
use App\Media\Repository\MomentRepository;
use App\Media\Request\MomentSearchRequest;
use DateTimeInterface;
use Doctrine\ORM\QueryBuilder;
use Ramsey\Uuid\UuidInterface;

class MomentProvider extends AbstractProvider
{
    public function __construct(MomentRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getByUserAndId(UuidInterface $userId, UuidInterface $momentId): Moment
    {
        /** @var Moment|null $moment */
        $moment = $this->repository->findOneBy([
            'id' => $momentId,
            'userId' => $userId,
        ]);

        if (!$moment) {
            throw new MomentNotFoundException();
        }

        return $moment;
    }

    public function findByAreaGroupedByMood(
        float $longMin,
        float $longMax,
        float $latMin,
        float $latMax,
        ?Mood $mood = null,
        ?UuidInterface $userId = null
    ): array {
        return $this->repository->findByAreaGroupedByMood($longMin, $longMax, $latMin, $latMax, $mood?->value, $userId);
    }

    public function searchRecordedOnDates(MomentSearchRequest $searchRequest): array
    {
        $orderExpression = $this->getOrderExpression($searchRequest);
        $orderDirection = $this->getOrderDirection($searchRequest);

        $limit = $searchRequest->resultsPerPage ?: self::RESULTS_PER_PAGE;
        $offset = ($searchRequest->page - 1) * $limit;

        $queryBuilder = $this->buildSearchQueryBuilder($searchRequest)
            ->select('root.recordedOn AS recordedOn')
            ->addSelect('COUNT(root.id) AS count')
            ->groupBy('root.recordedOn')
            ->orderBy($orderExpression, $orderDirection)
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        return $queryBuilder->getQuery()
            ->useQueryCache(true)
            ->getResult();
    }

    public function countRecordedOnDates(MomentSearchRequest $searchRequest): int
    {
        $queryBuilder = $this->buildSearchQueryBuilder($searchRequest);

        return (int)$queryBuilder->select('COUNT(DISTINCT root.recordedOn)')
            ->getQuery()
            ->useQueryCache(true)
            ->getSingleScalarResult();
    }

    protected function addFilters(QueryBuilder $queryBuilder, array $filters): void
    {
        $queryBuilder->innerJoin('root.location', 'location');

        if (isset($filters['location.longMin'])) {
            $queryBuilder->andWhere('location.long >= :longMin');

            unset($filters['location.longMin']);
        }

        if (isset($filters['location.longMax'])) {
            $queryBuilder->andWhere('location.long <= :longMax');

            unset($filters['location.longMax']);
        }

        if (isset($filters['location.latMin'])) {
            $queryBuilder->andWhere('location.lat >= :latMin');

            unset($filters['location.latMin']);
        }

        if (isset($filters['location.latMax'])) {
            $queryBuilder->andWhere('location.lat <= :latMax');

            unset($filters['location.latMax']);
        }

        if (isset($filters['root.statuses'])) {
            $queryBuilder->andWhere('root.status IN (:statuses)')
                ->setParameter('statuses', $filters['root.statuses']);

            unset($filters['root.status'], $filters['root.statuses']);
        }

        if (isset($filters['root.userIdExclusions'])) {
            if (!empty($filters['root.userIdExclusions'])) {
                $queryBuilder->andWhere('root.userId NOT IN (:userIdExclusions)')
                    ->setParameter('userIdExclusions', $filters['root.userIdExclusions']);
            }

            unset($filters['root.userIdExclusions']);
        }

        parent::addFilters($queryBuilder, $filters);
    }

    public function findByRecordedOn(UuidInterface $userId, DateTimeInterface $recordedOn): array
    {
        return $this->findBy(
            [
                'userId' => $userId,
                'recordedOn' => $recordedOn,
            ],
            [
                'recordedAt' => 'asc'
            ]
        );
    }

    protected function throwNotFoundException(): void
    {
        throw new MomentNotFoundException();
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
            'userIdExclusions',
            'status',
            'statuses',
            'recordedOn',
            'longMin' => 'location',
            'longMax' => 'location',
            'latMin' => 'location',
            'latMax' => 'location',
            'mood',
        ];
    }
}
