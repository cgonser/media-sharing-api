<?php

namespace App\User\Provider;

use App\Core\Provider\AbstractProvider;
use App\User\Exception\UserActivityNotFoundException;
use App\User\Repository\UserActivityRepository;
use Doctrine\ORM\QueryBuilder;

class UserActivityProvider extends AbstractProvider
{
    public function __construct(UserActivityRepository $repository)
    {
        $this->repository = $repository;
    }

    protected function throwNotFoundException()
    {
        throw new UserActivityNotFoundException();
    }

    protected function addFilters(QueryBuilder $queryBuilder, array $filters): void
    {
        if (isset($filters['startsAt'])) {
            $startsAt = \DateTime::createFromFormat(\DateTimeInterface::ATOM, $filters['startsAt']);

            $queryBuilder->andWhere('root.createdAt >= :startsAt')
                ->setParameter('startsAt', $startsAt);

            unset($filters['startsAt']);
        }

        if (isset($filters['endsAt'])) {
            $endsAt = \DateTime::createFromFormat(\DateTimeInterface::ATOM, $filters['endsAt']);

            $queryBuilder->andWhere('root.createdAt <= :endsAt')
                ->setParameter('endsAt', $endsAt);

            unset($filters['endsAt']);
        }

        parent::addFilters($queryBuilder, $filters);
    }
}
