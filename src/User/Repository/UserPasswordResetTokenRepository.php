<?php

namespace App\User\Repository;

use App\Core\Repository\BaseRepository;
use App\User\Entity\UserPasswordResetToken;
use Doctrine\Persistence\ManagerRegistry;

class UserPasswordResetTokenRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserPasswordResetToken::class);
    }
}
