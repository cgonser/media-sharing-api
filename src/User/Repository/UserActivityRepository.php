<?php

namespace App\User\Repository;

use App\Core\Repository\BaseRepository;
use App\User\Entity\UserActivity;
use Doctrine\Persistence\ManagerRegistry;

class UserActivityRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserActivity::class);
    }
}
