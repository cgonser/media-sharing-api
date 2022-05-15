<?php

namespace App\Notification\Provider;

use App\Core\Provider\AbstractProvider;
use App\Notification\Repository\NotificationRepository;
use Doctrine\ORM\QueryBuilder;

class NotificationProvider extends AbstractProvider
{
    public function __construct(NotificationRepository $repository)
    {
        $this->repository = $repository;
    }

    protected function addFilters(QueryBuilder $queryBuilder, array $filters): void
    {
        if (true === $filters['root.isNew']) {
            $queryBuilder->andWhere('root.readAt IS NULL');

        }

        unset($filters['root.isNew']);

        parent::addFilters($queryBuilder, $filters);
    }

    protected function getFilterableFields(): array
    {
        return [
            'userId',
            'isNew',
        ];
    }
}
