<?php

namespace App\User\Repository;

use App\Core\Repository\BaseRepository;
use App\User\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

class UserRepository extends BaseRepository implements UserLoaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function loadUserByIdentifier(string $identifier): ?User
    {
        return $this->findOneBy([
            'email' => strtolower($identifier),
        ]);
    }

    public function loadUserByUsername(string $username): ?User
    {
        return $this->loadUserByIdentifier($username);
    }
}
