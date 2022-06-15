<?php

namespace App\Core\Provider;

use App\Core\Exception\ResourceNotFoundException;
use App\Core\Request\SearchRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Ramsey\Uuid\UuidInterface;

abstract class AbstractProvider
{
    const RESULTS_PER_PAGE = 100;

    protected ServiceEntityRepository $repository;

    public function findAll(): array
    {
        return $this->repository->findAll();
    }

    public function findBy(?array $criteria = null, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function get(UuidInterface $id): object
    {
        /** @var object|null $program */
        $object = $this->repository->find($id);

        if (null === $object) {
            $this->throwNotFoundException();
        }

        return $object;
    }

    public function findOneBy(array $criteria, ?array $orderBy = null): ?object
    {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    public function refresh(object $object): void
    {
        $this->repository->refresh($object);
    }

    public function getBy(array $criteria): object
    {
        /** @var object|null $object */
        $object = $this->findOneBy($criteria);

        if (null === $object) {
            $this->throwNotFoundException();
        }

        return $object;
    }

    public function search(SearchRequest $searchRequest, ?array $filters = null): array
    {
        $orderExpression = $this->getOrderExpression($searchRequest);
        $orderDirection = $this->getOrderDirection($searchRequest);

        $limit = $searchRequest->resultsPerPage ?: self::RESULTS_PER_PAGE;
        $offset = ($searchRequest->page - 1) * $limit;

        $queryBuilder = $this->buildSearchQueryBuilder($searchRequest, $filters)
            ->orderBy($orderExpression, $orderDirection)
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        return $queryBuilder->getQuery()
            ->useQueryCache(true)
            ->getResult();
    }

    public function count(SearchRequest $searchRequest, ?array $filters = null): int
    {
        $queryBuilder = $this->buildSearchQueryBuilder($searchRequest, $filters);

        return (int)$queryBuilder->select('COUNT(root.id)')
            ->getQuery()
            ->useQueryCache(true)
            ->getSingleScalarResult();
    }

    protected function buildQueryBuilder(): QueryBuilder
    {
        return $this->repository->createQueryBuilder('root');
    }

    protected function buildSearchQueryBuilder(SearchRequest $searchRequest, ?array $filters = null): QueryBuilder
    {
        $queryBuilder = $this->buildQueryBuilder();

        if (null !== $searchRequest->search) {
            $this->addSearchClause($queryBuilder, $searchRequest->search);
        }

        if (null === $filters) {
            $filters = $this->prepareFilters($searchRequest);
        }

        if (!empty($filters)) {
            $this->addFilters($queryBuilder, $filters);
        }

        return $queryBuilder;
    }

    protected function prepareFilters(SearchRequest $searchRequest): array
    {
        $filters = [];

        foreach ($this->getFilterableFields() as $fieldName) {
            if (is_array($fieldName)) {
                $property = array_key_first($fieldName);
                $entity = $fieldName[$property];
            } else {
                $property = $fieldName;
                $entity = 'root';
            }

            if (!property_exists($searchRequest, $property)) {
                continue;
            }

            if (null !== $searchRequest->$property) {
                $filters[$entity.'.'.$property] = $searchRequest->$property;
            }
        }

        return $filters;
    }

    protected function addFilters(QueryBuilder $queryBuilder, array $filters): void
    {
        $i = 0;

        foreach ($filters as $fieldName => $value) {
            ++$i;

            $queryBuilder->andWhere(sprintf('%s = :filter_'.$i, $fieldName))
                ->setParameter('filter_'.$i, $value);
        }
    }

    protected function addSearchClause(QueryBuilder $queryBuilder, ?string $search): void
    {
        if (null === $search || '' === trim($search) || empty($this->getSearchableFields())) {
            return;
        }

        $searchFields = [];

        foreach ($this->getSearchableFields() as $fieldName => $fieldType) {
            if ('text' === $fieldType) {
                $searchFields[] = $queryBuilder->expr()->like(
                    sprintf('LOWER(root.%s)', $fieldName),
                    ':searchText'
                );
            }
        }

        $queryBuilder
            ->andWhere($queryBuilder->expr()->orX(...$searchFields))
            ->setParameter('searchText', '%'.strtolower($search).'%');
    }

    protected function getOrderExpression($searchRequest): string
    {
        return 'root.'.($searchRequest->orderProperty ?: 'id');
    }

    protected function getOrderDirection($searchRequest): string
    {
        return $searchRequest->orderDirection ?: 'ASC';
    }

    protected function getSearchableFields(): array
    {
        return [];
    }

    protected function getFilterableFields(): array
    {
        return [];
    }

    protected function throwNotFoundException()
    {
        throw new ResourceNotFoundException();
    }
}
