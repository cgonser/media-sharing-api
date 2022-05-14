<?php

namespace App\Media\Provider;

use App\Core\Provider\AbstractProvider;
use App\Core\Request\SearchRequest;
use App\Media\Entity\Moment;
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

    public function findByAreaGroupedByMood(float $longMin, float $longMax, float $latMin, float $latMax): array
    {
        return $this->repository->findByAreaGroupedByMood($longMin, $longMax, $latMin, $latMax);
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
        if (isset($filters['root.statuses'])) {
            $queryBuilder->andWhere('root.status IN (:statuses)')
                ->setParameter('statuses', $filters['root.statuses']);

            unset($filters['root.status'], $filters['root.statuses']);
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
            'status',
            'statuses',
            'recordedOn',
            'location',
            'mood',
        ];
    }
}
