<?php

namespace App\User\Provider;

use App\Core\Provider\AbstractProvider;
use App\User\Entity\User;
use App\User\Exception\UserNotFoundException;
use App\User\Repository\UserRepository;

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

    protected function getFilterableFields(): array
    {
        return [
            'userId',
            'username',
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
