<?php

namespace App\Notification\Repository;

use App\Core\Repository\BaseRepository;
use App\Notification\Entity\UserNotificationChannel;
use Doctrine\Persistence\ManagerRegistry;

class UserNotificationChannelRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserNotificationChannel::class);
    }
}
