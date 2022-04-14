<?php

namespace App\User\Repository;

use App\Core\Repository\BaseRepository;
use App\User\Entity\UserFollow;
use Doctrine\Persistence\ManagerRegistry;

class UserFollowRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserFollow::class);
    }
}
