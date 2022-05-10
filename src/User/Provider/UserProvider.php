<?php

namespace App\User\Provider;

use App\Core\Provider\AbstractProvider;
use App\User\Entity\User;
use App\User\Exception\UserNotFoundException;
use App\User\Repository\UserRepository;
use Doctrine\ORM\QueryBuilder;

class UserProvider extends AbstractProvider
{
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    protected function throwNotFoundException()
    {
        throw new UserNotFoundException();
    }

    public function findOneByUsername(string $username): ?User
    {
        return $this->repository->findOneBy(['username' => strtolower($username)]);
    }

    public function getByUsername(string $username): User
    {
        return $this->findOneByUsername($username) ?? $this->throwNotFoundException();
    }

    public function findOneByEmail(string $emailAddress): ?User
    {
        return $this->repository->findOneBy(['email' => strtolower($emailAddress)]);
    }

    protected function addFilters(QueryBuilder $queryBuilder, array $filters): void
    {
        if (!empty($filters['root.exclusions'])) {
            $queryBuilder->andWhere('root.id NOT IN (:exclusions)')
                ->setParameter('exclusions', $filters['root.exclusions']);
        }

        unset($filters['root.exclusions']);

        parent::addFilters($queryBuilder, $filters);
    }

    protected function getFilterableFields(): array
    {
        return [
            'userId',
            'username',
            'exclusions',
        ];
    }

    protected function getSearchableFields(): array
    {
        return [
            'username' => 'text',
            'bio' => 'text',
        ];
    }
}
