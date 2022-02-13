<?php

namespace App\User\Repository;

use App\Core\Repository\BaseRepository;
use App\User\Entity\UserIntegration;
use Doctrine\Persistence\ManagerRegistry;

class UserIntegrationRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserIntegration::class);
    }
}
