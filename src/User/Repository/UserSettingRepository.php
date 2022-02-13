<?php

namespace App\User\Repository;

use App\Core\Repository\BaseRepository;
use App\User\Entity\UserSetting;
use Doctrine\Persistence\ManagerRegistry;

class UserSettingRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserSetting::class);
    }
}
