<?php

namespace App\Notification\Repository;

use App\Core\Repository\BaseRepository;
use App\Notification\Entity\Notification;
use Doctrine\Persistence\ManagerRegistry;

class NotificationRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }
}
